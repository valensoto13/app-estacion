<?php 

	/**
	* @file User.php
	* @brief Declaraciones de la clase User para la conexión con la base de datos.
	* @author Matias Leonardo Baez
	* @date 2024
	* @contact elmattprofe@gmail.com
	*/

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	/*< incluye la clase Mailer.php para enviar correo electrónico*/

	// se crea la clase User que hereda de DBAbstract
	class User extends DBAbstract{

		private $nameOfFields = array();

		/**
		 * 
		 * @brief Es el constructor de la clase User
		 * 
		 * Al momento de instanciar User se llama al padre para que ejecute su constructor
		 * 
		 * */
		function __construct(){
		
			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();

			/**< Obtiene la estructura de la tabla */
			$result = $this->query('DESCRIBE appestacion_users');

			foreach ($result as $key => $row) {
				$buff =$row["Field"];
				/**< Almacena los nombres de los campos*/
				$this->nameOfFields[] = $buff;
				/**< Autocarga de atributos a la clase */
				$this->$buff=NULL;
			}
			

		}

		/**
		 * 
		 * Hace soft delete del registro
		 * @return bool siempre verdadero
		 * 
		 * */
		function leaveOut(){

			$id = $this->id;
			$fecha_hora = date("Y-m-d H:i:s");

			$ssql = "UPDATE users SET delete_at='$fecha_hora' WHERE id=$id";

			$this->query($ssql);

			return true;
		}

		/**
		 * 
		 * Finaliza la sesión
		 * @return bool true
		 * 
		 * */
		function logout(){
			return true;
		}

		function token_action_veri($token){
        $sql = "SELECT `token_action`, `email` FROM appestacion_users WHERE activo = '0'";
        $response = $this->query($sql);
      
        if ($response->num_rows > 0) {
            $rows = $response->fetch_all(MYSQLI_ASSOC);

            foreach ($rows as $row) {

                $email = $row["email"];
                $tok = trim($row["token_action"]);

                if ($token == $tok) {
                    $sql = "UPDATE `usu` SET `activo` = '1', `active_date` = NOW(), `token_action` = NULL WHERE `usu`.`email` = '$email'";
                    $response = $this->query($sql);

                    return $email;
                }
            }
            return false;
        } else {
           
            return false;
        }
    }



		/**
		 * 
		 * Verifica el token enviado al email para validar al usuario
		 * @brief valida el token email
		 * @param array $form [token]
		 * @return array [error, errno]
		 * 
		 * */

		function verify($form){

			/*< recupera el token del array*/
			$token = $form["token"];

			/*< consulta para buscar el usuario por medio de su token*/
			$ssql = "SELECT * FROM `appestacion_users` WHERE email_token = '$token'; ";

			/*< ejecuta la consulta*/
			$response = $this->query($ssql)->fetch_all(MYSQLI_ASSOC);

			/*< si se encontro el usuario*/
			if(count($response)>0){
 
 				/*< activa el usuario y borra el token email*/
				$ssql = "UPDATE appestacion_users SET active = '1' WHERE token = '$token'";

				/*< ejecuta la consulta*/
				$this->query($ssql);

				/*< parámetros para hacer login, como la contraseña esta cifrada le avisamos al login que lo está*/
				$form = ["txt_email" => $response[0]["email"], "txt_contraseña" => $response[0]["pass"], "cifrado" => 1];

				/*< ejecuta el login */
				$this->login($form);

				/*< mensaje de validación exitosa*/
				return ["errno" => 200, "error" => "Se valido el email"];

			}

			/*< el token no existe o no está relacionado a ningún usuario*/
			return ["errno" => 404, "error" => "El token es invalido"];

		}

		/**
		 * 
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		 * */
		function login($form){

			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< recupera el email del formulario*/
			if (!isset($form["txt_email"])) {
				return  ["error" => "Email no definido", "errno" => 402];
			}
			$email = $form["txt_email"];

			/*< consultamos si existe el email*/
			$result = $this->query("CALL `login2`('$email')");
			// el email no existe
			if(count($result)==0){
				return ["error" => "Email no registrado", "errno" => 404];
			}
			/*< seleccionamos solo la primer fila de la matriz*/
			$result = $result[0];
			
			if ($result["active"]=="0") {
				return ["error" => "Su usuario aún no se ha validado, revise su casilla de correo", "errno" => 404];

			}
			if ($result["blocked"]=="1") {
				return ["error" => "Su usuario está bloqueado, revise su casilla de correo", "errno" => 404];

			}
			if ($result["recovery"]=="1") {
				return ["error" => "Su usuario está bloqueado, revise su casilla de correo", "errno" => 404];

			}

			// si el email existe y la contraseña es valida
			if($result["password"]==md5($form["txt_pass"].$_ENV['PROJECT_NAME'])){

				/**< autocarga de valores en los atributos de la clase */
				foreach ($this->nameOfFields as $key => $value) {
					$this->$value = $result[$value];
				}

				// para que los avatares sean gatitos

				/*< carga la clase en la sesión*/
				$_SESSION["app-estacion"]['user'] = $this;

				/*< usuario valido*/
				return ["error" => "Acceso valido", "errno" => 200];
			}
			$correo = new Mailer();
			$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

// Determinar el sistema operativo
$os = 'Desconocido';
if (preg_match('/linux/i', $browser)) {
    $os = 'Linux';
} elseif (preg_match('/macintosh|mac os x/i', $browser)) {
    $os = 'Mac OS X';
} elseif (preg_match('/windows|win32/i', $browser)) {
    $os = 'Windows';
}

// URL para bloquear la cuenta
$blockUrl = 'https://mattprofe.com.ar/alumno/6846/app-estacion/blocked?token='.$this->new_token($email);;

$cuerpo_email = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Inicio de Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #d9534f;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Información de Inicio de Sesión</h1>
        <p>Hemos detectado un inicio de sesión en tu cuenta con la siguiente información:</p>
        <table>
            <tr>
                <th>IP</th>
                <td>{$ip}</td>
            </tr>
            <tr>
                <th>Sistema Operativo</th>
                <td>{$os}</td>
            </tr>
            <tr>
                <th>Navegador Web</th>
                <td>{$browser}</td>
            </tr>
        </table>
        <p>Si no fuiste tú quien inició sesión, por favor, bloquea tu cuenta inmediatamente haciendo clic en el siguiente botón:</p>
        <a href="{$blockUrl}" class="button">No fui yo, bloquear cuenta</a>
    </div>
</body>
</html>
HTML;

				/*< envia el correo electrónico de validación*/
				$correo->send(["destinatario" => $email, "motivo" => "Intento de inicio de sesion", "contenido" => $cuerpo_email] );
			return ["error" => "Error de contraseña", "errno" => 405];

		}

		/**
		 * 
		 * Agrega un nuevo usuario si no existe el correo electronico en la tabla users
		 * @param array $form es un arreglo assoc con los datos del formulario
		 * @return array que posee códigos de error especiales 
		 * 
		 * */
		function register($form){
			
			/*< recupera el email*/
			$email = $form["txt_email"];

			/*< consulta si el email ya esta en la tabla de usuarios*/
			$result = $this->query("SELECT * FROM appestacion_users WHERE email = '$email'");

			if ($form["txt_pass"]!=$form["txt_pass_repeat"]) {
				return ["error" => "Las contraseñas no coinciden", "errno" => 400];
			}
			// el email no existe entonces se registra
			if(count($result)==0){

				/*< encripta la contraseña*/
				$pass = md5($form["txt_pass"].$_ENV['PROJECT_NAME']);

				/*< se crea el token único para validar el correo electrónico*/
				$token_email = md5($_ENV['PROJECT_WEB_TOKEN'].$email);
				$token = md5($_ENV['PROJECT_WEB_TOKEN'].$email.$_ENV['PROJECT_NAME']);

				/*< agrega el nuevo usuario y deja en pendiente de validar su email*/
				$ssql = "INSERT INTO appestacion_users (token,email, password, token_action,add_date) VALUES ('$token','$email','$pass', '$token_email',NOW())";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< se recupera el id del nuevo usuario*/
				$this->id = $this->db->insert_id;

				/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
				$correo = new Mailer();

				/*< plantilla de email para validar cuenta*/
				$cuerpo_email = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo Electrónico</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <h2>¡Bienvenido a nuestra plataforma!</h2>
        <p>Para activar tu usuario, haz clic en el siguiente botón:</p>
        <p style="text-align: center;">
            <a href="https://mattprofe.com.ar/alumno/6846/app-estacion/verify?token='.$token_email.'" style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Click aquí para activar tu usuario</a>
        </p>
    
        <p>Gracias por unirte a nuestra plataforma.</p>
        <p>Equipo de soporte</p>
    </div>

</body>
</html>
';

				/*< envia el correo electrónico de validación*/
				$correo->send(["destinatario" => $email, "motivo" => "Confirmación de registro", "contenido" => $cuerpo_email] );


				/*< aviso de registro exitoso*/
				return ["error" => "Usuario registrado", "errno" => 200];
			}
			if(isset($result['delete_date'])){

				/*< recupera el id del usuario que quiere volver a nuestra app*/
				$id=$result["id"];
				$this->id = $result["id"];

				/*< encripta la nueva contraseña*/
				$pass = md5($form["txt_pass"]."app-estacion");

				/*< consulta para volver a activar el usuario que se había ido*/
				$ssql = "UPDATE appestacion_users SET first_name='', last_name='', `password`='$pass', delete_at='0000-00-00 00:00:00' WHERE id=$id";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< mensaje de usuario volvio a la app*/
				return ["error" => "Usuario que abandono volvio a la app", "errno" => 201];
			}

			// si el email existe 
			return ["error" => "Correo ya registrado", "errno" => 405];

		}


		/**
		 * 
		 * Actualiza los datos del usuario con los datos de un formulario
		 * @param array $form es un arregle asociativo con los datos a actualizar
		 * @return array arreglo con el código de error y descripción
		 * 
		 * */
		function update($form){
			$token = $form["token"];

			$ssql = "UPDATE appestacion_users SET active=1,token_action='',active_date=NOW() WHERE token_action='$token'";

			$result = $this->query($ssql);

			return ["error" => "Se actualizo correctamente", "errno" => 200];
		}

		function update_block($form){
			$token = $form["token"];

			$ssql = "UPDATE appestacion_users SET blocked=1,token_action='',blocked_date=NOW() WHERE token_action='$token'";

			$result = $this->query($ssql);

			return ["error" => "Se actualizo correctamente", "errno" => 200];
		}

		function new_token($email){
			$token = md5($email.$_ENV['PROJECT_NAME']);

			$ssql = "UPDATE appestacion_users SET token_action='$token' WHERE email='$email'";

			$result = $this->query($ssql);

			return $token;
		}

		function recovery($form){
			$email = $form["txt_email"];

			$ssql = "SELECT * FROM appestacion_users WHERE email='$email'";
			$result = $this->query($ssql);
			if ($result == null) {
				return ["error" => "Email no registrado", "errno" => 404];
			}
			$new = $this->new_token($email);
			$correo = new Mailer();

			$cuerpo_email = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recuperación de Cuenta</h1>
        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Si no solicitaste este cambio, puedes ignorar este correo.</p>
        <p>Para restablecer tu contraseña, por favor haz clic en el siguiente botón:</p>
        <a href="https://mattprofe.com.ar/alumno/6846/app-estacion/reset?token='.$new.'" class="button">Click aquí para restablecer contraseña</a>
    </div>
</body>
</html>
';
			$correo->send(["destinatario" => $email, "motivo" => "Recuperacion de cuenta", "contenido" => $cuerpo_email] );

			$ssql = "UPDATE appestacion_users SET recovery=1,recover_date=NOW() WHERE email='$email'";
			$result = $this->query($ssql);

			return ["error" => "Email de recuperacion enviado revise su casilla", "errno" => 200];
		}
		function reset($form){
			$pass = $form["txt_pass"];
			$passr = $form["txt_pass_repeat"];

			$token = $form["token"];
			if ($pass!=$passr) {
				return ["error" => "Las contraseñas no coinciden", "errno" => 420];
			}
			$pass = md5($form["txt_pass"].$_ENV['PROJECT_NAME']);
			$ssql = "SELECT * FROM appestacion_users WHERE token_action='$token'";
			$result = $this->query($ssql);
			$email = $result[0]['email'];
			$ssql = "UPDATE appestacion_users SET recovery=0,blocked=0,recover_date=NOW(),password='$pass' WHERE token_action='$token'";
			$result = $this->query($ssql);

			$correo = new Mailer();
			$ip = $_SERVER['REMOTE_ADDR'];
			$browser = $_SERVER['HTTP_USER_AGENT'];
			// Determinar el sistema operativo
			$os = 'Desconocido';
			if (preg_match('/linux/i', $browser)) {
			    $os = 'Linux';
			} elseif (preg_match('/macintosh|mac os x/i', $browser)) {
			    $os = 'Mac OS X';
			} elseif (preg_match('/windows|win32/i', $browser)) {
			    $os = 'Windows';
			}
			$new = $this->new_token($email);
			// URL para bloquear la cuenta
			$blockUrl = 'https://mattprofe.com.ar/alumno/6846/app-estacion/blocked?token='.$new;
$cuerpo_email = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Inicio de Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #d9534f;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Información de Recuperación de cuenta</h1>
        <p>Hemos detectado un cambio de contraseña en tu cuenta con la siguiente información:</p>
        <table>
            <tr>
                <th>IP</th>
                <td>{$ip}</td>
            </tr>
            <tr>
                <th>Sistema Operativo</th>
                <td>{$os}</td>
            </tr>
            <tr>
                <th>Navegador Web</th>
                <td>{$browser}</td>
            </tr>
        </table>
        <p>Si no fuiste tú quien cambio la contraseña, por favor, bloquea tu cuenta inmediatamente haciendo clic en el siguiente botón:</p>
        <a href="{$blockUrl}" class="button">No fui yo, bloquear cuenta</a>
    </div>
</body>
</html>
HTML;

			
			$correo->send(["destinatario" => $email, "motivo" => "Recuperacion de cuenta", "contenido" => $cuerpo_email] );
			return ["error" => "Contraseña cambiada, <a href='login'>loguearse</a>", "errno" => 200];
		}
		/**
		 * 
		 * Cantidad de usuarios registrados
		 * @return int cantidad de usuarios registrados
		 * 
		 * */
		function getCantUsers(){

			$result = $this->query("SELECT * FROM users");

			return $this->db->affected_rows;
		}


		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function getAllUsers($request){

			$request_method = $_SERVER["REQUEST_METHOD"];

			/*< Es el método correcto en HTTP?*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< Solo un usuario logueado puede ver el listado */
			if(!isset($_SESSION["app-estacion"])){
				return ["errno" => 411, "error" => "Para usar este método debe estar logueado"];
			}

			/*

			if(!isset($_SESSION["morphyx"]['user_level'])){

				if($_SESSION["morphyx"]['user_level']!='admin'){
				return ["errno" => 412, "error" => "Solo el 	administrador puede utilizar el metodo"];
				}
			}

			*/


			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

			$result = $this->query("SELECT * FROM users LIMIT $inicio, $cantidad");

			return $result;
		}


	}

	
<?php

	include_once 'env.php';


	include 'lib/mp-mailer/Mailer/src/PHPMailer.php';
	include 'lib/mp-mailer/Mailer/src/SMTP.php';
	include 'lib/mp-mailer/Mailer/src/Exception.php';


	// incluimos a User para poder hacer uso de la variable cargada en session
	include_once 'models/User.php';

	// Inicia la sesión
	session_start();

	// motor de plantillas
	include 'lib/Pork/Pork.php';  	


    // por defecto seccion es register
    $seccion = "landing";

    // si existe slug entonces la sección es su contenido
    if (isset($_GET['slug'])) { 
        if (!empty($_GET['slug'])) { 
            $seccion = $_GET['slug']; 
        }
    } 


    $controllerPath = 'controllers/'.$seccion.'Controller.php';
    // verificamos que exista el controlador
    if(!file_exists($controllerPath)){
        // si no existe el controlador lo llevamos al controlador de error 404
        $seccion = "error404";
        $controllerPath = 'controllers/'.$seccion.'Controller.php';
    }

	//=== firewall

	// Listas de acceso dependiendo del estado del usuario
	$seccion_login = ["panel", "logout", "abandonar", "detalle"];
	$seccion_anonimo = ["landing", "login", "register", "reset", "recovery", "verify"];


	// sesión iniciada
	if(isset($_SESSION['app-estacion'])){
		
		// recorre la lista de secciones no permitidas
		foreach ($seccion_anonimo as $key => $value) {
			// si está solicitando una sección no permitida
			if($seccion == $value){
				$seccion = "panel";
				break;
			}
		}

	}else{ // sesión no iniciada

		// recorre la lista de secciones no permitidas
		foreach ($seccion_login as $key => $value) {
			// si está solicitando una sección no permitida
			if($seccion == $value){
				$seccion = "landing";
				break;
			}
		}

		

	}

	// === fin firewall


	/*echo "Sección solicitada: " . $seccion . "<br>"; 
	echo "Archivo de controlador: controllers/{$seccion}Controller.php" . "<br>"; // Añade un mensaje si el controlador existe o no 
	if (file_exists('controllers/'.$seccion.'Controller.php')) { 
		echo "El controlador existe.<br>"; 
	} else { 
		echo "El controlador NO existe.<br>"; 
	}*/

	include 'controllers/'.$seccion.'Controller.php';

?>





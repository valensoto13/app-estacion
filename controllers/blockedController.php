<?php 
	
	/*< Captura de de la url el valor de la variable token*/
	$user = new User();
	$user->update_block(["token"=>explode("=", $_SERVER["REQUEST_URI"])[1]]);
	
	header("location:login");

 ?>
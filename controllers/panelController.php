<?php 

	// se muestra el contenido de SESSION (para debug)
	$usuario = $_SESSION["app-estacion"]["user"];

	// crea el objeto con la vista
	$tpl = new Pork("panel");

	// carga la vista
	$tpl->loadTPL();

	// imprime en la vista en la página
	$tpl->printTPL();

 ?>
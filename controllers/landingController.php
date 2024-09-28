<?php



	// crea el objeto con la vista
	$tpl = new Pork("landing");


	// array para pasar variables a la vista
	$logued = (!isset($_SESSION['kiwi']))?"display_none":"";
	$nologued = (isset($_SESSION['kiwi']))?"display_none":"";

	// reemplaza las variables en la vista
	$tpl->setVarsTPL(["LOGUED"=>$logued,"NOLOGUED"=>$nologued]);

	// imprime en la página la vista
	$tpl->printTPL();

 ?>
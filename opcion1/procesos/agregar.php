<?php 
	require_once "../clases/conexion.php";
	require_once "../clases/crud.php";
	$obj= new crud();

	$datos=array(
		$_POST['horaIngreso'],
		$_POST['horaSalida'],
		$_POST['cedula']
				);

	echo $obj->agregar($datos);
	

 ?>
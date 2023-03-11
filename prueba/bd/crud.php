<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recepción de los datos enviados mediante POST desde el JS   
$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$apellido1 = (isset($_POST['apellido1'])) ? $_POST['apellido1'] : '';
$apellido2 = (isset($_POST['apellido2'])) ? $_POST['apellido2'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$newid = (isset($_POST['newid'])) ? $_POST['newid'] : '';
$pass1 = (isset($_POST['pass1'])) ? $_POST['pass1'] : '';
$pass2 = (isset($_POST['pass2'])) ? $_POST['pass2'] : '';
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : '';
$correo = (isset($_POST['correo'])) ? $_POST['correo'] : '';
$pass = password_hash($pass2,PASSWORD_DEFAULT);

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO usuarios (cedula, nombre, apellido1, apellido2, pass, telefono, correo) VALUES('$newid', '$nombre', '$apellido1', '$apellido2', '$pass', '$telefono', '$correo') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();

        $consulta = "SELECT cedula, nombre, apellido1, apellido2, telefono, correo FROM usuarios where cedula = '$newid' ORDER BY cedula DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //modificación
        if ($pass1=="") {
            $consulta = "UPDATE usuarios SET cedula='$newid', nombre='$nombre', apellido1='$apellido1', apellido2='$apellido2', telefono='$telefono', correo='$correo' WHERE cedula='$id'";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            
            $consulta = "SELECT cedula, nombre, apellido1, apellido2, telefono, correo FROM usuarios WHERE cedula='$newid'";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
            break;   
        } else {
            $consulta = "UPDATE usuarios SET cedula='$newid', nombre='$nombre', apellido1='$apellido1', apellido2='$apellido2', pass='$pass', telefono='$telefono', correo='$correo' WHERE cedula='$id'";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            
            $consulta = "SELECT cedula, nombre, apellido1, apellido2, telefono, correo FROM usuarios WHERE cedula='$newid'";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
            break;   
        }
    case 3://baja
        $consulta = "DELETE FROM usuarios WHERE cedula='$id'";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);                          
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;


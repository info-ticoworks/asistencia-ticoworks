<?php
console.log("Carga de CRUD...");
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recepción de los datos enviados mediante POST desde el JS
console.log("Recepción de datos");
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$apellido1 = (isset($_POST['apellido1'])) ? $_POST['apellido1'] : '';
$apellido2 = (isset($_POST['apellido2'])) ? $_POST['apellido2'] : '';
$pass1 = (isset($_POST['pass1'])) ? $_POST['pass2'] : '';
$pass2 = (isset($_POST['pass2'])) ? $_POST['pass2'] : '';
if ($pass1 == $pass2) {
    $hashed = password_hash($pass1,PASSWORD_DEFAULT);
} else if ($pass1 <> $pass2) {
    echo "<script>
    Swal.fire({
        icon: 'error',
        title: 'Lo sentimos...!',
        text: 'Las contraseñas no coinciden..!',  
    })
    </script>";
}
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : '';
$correo = (isset($_POST['correo'])) ? $_POST['correo'] : '';
$listtipoUsuario = (isset($_POST['tipoUsuario'])) ? $_POST['tipoUsuario'] : '';
if ($listtipoUsuario == "Operario") {
    $idtipoUsuario = 1;
} else if ($listtipoUsuario == "Supervisor") {
    $idtipoUsuario = 2;
} else if ($listtipoUsuario == "Gerente") {
    $idtipoUsuario = 3;
} else if ($listtipoUsuario == "Administrador") {
    $idtipoUsuario = 4;
}
if(isset($_POST['wsNotif'])) {
    $wsNotifc = 1;
} else {
    $wsNotifc = 0;
};
echo $id;

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO usuarios (cedula, nombre, apellido1, apellido2, pass, telefono, correo, idTipoUsuario, wsNotif) VALUES('$id', '$nombre', '$apellido1', '$apellido2', '$hashed', '$telefono', '$correo', '$idtipoUsuario', '$wsNotif')";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();

        $consulta = "SELECT cedula, nombre, apellido1, apellido2, telefono, correo, nombreTipoUsuario, wsNotif FROM usuarios inner join tipoUsuario on usuarios.idTipoUsuario=tipoUsuario.idTipoUsuario ORDER BY cedula DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        echo '<script>console.log("TicoWorks says: Paso 3")</script>';
        echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Felicidades...!',
            text: 'Usuario creado con éxito..!',
        })
        </script>";
        break;
    case 2: //modificación
        $consulta = "UPDATE usuarios SET cedula='$id', nombre='$nombre', apellido1='$apellido1', apellido2='$apellido2', pass='$hashed', telefono='$telefono', correo='$correo', idTipoUsuario='$idtipoUsuario', wsNotif='$wsNotif' WHERE cedula='$id'";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT cedula, nombre, apellido1, apellido2, telefono, correo, nombreTipoUsuario, wsNotif FROM usuarios inner join tipoUsuario on usuarios.idTipoUsuario=tipoUsuario.idTipoUsuario WHERE cedula='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;     
    case 3://baja
        console.log("Borrado, Paso 2");
        $consulta = "DELETE FROM usuarios WHERE cedula='$id'";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);                       
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;


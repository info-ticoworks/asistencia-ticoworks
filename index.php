<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UFT-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"&amp;gt;>
        <title>Página Principal</title>
        <link rel="stylesheet"  type="text/css" href="./css/main.css">
        <link rel="stylesheet"  type="text/css" href="./icons/fonts.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.css">
        <link rel="icon" href="">
        <script src="https://maps.googleapis.com/maps/api/js?key-127.0.0.1&callback-initMap" async defer></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>
    <header id="header">
        <script src="js/funcion.js"></script>
    </header>
    
    <body>   
            <img class="tw-logo" src="./image/logo1.png">
            <form action="index.php" class="form-box" method="POST">
            <h3 class="form-title">Sistema de Asistencia Laboral</h3>
            <h3 class="form-sub-title">Te damos la bienvenida!</h3>
            <h3 class="form-sub-title">Inicia Sesion y registra tu asistencia.</h3>
            <input type="text" placeholder="Cedula" name="ced" id="ced" autofocus>
            <input type="password" placeholder="Contraseña" name="passc" id="passc">
            <div>
               <!-- <input type="checkbox" name="00">
                <p>¿Mantener sesion iniciada?</p>--> 
            </div>

            <input type="submit" value="Ingresar" name="btverificar" id="btverificar">
            <p><a class="volver" href="./cerrar.php">Cerrar Sesion</a></p>
            <?php
            //header("Refresh:0");
            if (isset($_POST['btverificar'])) {
                require './config.php';
                $cedula = $_POST['ced'];
                $pass = $_POST['passc'];
                //$q = "SELECT * FROM usuarios where cedula = '$cedula'";
                //$consulta1 = mysqli_query($conexion, $q);
                //if(mysqli_num_rows($consulta1) == 1) {
                    //$row = mysqli_fetch_array($consulta1);
                    //$hashed_pass = $row ['pass'];
                    //$verify = password_verify($pass, $hashed_pass);

                    //if($verify){
                        //echo 'Password Verified!';
                    //}else{
                        //echo 'Incorrect Password!';
                    //}
                //}

                $q = "SELECT * FROM usuarios where cedula = '$cedula'";
                $consulta = mysqli_query($conexion, $q);
                //$array = mysqli_fetch_array($consulta);
                if(mysqli_num_rows($consulta) == 1) {
                    $row = mysqli_fetch_array($consulta);
                    $hashed_pass = $row ['pass'];
                    $idTipoUsuario = $row ['idTipoUsuario'];
                    $ced = $row ['cedula'];
                    $_SESSION['cedula'] = $row ['cedula'];
                    $_SESSION['nombre'] = $row ['nombre'];
                    $_SESSION['apellido1'] = $row ['apellido1'];
                    $_SESSION['telefono'] = $row ['telefono'];
                    $_SESSION['correo'] = $row ['correo'];
                    $_SESSION['idTipoUsuario'] = $row ['idTipoUsuario'];
                    $_SESSION['wsNotif'] = $row ['wsNotif'];
                    $verify = password_verify($pass, $hashed_pass);
                    if($verify){
                        if($idTipoUsuario == "1" || $idTipoUsuario == "2" || $idTipoUsuario == "3"){
                            header("Location: frmHora.php");
                        }else if($idTipoUsuario == "4"){
                            header("Location: frmAdmin.php");
                        }else{
                            echo "<script>
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...!',
                            text: 'Acceso no autorizado al sistema..!',  
                            })
                            </script>";
                        }
                        //echo 'Password Verified!';
                    }else{
                        echo "<script>
                        Swal.fire({
                        icon: 'warning',
                        title: 'Lo sentimos...!',
                        text: 'La contraseña es incorrecta.',  
                        })
                        </script>";
                        //echo 'Incorrect Password!';
                    }




                } else {
                    if(empty($_POST['ced']) || empty($_POST['passc'])){
                        echo "<script>
                        Swal.fire({
                        icon: 'warning',
                        title: 'Lo sentimos...!',
                        text: 'Debe digitar todos los Datos.',  
                        })
                        </script>";
                    }else{
                        echo "<script>
                        Swal.fire({
                        icon: 'error',
                        title: 'Lo sentimos...!',
                        text: 'Datos incorrectos.',  
                        })
                        </script>";
                    }
                  }
                }
            ?>
        </form>
    </body>
</html>
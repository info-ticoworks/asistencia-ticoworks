<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UFT-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Página Principal</title>
        <link rel="icon" type="image/x-icon" href="./image/favicon.ico">
        <link rel="stylesheet" href="./css/main.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>
    <body>   
        <img class="tw-logo" src="./image/logo1.png">
        <form action="index.php" class="form-box" method="POST">
        <h3 class="form-sub-title">Te damos la bienvenida!</h3>
        <h3 class="form-sub-title">Inicia Sesion y registra tu asistencia.</h3>
        <input type="text" placeholder="Cédula" name="ced" id="ced" autofocus>
        <input type="password" placeholder="Contraseña" name="passc" id="passc">
        <input type="submit" value="Ingresar" name="btverificar" id="btverificar">
            <?php
            //header("Refresh:0");
            if (isset($_POST['btverificar'])) {
                require './config.php';
                $cedula = $_POST['ced'];
                $pass = $_POST['passc'];
                $q = "SELECT * FROM usuarios
                    inner join empresa on usuarios.idEmpresa=empresa.idempresa
                    where cedula = '$cedula'";
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
                    $_SESSION['mailNotif'] = $row ['mailNotif'];
                    $_SESSION['wsNotif'] = $row ['wsNotif'];
                    $_SESSION['idEmpresa'] = $row ['idEmpresa'];
                    $_SESSION['nombreEmpresa'] = $row ['nombreEmpresa'];
                    $verify = password_verify($pass, $hashed_pass);
                    if($verify){
                        if($idTipoUsuario == "1" || $idTipoUsuario == "2" || $idTipoUsuario == "3"){
                            header("Location: frmHora.php");
                        }else if($idTipoUsuario == "5"){
                            header("Location: ./admin/");
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
    <footer>
        <div class="footer">
            Developed by: Ticoworks. 2016-2023
        </div>
        <div class="footer-padding"></div>
    </footer>



</html>

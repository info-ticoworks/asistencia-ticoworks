<?php
session_start();
include './config.php';
$cedula = $_SESSION['cedula'];
$nombre = $_SESSION['nombre'];
$apellido1 = $_SESSION['apellido1'];
$telefono = $_SESSION['telefono'];
$correo = $_SESSION['correo'];
$idTipoUsuario = $_SESSION['idTipoUsuario'];
$mailNotif = $_SESSION['mailNotif'];
$wsNotif = $_SESSION['wsNotif'];
$idEmpresa = $_SESSION['idEmpresa'];
$nombreEmpresa = $_SESSION['nombreEmpresa'];
date_default_timezone_set('America/Costa_Rica');
$bMeses = array("void","Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$bDias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
$fecha = getdate();
$dias = $bDias[$fecha["wday"]];
$meses = $bMeses[$fecha["mon"]];
$fechaActual = "$dias ".$fecha["mday"]." de ".$meses." de ".$fecha["year"]."";
$horaActual = "".$fecha["hours"].":".$fecha["minutes"].":".$fecha["seconds"]."";
if($idTipoUsuario<>5){
    // header ("Location: rediriges a la pagina de logueo".)
    header("Location: ./index.php");
}
?>
<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="UFT-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Registrar Usuario</title>
        <link rel="icon" type="image/x-icon" href="./image/favicon.ico">
        <link rel="stylesheet" href="./css/main.css">
        <link rel="stylesheet" href="./icons/fonts.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>
    <header id="header">
        <nav class="menu">
            <div class="logo-box">
            </div>
            <div class="logo-box">
                <span class="btn-menu"><i class="fas fa-bars"></i></span>
            </div>
            <div class="list-container">        
                <ul class="lists">
                    <li><a href="frmAdmin.php"><span class="icon-user-plus"></span>Registrar Hora</a></li>
                    <li><a href="cerrar.php"><span class="icon-user-plus"></span>Cerrar Sesión</a></li>
                </ul>
            </div>
        </nav>
        <script src="js/funcion.js"></script>
    </header>
    <body>
        <img class="tw-logo" src="./image/logo1.png">
        <form action="frmRegistro.php" class="form-box" method ="POST"> 
            <h3 class="form-sub-title">Empresa: <?php echo $nombreEmpresa?></h3>
            <h3 class="form-sub-title">Fecha: <?php echo $fechaActual?> </h3>
            <h3 class="form-sub-title">Registrar nuevo usuario</h3>
            <input type="number" placeholder="Cédula*" name="ced" id="ced" required>
            <input type="text" placeholder="Nombre*" name="nombre" id="nombre" required>
            <input type="text" placeholder="Primer apellido*" name="apellido1" id="apellido1" required>
            <input type="text" placeholder="Segundo apellido*" name="apellido2" id="apellido2" required>
            <input type="password" placeholder="Password*" name="pass1" id="pass1" minlength="8" required>
            <input type="password" placeholder="Repetir password*" name="pass2" id="pass2" required>
            <input type="tel" pattern="[0-9]{8}" placeholder="Teléfono*" name="telefono" id="telefono" required>
            <input type="email" pattern="[^@\s]+@[^@\s]+.[^@\s]" placeholder="E-mail*" name="correo" id="correo" required>
            <select class="select" id="tipoUsuario" name="tipoUsuario">
                <option selected disabled>Tipo de Usuario*</option>
                <option name="operario">Operario</option>
                <option name="supervisor">Supervisor</option>
                <option name="gerente">Gerente</option>
                <option name="administrador">Administrador</option>
            </select>
            <div class="notifCheck">
                <input type="checkbox" class="Notif" name="wsNotif" value="wsNotif">
                <label for="Notif">Recibir notificaciones por WhatsApp</label>
            </div>
            <div>
                <!-- <p> ¿Acepta todos los terminos? </p>
                <input type="checkbox" name="00"> -->
            </div>
            <input type="submit" value="Registrar" name="btverificarc" id="btverificarc">
            <input type="submit" value="Cerrar Sesión" name="btSalir" id="btSalir">
            <?php
            require './class/Registro.php';
            try {
                $Registro = new Registro();
                if (isset($_POST['btverificarc'])) {
                    if (empty($_POST['ced'])
                    || empty($_POST['nombre']) 
                    || empty($_POST['apellido1']) 
                    || empty($_POST['apellido2']) 
                    || empty($_POST['pass1']) 
                    || empty($_POST['pass2']) 
                    || empty($_POST['telefono']) 
                    || empty($_POST['correo']) 
                    || empty($_POST['tipoUsuario'])) {
                        echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error...!',
                            text: 'Debe digitar todos los Datos.',  
                        })
                        </script>";
                    } else if (strlen(($_POST['pass1'])) < 8) {
                        echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error...!',
                            text: 'La contraseña debe tener un minimo de 8 caracteres..!',  
                        })
                        </script>";
                    } else if ($_POST['pass1'] <> $_POST['pass2']) {
                        echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Lo sentimos...!',
                            text: 'Las contraseñas no coinciden..!',  
                        })
                        </script>";
                    } else {
                        $usuarioc = $_POST['nombre'];
                        $ced = $_POST['ced'];
                        $apellido1c = $_POST['apellido1'];
                        $apellido2c = $_POST['apellido2'];
                        $hash = password_hash($_POST['pass2'],PASSWORD_DEFAULT);
                        $passc = $hash;
                        $telefonoc = $_POST['telefono'];
                        $correoc = $_POST['correo'];
                        $listtipoUsuario = $_POST['tipoUsuario'];
                        if ($listtipoUsuario == "Operario") {
                            $tipoUsuarioc = 1;
                        } else if ($listtipoUsuario == "Supervisor") {
                            $tipoUsuarioc = 2;
                        } else if ($listtipoUsuario == "Gerente") {
                            $tipoUsuarioc = 3;
                        } else if ($listtipoUsuario == "Administrador") {
                            $tipoUsuarioc = 4;
                        };
                        if(isset($_POST['wsNotif'])) {
                            $wsNotifc = 1;
                        } else {
                            $wsNotifc = 0;
                        };
                        $Registro->setUsuario($usuarioc);
                        $Registro->setCedula($ced);
                        $Registro->setApellido1($apellido1c);
                        $Registro->setApellido2($apellido2c);
                        $Registro->setContraseña($passc);
                        $Registro->setTelefono($telefonoc);
                        $Registro->setCorreo($correoc);
                        $Registro->settipoUsuario($tipoUsuarioc);
                        $Registro->setwsNotif($wsNotifc);
                        echo '<p>' .$Registro->insertarPerfil(). '</p>';
                    }
                }        /* Todo fue OK si llegamos a esta línea */
            } catch (Exception $e) {
                /* Podemos finalizar la ejecución con un mensaje o mostrar HTML con él */
                echo sprintf("Error!: %s", $e->getMessage());
            }
            try {
                if (isset($_POST['btSalir'])) {
                    header("Location: ./index.php");
                }
            } catch (Exception $e) {
                log_exception($e);
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
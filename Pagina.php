<?php
session_start();
include './config.php';
$cedula = $_SESSION['cedula'];
if(!isset($cedula)){
    // header ("Location: rediriges a la pagina de logueo".)
    header("Location: ./index.php");
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title> Registro de Asistencia</title> 
        <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0">
        <link rel="stylesheet" href="css/EdicionPP.css">
        <link rel="stylesheet"  type="text/css" href="icons/fonts.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>  
    <header id="header">
        <nav class="menu">
            <div class="logo-box">
                <a href="#"></a>
                <span class="btn-menu"><i class="icon-menu"></i></span>
            </div>

            <div class="list-container">
                <ul class="lists">
                    <li><a href="frmHora.php"><span class="icon-user-check"></span>Registrar hora de Ingreso o Salida</a></li>
                    <li><a href="index.php"><span class="icon-user-check"></span>Cerrar Sesion</a></li>
                </ul>
            </div>
        </nav>
        <script src="js/funcion.js"></script>
    </header>
    <body> 
    <center>
            <div class="Lista">
                <table>
                    <thead>
                        <tr>
                            <td>Perfil</td>
                            <td>Fecha</td>
                            <td>Hora de entrada</td>
                            <td>Hora de salida</td> 
                            <td>Hora de salida a Almuerzo</td>
                            <td>Hora de entrada de Almuerzo</td>
                        </tr>
                    </thead>
                    <?php
                    session_start();
                    $us = $_SESSION['cedula'];
                    $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                    $sql = "SELECT * FROM Fechaperfil WHERE Cedula = '$us'";
                    $result = mysqli_query($conexion, $sql);
                    while($mostrar = mysqli_fetch_array($result)){
                    ?>
                    <tr>
                        <td> <?php echo $mostrar["Cedula"] ?> </td>
                        <td> <?php echo $mostrar["Fecha"] ?> </td>
                        <td> <?php echo $mostrar["HoraIngreso"] ?></td>
                        <td> <?php echo $mostrar["HoraSalida"] ?></td>
                        <td> <?php echo $mostrar["HoraSalidaAlmuerzo"] ?></td>
                        <td> <?php echo $mostrar["HoraEntradaAlmuerzo"] ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        <script src="funcion.js"></script>
        <footer class="footer">
        <label class="DR">&COPY;  Derechos Reservados <?php echo date("Y"); ?> </label> 
        </footer>
        </center>
    </body>
</html>
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
        <link rel="stylesheet" type="text/css" href="icons/fonts.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>  
   <header id="header">
        <nav class="menu">
            <a href=""><img src="image/Sinfondo.png" alt="logo" class="logo_img"></a>
            <div class="logo-box">
                <a href="admin.php"></a>
                <a href="#"></a>
                <span class="btn-menu"><i class="icon-menu"></i></span>
            </div>

            <div class="list-container">
                <ul class="lists">
                    <li><a href="index.php"><span class="icon-user-check"></span>Salir</a></li>
                </ul>
            </div>
        </nav>
       <script src="js/funcion.js"></script>
     </header>
    <body>  
        <form action="admin.php" class="form-box" method="POST">
        <div class="Lista">
            <table>
                <thead>
                    <tr>
                        <td></td>
                        <td>
                            <select id="listN" name="listN">
                                <option selected disabled>Filtrar por Identificación</option>
                                <option name="todos">Todos</option>
                                 <?php
                                  $mysqli = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                                  $query = $mysqli -> query ("SELECT Cedula FROM Fechaperfil");
                                  while ($datos = mysqli_fetch_array($query)) {
                                  echo '<option>'.$datos["Cedula"].'</option>';
                                  }
                                ?>
                            </select>
                        </td>
                        <td></td>
                        <td>
                            <select id="listF" name="listF">
                                <option selected disabled>Filtrar por Fecha</option>
                                <option name="todos">Todos</option>
                                <?php
                                  $mysqli = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                                  $query = $mysqli -> query ("SELECT Fecha FROM Fechaperfil");
                                  while ($datos = mysqli_fetch_array($query)) {
                                  echo '<option>'.$datos["Fecha"].'</option>';
                                  }
                                ?>
                            </select>
                        </td>
                        <td></td>
                        <td>
                            <input type="submit" value="Filtrar" name="btFiltrar" id="btFiltrar">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Perfil</td>
                        <td>Fecha</td>
                        <td>Ubicación</td>
                        <td>Hora de entrada</td>
                        <td>Hora de salida</td>
                        <td>Hora de salida a Almuerzo</td>
                        <td>Hora de entrada de Almuerzo</td>
                    </tr>
                </thead>
                <?php
                if (isset($_POST['listN'])){
                if (isset($_POST['btFiltrar'])){
                    $res = $_POST['listN'];
                $conexion = mysqli_connect("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                if($res=="Todos"){
                $sql = "SELECT * FROM Fechaperfil";
                }else{
                $sql = "SELECT * FROM Fechaperfil WHERE Cedula = '$res'";
                }
                $result = mysqli_query($conexion, $sql);
                
                while ($mostrar = mysqli_fetch_array($result)) {
                    
                    ?>
                    <tr>
                        <td> <?php echo $mostrar["Cedula"] ?> </td>
                        <td> <?php echo $mostrar["Fecha"] ?> </td>
                        <td> <?php echo $mostrar["Ubicacion"] ?> </td>
                        <td> <?php echo $mostrar["HoraIngreso"] ?></td>
                        <td> <?php echo $mostrar["HoraSalida"] ?></td>
                        <td> <?php echo $mostrar["HoraSalidaAlmuerzo"] ?></td>
                        <td> <?php echo $mostrar["HoraEntradaAlmuerzo"] ?></td>
                    </tr>
                    <?php
                }
                }
                }else{
                if (isset($_POST['listF'])){
                if (isset($_POST['btFiltrar'])){
                    $resul = $_POST['listF'];
                
                $conexion = mysqli_connect("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                if($resul=="Todos"){
                $sql = "SELECT * FROM Fechaperfil";
                }else{
                $sql = "SELECT * FROM Fechaperfil WHERE Fecha = '$resul'";
                }
                $result = mysqli_query($conexion, $sql);
                while ($mostrar = mysqli_fetch_array($result)) {
                    ?>
                    <tr>
                        <td> <?php echo $mostrar["Cedula"] ?> </td>
                        <td> <?php echo $mostrar["Fecha"] ?> </td>
                        <td> <?php echo $mostrar["Ubicacion"] ?> </td>
                        <td> <?php echo $mostrar["HoraIngreso"] ?></td>
                        <td> <?php echo $mostrar["HoraSalida"] ?></td>
                        <td> <?php echo $mostrar["HoraSalidaAlmuerzo"] ?></td>
                        <td> <?php echo $mostrar["HoraEntradaAlmuerzo"] ?></td>
                    </tr>
                    <?php
                }
                }
                }else{
                if (isset($_POST['listN']) && isset($_POST['listF'])){
                if (isset($_POST['btFiltrar'])){
                    $res = $_POST['listN'];
                    $resul = $_POST['listF'];
                }
                
                $conexion = mysqli_connect("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                if($res=="Todos" && $resul=="Todos" ){
                $sql = "SELECT * FROM Fechaperfil";
                }else{
                $sql = "SELECT FROM Fechaperfil WHERE Cedula = '$res' AND Fecha = '$resul'";
                }
                $r = mysqli_query($conexion, $sql);
                while ($mostrar = mysqli_fetch_array($r)) {
                    ?>
                    <tr>
                        <td> <?php echo $mostrar["Cedula"] ?> </td>
                        <td> <?php echo $mostrar["Fecha"] ?> </td>
                        <td> <?php echo $mostrar["Ubicacion"] ?> </td>
                        <td> <?php echo $mostrar["HoraIngreso"] ?></td>
                        <td> <?php echo $mostrar["HoraSalida"] ?></td>
                        <td> <?php echo $mostrar["HoraSalidaAlmuerzo"] ?></td>
                        <td> <?php echo $mostrar["HoraEntradaAlmuerzo"] ?></td>
                    </tr>
                    <?php
                }
                }
                }
                }
                ?>
            </table>
        </div>
            
            <div class="perfil">
                <table>
                <thead>
                    <tr>
                        <td>
                            <select id="listC" name="listC">
                                <option selected disabled>Filtrar perfiles por identificación</option>
                                <option name="todos">Todos</option>
                                 <?php
                                  $mysqli= new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                                  $query = $mysqli -> query ("SELECT Cedula FROM Perfiles WHERE Cedula NOT LIKE (3101646103)");
                                  while ($datos = mysqli_fetch_array($query)) {
                                  echo '<option>'.$datos["Cedula"].'</option>';
                                  }
                                ?>
                            </select>
                        </td>
                        <td></td>
                        <td>
                            <input type="submit" value="Filtrar" name="btFiltro" id="btFiltro">
                        </td>
                    </tr>
                    <tr>
                        <td>Cedula</td>
                        <td>Nombre</td>
                        <td>Password</td>
                    </tr>
                </thead>
                <?php
                if (isset($_POST['listC'])){
                if (isset($_POST['btFiltro'])){
                    $resultado = $_POST['listC'];
                $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
                if($resultado=="Todos"){
                $sql = "SELECT * FROM Perfiles WHERE Cedula NOT LIKE (3101646103)";
                }else{
                $sql = "SELECT * FROM Perfiles WHERE Cedula = '$resultado'";
                $_REQUEST["id"] = $resultado;
                }
                $result = mysqli_query($conexion, $sql);
                while ($mostrar = mysqli_fetch_array($result)) {
                    
                    ?>
                    <tr>
                        <td> <?php echo $mostrar["Cedula"] ?> </td>
                        <td> <?php echo $mostrar["Nombre"] ?> </td>
                        <td> <?php echo $mostrar["Password"] ?></td>
                    </tr>
                    <?php
                }
                }
                }
                ?>
            </table>
                <br>
                <br>
            </div>
        </form>
    </body>
</html>
<?php
try {
$host = "107.180.13.125";
$usuario = "asist-ecok";
$clave = "CkowfYQ34JJdQ8Um4ILE";
$bd="asistencia-ecokhemia";
$conexion = mysqli_connect($host, $usuario, $clave, $bd);
//$servername = "107.180.13.125";
//$username = "asist-ecok";
//$password = "CkowfYQ34JJdQ8Um4ILE";
//$bd="asistencia-ecokhemia";

// Create connection
//$conexion = new mysqli($servername, $username, $password, $bd);

// Check connection
if ($conexion->connect_error) {
  die("Connection failed: " . $conexion->connect_error);
} else {
       echo '<script>console.log("TicoWorks says: Conexión Establecida 07-08-2022")</script>';
}
// echo "Connected successfully";
} catch (Exception $e) {
  log_exception($e);
  echo '<script>console.log("Errorcito")</script>';
}

?>

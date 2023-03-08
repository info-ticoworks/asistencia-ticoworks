<?php
try {
  $host = "51.222.14.197";
  $usuario = "tw-dbusr";
  $clave = "5paE2Tuznc2z7HhhMGR8";
  $bd="AsistenciaTW";
  $conexion = mysqli_connect($host, $usuario, $clave, $bd);

  // Check connection
  if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
  } else {
    echo '<script>console.log("TicoWorks says: Conexión Establecida 07-08-2022")</script>';
  }
  //echo "Connected successfully";
  } catch (Exception $e) {
  log_exception($e);
  echo '<script>console.log("Errorcito")</script>';
}
?>

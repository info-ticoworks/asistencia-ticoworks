<?php
session_start();
$cedula = $_SESSION['cedula'];
if(!isset($cedula)){
    // header ("Location: rediriges a la pagina de logueo".)
    header("Location: ./index.php");
}

echo '<script>console.log("Carga de archivo NotiWhats")</script>';
    function notiEntradaSinUbicacion() {
      try {
        require './config.php';
        $sql = "SELECT * FROM usuarios where wsNotif = 1";
        if($result = mysqli_query($conexion, $sql)){
          if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
              $row_id = $row ["cedula"];
              $row_nombre = $row ["nombre"];
              $row_apellido = $row ["apellido1"];
              $row_phone = $row ["telefono"];
              $row_mail = $row ["correo"];
              $userinfo[] = array("cedula"=> $row_id , "nombre"=> $row_nombre, "apellido1"=> $row_apellido,  "telefono"=> $row_phone,  "correo"=> $row_mail);
            }
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
            foreach ($userinfo as $user) {
              //echo "Cédula: {$user['cedula']}\n"
              // . "Nombre: {$user['nombre']}\n"
              // . "Apellido: {$user['apellido1']}\n\n"
              // . "Número de teléfono: {$user['telefono']}\n\n"
              // . "Correo Electrónico: {$user['correo']}\n\n";
              $curl = curl_init();
              curl_setopt_array($curl, [
                  CURLOPT_PORT => "3020",
                  CURLOPT_URL => "http://ws.tico.works/lead",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
                  CURLOPT_HTTPHEADER => [
                  "Content-Type: application/json"
                  ],
              ]);
              $response = curl_exec($curl);
              $err = curl_error($curl);
              curl_close($curl);
              if ($err) {
                  echo "cURL Error #:" . $err;
              } else {
              //echo $response;
              echo '<script>console.log("Notificación enviada por WhatsApp exitosamente...")</script>';
              }
            }
          } else {
            //echo $response;
            echo '<script>console.log("Error al enviar la notificación por WhatsApp...")</script>';
          }
        }


      } catch (Exception $e) {
        log_exception($e);
      }
    }

    function notiEntradaConUbicacion() {
      try {
        require './config.php';
        $sql = "SELECT * FROM usuarios where wsNotif = 1";
        if($result = mysqli_query($conexion, $sql)){
          if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
              $row_id = $row ["cedula"];
              $row_nombre = $row ["nombre"];
              $row_apellido = $row ["apellido1"];
              $row_phone = $row ["telefono"];
              $row_mail = $row ["correo"];
              $userinfo[] = array("cedula"=> $row_id , "nombre"=> $row_nombre, "apellido1"=> $row_apellido,  "telefono"=> $row_phone,  "correo"=> $row_mail);
            }
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
            foreach ($userinfo as $user) {
              //echo "Cédula: {$user['cedula']}\n"
              // . "Nombre: {$user['nombre']}\n"
              // . "Apellido: {$user['apellido1']}\n\n"
              // . "Número de teléfono: {$user['telefono']}\n\n"
              // . "Correo Electrónico: {$user['correo']}\n\n";
              $curl = curl_init();
              curl_setopt_array($curl, [
                  CURLOPT_PORT => "3020",
                  CURLOPT_URL => "http://ws.tico.works/lead",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
                  CURLOPT_HTTPHEADER => [
                  "Content-Type: application/json"
                  ],
              ]);
              $response = curl_exec($curl);
              $err = curl_error($curl);
              curl_close($curl);
              if ($err) {
                  echo "cURL Error #:" . $err;
              } else {
              //echo $response;
              echo '<script>console.log("Notificación enviada por WhatsApp exitosamente...")</script>';
              }
            }
          } else {
            //echo $response;
            echo '<script>console.log("Error al enviar la notificación por WhatsApp...")</script>';
          }
        }


      } catch (Exception $e) {
        log_exception($e);
      }
    }



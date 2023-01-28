<?php
echo '<script>console.log("Carga de archivo NotiWhats")</script>';
Class NotiWhats {
  private $cedula;
  public function __construct() {
    $this->cedula = "";
  } 
  public function getCedula() {
    return $this->cedula;
  }
  public function setCedula($cedula): void {
    $this->cedula = $cedula;
  }
  function enviarNoti() {
    echo '<script>console.log("Paso 1 - Envío de Notificación por WhatsApp")</script>';
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_PORT => "3020",
      CURLOPT_URL => "http://51.222.14.197:3020/lead",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\n  \"message\":\"Mi número de cédula es {}\",\n  \"phone\":\"50683528129\"\n}",
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
      echo '<script>console.log("Paso 2 Notificacion")</script>';
  }
}
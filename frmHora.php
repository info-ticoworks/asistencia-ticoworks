<?php
session_start();
include './config.php';
$cedula = $_SESSION['cedula'];
$nombre = $_SESSION['nombre'];
$apellido1 = $_SESSION['apellido1'];
$telefono = $_SESSION['telefono'];
$correo = $_SESSION['correo'];
$idTipoUsuario = $_SESSION['idTipoUsuario'];
$wsNotif = $_SESSION['wsNotif'];
if(!isset($cedula)){
    // header ("Location: rediriges a la pagina de logueo".)
    header("Location: ./index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>TW-Asistencia</title>
        <link rel="stylesheet" href="./css/EdicionAR.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>
    <body>

        <script type="text/javascript">
            header("Refresh:0");
           if (typeof navigator.geolocation == 'object'){
               navigator.geolocation.getCurrentPosition(mostrar_ubicacion);
           }

           function mostrar_ubicacion(p)
           {
               var posicion = p.coords.latitude+','+p.coords.longitude;
               var latitud = p.coords.latitude;
               var longitud = p.coords.longitude;
               document.getElementById("Ub").value = posicion;
               document.getElementById("latitud").value = latitud;
               document.getElementById("longitud").value = longitud;
           }
        </script>

        <form action="frmHora.php" class="access-box" method="POST"> 
            <p><img alt="" width="280" height="216" src="./image/logo1.png"></p> 
                <h3 class="form-title">Registrar hora</h3>
                <h3 class="form-title">Hola <?php echo $nombre?> <?php echo $apellido1?> </h3>

                <select id="lista" name="lista">
                    <option selected disabled>Horario a establecer</option>
                    <option name="Entrada">Entrada</option>
                    <option name="Salida a descanso">Salida a descanso</option>
                    <option name="Entrada de descanso">Entrada de descanso</option>
                    <option name="Salida">Salida</option>
                </select>
                            <!-- <input type="checkbox" name="00">
                            <p>¿Mantener sesion iniciada?</p>--> 
            <input type="submit" value="Registrar marca" name="btEnviar" id="btEnviar">
            <input type="hidden" id="Ub" name="Ub" readonly>
            <input type="hidden" id="latitud" name="latitud" readonly>
            <input type="hidden" id="longitud" name="longitud" readonly>
            <input type="submit" value="Cerrar Sesión" name="btSalir" id="btSalir">
        </form>
    <?php

    function getAddress($latitude, $longitude)
    {
        try {
                //google map api url
                $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=AIzaSyBv0et_lBK9kxP1DCwAnmdfP8YH-j32JkU";

                // send http request
                $geocode = file_get_contents($url);
                $json = json_decode($geocode);
                $address = $json->results[0]->formatted_address;
                return $address;
            } catch (Exception $e) {
                log_exception($e);
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
    }
    try {
        if (isset($_POST['btEnviar'])) {
            require './config.php';
            echo '<script>console.log("Paso 1")</script>';
            require './class/FechaPerfil.php';
            echo '<script>console.log("Paso 3")</script>';
            //require './notiWhats.php';
            //echo '<script>console.log("Paso 5")</script>';
            $FechaPerfil = new FechaPerfil();
            if(empty($_POST['lista'])) {
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Lo sentimos...!',
                text: 'Debe escoger una opción válida!',  
                })
                </script>";
            } else {
                $listR = $_POST["lista"];
            }
            $date = date("y-m-d");
            $ubicacion = $_POST['Ub'];
            $latitude = $_POST['latitud'];
            $longitude = $_POST['longitud'];
            // coordinates
            //echo 'Latitud: ' . $latitude;
            //echo 'Longitud: ' . $longitude;
            //$result = getAddress($latitude, $longitude);
            //echo 'Address: ' . $result;

                // produces output
                // Address: 58 Brooklyn Ave, Brooklyn, NY 11216, USA

            if(empty($cedula) || empty($_POST['lista'])){
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Lo sentimos...!',
                text: 'Debe completar el formulario!',  
                })
                </script>";
                //header("Refresh:5");
            }else{
            //Inicio de consulta de usuario
            $sql = "SELECT * FROM usuarios where cedula = $cedula";
            if($result = mysqli_query($conexion, $sql)){
                if(mysqli_num_rows($result) > 0){
                    //while($row = mysqli_fetch_array($result)){
                        //$nombre = $row['nombre'];
                        //$apellido1 = $row['apellido1'];
                    //}
                    //echo "</table>";
                    // Free result set
                    //mysqli_free_result($result);

                    //Entrada
                    if ($listR == "Entrada") {
                        $q = "SELECT COUNT(*) as contar from marcas where cedula = '$cedula' AND fecha = '$date' AND idTipoMarca = 1";
                        $consulta = mysqli_query($conexion, $q);
                        $array = mysqli_fetch_array($consulta);
                        if ($array['contar'] == 0) {
                            $FechaPerfil->setUser($cedula);
                            $FechaPerfil->setDate($date);
                            $FechaPerfil->setUbicacion($ubicacion);
                            echo '<p>' . $FechaPerfil->insertarUsuarioIngreso() . '</p>';
                            date_default_timezone_set('America/Costa_Rica');
                            $time = date("H:i");
                            $FechaPerfil->setIngreso($time);
                            $FechaPerfil->setDate($date);
                            $FechaPerfil->setUser($cedula);
                            echo '<p>' . $FechaPerfil->insertarHoraIngreso(). '</p>';
                            //require './notiWhats.php';
                            if(empty($_POST['Ub']) || $listR == ""){
                                //Notificaciones sin Ubicación
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
                                        // Free result set
                                        mysqli_free_result($result);
                                        foreach ($userinfo as $user) {
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
                                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                            }else{
                                //Notificaciones con Ubicación
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
                                        // Free result set
                                        mysqli_free_result($result);
                                        foreach ($userinfo as $user) {
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
                                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                            }
                            echo "<script>
                            Swal.fire({
                            icon: 'success',
                            title: 'Felicidades...!',
                            text: 'Hora de ingreso registrada..!',  
                            })
                            </script>";
                            }else{
                                echo "<script>
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Lo sentimos $nombre...!',
                                    text: 'Ya ha registrado la hora de ingreso de hoy $date..!',  
                                    })
                                </script>";
                                header("Refresh:5");
                            }
                    //Salida
                    } else {
                        if ($listR == "Salida") {
                            $q = "SELECT COUNT(*) as contar from marcas where cedula = '$cedula' AND fecha = '$date' AND idTipoMarca = 4";
                            $consulta = mysqli_query($conexion, $q);
                            $array = mysqli_fetch_array($consulta);
                            if ($array['contar'] == 0) {
                                $FechaPerfil->setUser($cedula);
                                $FechaPerfil->setDate($date);
                                $FechaPerfil->setUbicacion($ubicacion);
                                echo '<p>' . $FechaPerfil->insertarUsuarioSalida() . '</p>';
                                date_default_timezone_set('America/Costa_Rica');
                                $time = date("H:i");
                                $FechaPerfil->setSalida($time);
                                $FechaPerfil->setDate($date);
                                $FechaPerfil->setUser($cedula);
                                echo '<p>' . $FechaPerfil->insertarHoraSalida() . '</p>';
                                if(empty($_POST['Ub']) || $listR == ""){
                                    //Notificaciones sin Ubicación
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
                                            // Free result set
                                            mysqli_free_result($result);
                                            foreach ($userinfo as $user) {
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
                                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                                }else{
                                    //Notificaciones con Ubicación
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
                                            // Free result set
                                            mysqli_free_result($result);
                                            foreach ($userinfo as $user) {
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
                                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                                }
                                    echo "<script>
                                        Swal.fire({
                                        icon: 'success',
                                        title: 'Felicidades...!',
                                        text: 'Hora de salida registrada..!',  
                                        })
                                    </script>";
                                } else {
                                    echo "<script>
                                        Swal.fire({
                                        icon: 'error',
                                        title: 'Lo sentimos $nombre...!',
                                        text: 'Ya ha registrado su hora de salida de hoy $date..!',  
                                        })
                                    </script>";
                                }

                    //Salida a almuerzo
                    } else {
                        if ($listR == "Salida a descanso") {
                            $q = "SELECT COUNT(*) as contar from marcas where cedula = '$cedula' AND fecha = '$date' AND idTipoMarca = 2";
                            $consulta = mysqli_query($conexion, $q);
                            $array = mysqli_fetch_array($consulta);
                            if ($array['contar'] == 0) {
                                $FechaPerfil->setUser($cedula);
                                $FechaPerfil->setDate($date);
                                $FechaPerfil->setUbicacion($ubicacion);
                                echo '<p>' . $FechaPerfil->insertarUsuarioSalidaAlmuerzo() . '</p>';
                                date_default_timezone_set('America/Costa_Rica');
                                $time = date("H:i");
                                $FechaPerfil->setSalidaAlmuerzo($time);
                                $FechaPerfil->setDate($date);
                                $FechaPerfil->setUser($cedula);
                                echo '<p>' . $FechaPerfil->insertarHoraSalidaAlmuerzo() . '</p>';
                                if(empty($_POST['Ub']) || $listR == ""){
                                    //Notificaciones sin Ubicación
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
                                            // Free result set
                                            mysqli_free_result($result);
                                            foreach ($userinfo as $user) {
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
                                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su salida a descanso, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                                }else{
                                    //Notificaciones con Ubicación
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
                                            // Free result set
                                            mysqli_free_result($result);
                                            foreach ($userinfo as $user) {
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
                                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su salida a descanso desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                                }
                                echo "<script>
                                Swal.fire({
                                icon: 'success',
                                title: 'Felicidades...!',
                                text: 'Hora de salida a almuerzo registrada..!',  
                                })
                                </script>";
                                } else {
                                    echo "<script>
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Lo sentimos $nombre...!',
                                    text: 'Ya ha registrado su hora de salida a almuerzo de hoy $date..!',  
                                    })
                                    </script>";
                                }
                    //Entrada después de almuerzo
                    } else {
                        if ($listR == "Entrada de descanso") {
                            $q = "SELECT COUNT(*) as contar from marcas where cedula = '$cedula' AND fecha = '$date' AND idTipoMarca = 3";
                            $consulta = mysqli_query($conexion, $q);
                            $array = mysqli_fetch_array($consulta);
                                if ($array['contar'] == 0) {
                                    $FechaPerfil->setUser($cedula);
                                    $FechaPerfil->setDate($date);
                                    $FechaPerfil->setUbicacion($ubicacion);
                                    echo '<p>' . $FechaPerfil->insertarUsuarioEntradaAlmuerzo() . '</p>';
                                    date_default_timezone_set('America/Costa_Rica');
                                    $time = date("H:i");
                                    $FechaPerfil->setEntradaAlmuerzo($time);
                                    $FechaPerfil->setDate($date);
                                    $FechaPerfil->setUser($cedula);
                                    echo '<p>' . $FechaPerfil->insertarHoraEntradaAlmuerzo() . '</p>';
                                    if(empty($_POST['Ub']) || $listR == ""){
                                        //Notificaciones sin Ubicación
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
                                                // Free result set
                                                mysqli_free_result($result);
                                                foreach ($userinfo as $user) {
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
                                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su entrada después de descanso, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                                    }else{
                                        //Notificaciones con Ubicación
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
                                                // Free result set
                                                mysqli_free_result($result);
                                                foreach ($userinfo as $user) {
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
                                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola {$user['nombre']}. El colaborador $nombre $apellido1, con la cédula $cedula ha registrado su entrada después de descanso desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"506{$user['telefono']}\"\n}",
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
                                    }
                                    echo "<script>
                                        Swal.fire({
                                        icon: 'success',
                                        title: 'Felicidades...!',
                                        text: 'Hora de entrada del almuerzo registrada..!',  
                                        })
                                    </script>";
                                } else {
                                    echo "<script>
                                        Swal.fire({
                                        icon: 'error',
                                        title: 'Lo sentimos $nombre $nombre...!',
                                        text: 'Ya ha registrado su hora de entrada del almuerzo de hoy $date..!',  
                                        })
                                        </script>";
                                    }
                    } else {
                        if(empty($cedula) || $listR == ""){
                            echo "<script>
                            Swal.fire({
                            icon: 'error',
                            title: 'Lo sentimos $nombre...!',
                            text: 'Debe completar el formulario..!',  
                            })
                            </script>";    
                        }
                    }
                }
            }
        }
                } else {
                    echo "<script>
                    Swal.fire({
                    icon: 'error',
                    title: 'Lo sentimos $nombre!',
                    text: 'La cédula ingresada no corresponde a ningún colaborador de TicoWorks!',  
                    })
                    </script>";
                    header("Refresh:5");
                }
            } else{
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($conexion);
            }
            //Final de consulta de usuario
        }
    }
} catch (Exception $e) {
        log_exception($e);
}
    try {
        if (isset($_POST['btSalir'])) {
            header("Location: ./index.php");
        }
    } catch (Exception $e) {
        log_exception($e);
    }

?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Ecokhemia</title>
        <link rel="stylesheet" href="css/EdicionAR.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>
    <body>

        <script type="text/javascript">
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

        <form action="frmHora.php" class="form-box" method="POST"> 
            <p><img alt="" width="280" height="135" src="./image/Sinfondo.png"></p> 
                <h3 class="form-title">Registrar hora</h3>
                <input type="text" placeholder="Cedula" name="ced" id="ced" autofocus>
                <select id="lista" name="lista">
                    <option selected disabled>Horario a establecer</option>
                    <option name="Ingreso">Ingreso</option>
                    <option name="Salida">Salida</option>
                    <option name="Salida a Almuerzo">Salida a Almuerzo</option>
                    <option name="Entrada despues del almuerzo">Entrada despues de Almuerzo</option>
                </select>
                            <!-- <input type="checkbox" name="00">
                            <p>¿Mantener sesion iniciada?</p>--> 
            <input type="submit" value="Enviar" name="btEnviar" id="btEnviar">
            <p>¿Ya has registrado la hora? <a class="" href="./">Volver atras</a></p>
            <input type="hidden" id="Ub" name="Ub" readonly>
            <input type="hidden" id="latitud" name="latitud" readonly>
            <input type="hidden" id="longitud" name="longitud" readonly>
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
            echo '<script>console.log("Paso 2")</script>';
            require './config.php';
            echo '<script>console.log("Paso 4")</script>';
            require './class/FechaPerfil.php';
            echo '<script>console.log("Paso 5")</script>';
            require './noti_ingreso.php';
            $FechaPerfil = new FechaPerfil();
            $Noti = new NotiWhats();
            $listR = $_POST["lista"];
            $cedula = ($_POST['ced']);
            $date = date("y-m-d");
            $ubicacion = $_POST['Ub'];
            $latitude = $_POST['latitud'];
            $longitude = $_POST['longitud'];
            $nombre = "";
            // coordinates
            //echo 'Latitud: ' . $latitude;
            //echo 'Longitud: ' . $longitude;
            //$result = getAddress($latitude, $longitude);
            //echo 'Address: ' . $result;

                // produces output
                // Address: 58 Brooklyn Ave, Brooklyn, NY 11216, USA

            if(empty($_POST['ced']) || $listR == ""){
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'Debe completar el formulario!',  
                })
                </script>";

            }else{
            //Inicio de consulta de usuario
            $sql = "SELECT * FROM Perfiles where Cedula = $cedula";
            if($result = mysqli_query($conexion, $sql)){
                if(mysqli_num_rows($result) > 0){
                    //echo "<table>";
                        //echo "<tr>";
                            //echo "<th>Cedula</th>";
                            //echo "<th>Nombre</th>";
                        //echo "</tr>";
                    while($row = mysqli_fetch_array($result)){
                        //echo "<tr>";
                        //    echo "<td>" . $row['Cedula'] . "</td>";
                        //    echo "<td>" . $row['Nombre'] . "</td>";
                        //echo "</tr>";
                        $nombre = $row['Nombre'];
                        //echo $cedula;
                        //echo $nombre;
                        //echo $ubicacion;
                    }
                    echo "</table>";
                    // Free result set
                    mysqli_free_result($result);


                    $q = "SELECT COUNT(*) as contar from Fechaperfil where Cedula = '$cedula' AND Fecha = '$date'";
                    $consulta = mysqli_query($conexion, $q);
                    $array = mysqli_fetch_array($consulta);
                        if ($array['contar'] == 0) {
                        $FechaPerfil->setUser($cedula);
                        $FechaPerfil->setDate($date);
                        $FechaPerfil->setUbicacion($ubicacion);
                        echo '<p>' . $FechaPerfil->insertarFechaPerfil() . '</p>';
                        }
                    if ($listR == "Ingreso") {
                            date_default_timezone_set('America/Costa_Rica');
                            $time = date("H:i");
                            $cons = "SELECT COUNT(HoraIngreso) as contar from Fechaperfil where Cedula = '$cedula' AND Fecha = '$date'";
                            $consulta = mysqli_query($conexion, $cons);
                            $array = mysqli_fetch_array($consulta);
                            if ($array['contar'] == 0) {
                            $FechaPerfil->setIngreso($time);
                            $FechaPerfil->setDate($date);
                            $FechaPerfil->setUser($cedula);
                            echo '<p>' . $FechaPerfil->insertarHoraIngreso(). '</p>';
        
                            if(empty($_POST['Ub']) || $listR == ""){

                                //Notificaciones sin Ubicación

                            //inicio de envío de notificación por WhatsApp a César
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683528129\"\n}",
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
                        //Final de envío de notificación por WhatsApp a César
    
                        //inicio de envío de notificación por WhatsApp a Josué
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660731814\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Josué
    
                        //inicio de envío de notificación por WhatsApp a Andrea
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50687090676\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Andrea

                        //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662741130\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Emilio de Calidad

                        //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660071814\"\n}",
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
                        //Final de envío de notificación por WhatsApp al administrador Jeffry Robles

                                //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662129007\"\n}",
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
                            //Final de envío de notificación por WhatsApp RRHH Hilary

                        //inicio de envío de notificación por WhatsApp al Supervisor
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50661363126\"\n}",
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
                        //Final de envío de notificación por WhatsApp al Supervisor

                        //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50663710437\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                        //inicio de envío de notificación por WhatsApp a Mario Moya
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50671181265\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Mario Moya

                        //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50664041755\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                        //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683307411\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Maritza de BMW


                    }else{

                    //Notificaciones con Ubicación

                    //inicio de envío de notificación por WhatsApp a César
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683528129\"\n}",
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
                    //Final de envío de notificación por WhatsApp a César
    
                    //inicio de envío de notificación por WhatsApp a Josué
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660731814\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Josué
    
                    //inicio de envío de notificación por WhatsApp a Andrea
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50687090676\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Andrea

                    //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662741130\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Emilio de Calidad

                    //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660071814\"\n}",
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
                    //Final de envío de notificación por WhatsApp al administrador Jeffry Robles

                    //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662129007\"\n}",
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
                    //Final de envío de notificación por WhatsApp RRHH Hilary

                    //inicio de envío de notificación por WhatsApp al Supervisor
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50661363126\"\n}",
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
                    //Final de envío de notificación por WhatsApp al Supervisor

                    //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50663710437\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                    //inicio de envío de notificación por WhatsApp a Mario Moya
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50671181265\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Mario Moya

                    //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50664041755\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                    //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su inicio de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683307411\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Maritza de BMW

                            }

                            echo "<script>
                        Swal.fire({
                        icon: 'success',
                        title: 'Enhorabuena...!',
                        text: 'Hora de ingreso registrada..!',  
                        })
                        </script>";
                            }else{
                                echo "<script>
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...!',
                        text: 'Ya ha registrado la hora de ingreso de hoy $date..!',  
                        })
                        </script>";
                            }
                    } else {
                        if ($listR == "Salida") {
                            date_default_timezone_set('America/Costa_Rica');
                            $time = date("H:i");
                            $cons = "SELECT COUNT(HoraSalida) as contar from Fechaperfil where Cedula = '$cedula' AND Fecha = '$date'";
                            $consulta = mysqli_query($conexion, $cons);
                            $array = mysqli_fetch_array($consulta);
                            if ($array['contar'] > 0) {
                                echo "<script>
                        Swal.fire({
                        icon: 'error',
                        title: 'Oops...!',
                        text: 'Ya ha registrado su hora de salida de hoy $date..!',  
                        })
                        </script>";
                            } else {
                                $FechaPerfil->setSalida($time);
                                $FechaPerfil->setDate($date);
                                $FechaPerfil->setUser($cedula);
                                echo '<p>' . $FechaPerfil->insertarHoraSalida() . '</p>';

                                if(empty($_POST['Ub']) || $listR == ""){

                                    //Notificaciones sin Ubicación
    
                                //inicio de envío de notificación por WhatsApp a César
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683528129\"\n}",
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
                            //Final de envío de notificación por WhatsApp a César
        
                            //inicio de envío de notificación por WhatsApp a Josué
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660731814\"\n}",
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
                            //Final de envío de notificación por WhatsApp a Josué
        
                            //inicio de envío de notificación por WhatsApp a Andrea
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50687090676\"\n}",
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
                            //Final de envío de notificación por WhatsApp a Andrea
    
                            //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662741130\"\n}",
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
                            //Final de envío de notificación por WhatsApp a Emilio de Calidad
    
                            //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660071814\"\n}",
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
                            //Final de envío de notificación por WhatsApp al administrador Jeffry Robles
    
                                    //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662129007\"\n}",
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
                                //Final de envío de notificación por WhatsApp RRHH Hilary
    
                            //inicio de envío de notificación por WhatsApp al Supervisor
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50661363126\"\n}",
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
                            //Final de envío de notificación por WhatsApp al Supervisor

                        //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50663710437\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                    //inicio de envío de notificación por WhatsApp a Mario Moya
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50671181265\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Mario Moya

                    //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50664041755\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                    //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683307411\"\n}",
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
                //Final de envío de notificación por WhatsApp a Maritza de BMW
    
    
                                }else{
    
                                //Notificaciones con Ubicación
    
                                //inicio de envío de notificación por WhatsApp a César
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683528129\"\n}",
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
                            //Final de envío de notificación por WhatsApp a César
        
                            //inicio de envío de notificación por WhatsApp a Josué
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660731814\"\n}",
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
                            //Final de envío de notificación por WhatsApp a Josué
        
                            //inicio de envío de notificación por WhatsApp a Andrea
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50687090676\"\n}",
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
                            //Final de envío de notificación por WhatsApp a Andrea
    
                            //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662741130\"\n}",
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
                            //Final de envío de notificación por WhatsApp a Emilio de Calidad
    
                            //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660071814\"\n}",
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
                            //Final de envío de notificación por WhatsApp al administrador Jeffry Robles
    
                                    //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662129007\"\n}",
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
                                //Final de envío de notificación por WhatsApp RRHH Hilary
    
                            //inicio de envío de notificación por WhatsApp al Supervisor
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
                                    CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50661363126\"\n}",
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
                            //Final de envío de notificación por WhatsApp al Supervisor

                    //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50663710437\"\n}",
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
                //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                //inicio de envío de notificación por WhatsApp a Mario Moya
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50671181265\"\n}",
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
                //Final de envío de notificación por WhatsApp a Mario Moya

                //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50664041755\"\n}",
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
                //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su finalización de labores desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683307411\"\n}",
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
                //Final de envío de notificación por WhatsApp a Maritza de BMW
    
    
                                }

                        echo "<script>
                        Swal.fire({
                        icon: 'success',
                        title: 'Enhorabuena...!',
                        text: 'Hora de salida registrada..!',  
                        })
                        </script>";
                            }
                        } else {
                            if ($listR == "Salida a Almuerzo") {
                                date_default_timezone_set('America/Costa_Rica');
                                $time = date("H:i");
                                $cons = "SELECT COUNT(HoraSalidaAlmuerzo) as contar from Fechaperfil where Cedula = '$cedula' AND Fecha = '$date'";
                                $consulta = mysqli_query($conexion, $cons);
                                $array = mysqli_fetch_array($consulta);
                                if ($array['contar'] > 0) {
                                    echo "<script>
                                Swal.fire({
                                icon: 'error',
                                title: 'Oops...!',
                                text: 'Ya ha registrado su hora de salida a almuerzo de hoy $date..!',  
                                })
                                </script>";
                                } else {
                                    $FechaPerfil->setSalidaAlmuerzo($time);
                                    $FechaPerfil->setDate($date);
                                    $FechaPerfil->setUser($cedula);
                                    echo '<p>' . $FechaPerfil->insertarHoraSalidaAlmuerzo() . '</p>';

                                    if(empty($_POST['Ub']) || $listR == ""){

                                        //Notificaciones sin Ubicación
        
                                    //inicio de envío de notificación por WhatsApp a César
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683528129\"\n}",
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
                                //Final de envío de notificación por WhatsApp a César
            
                                //inicio de envío de notificación por WhatsApp a Josué
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660731814\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Josué
            
                                //inicio de envío de notificación por WhatsApp a Andrea
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50687090676\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Andrea
        
                                //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662741130\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Emilio de Calidad
        
                                //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660071814\"\n}",
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
                                //Final de envío de notificación por WhatsApp al administrador Jeffry Robles
        
                                        //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662129007\"\n}",
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
                                    //Final de envío de notificación por WhatsApp RRHH Hilary
        
                                //inicio de envío de notificación por WhatsApp al Supervisor
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50661363126\"\n}",
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
                                //Final de envío de notificación por WhatsApp al Supervisor

                        //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50663710437\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                    //inicio de envío de notificación por WhatsApp a Mario Moya
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50671181265\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Mario Moya

                    //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50664041755\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                    //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683307411\"\n}",
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
                //Final de envío de notificación por WhatsApp a Maritza de BMW
        
        
                                    }else{
        
                                    //Notificaciones con Ubicación
        
                                    //inicio de envío de notificación por WhatsApp a César
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683528129\"\n}",
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
                                //Final de envío de notificación por WhatsApp a César
            
                                //inicio de envío de notificación por WhatsApp a Josué
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660731814\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Josué
            
                                //inicio de envío de notificación por WhatsApp a Andrea
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50687090676\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Andrea
        
                                //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662741130\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Emilio de Calidad
        
                                //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660071814\"\n}",
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
                                //Final de envío de notificación por WhatsApp al administrador Jeffry Robles
        
                                        //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662129007\"\n}",
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
                                    //Final de envío de notificación por WhatsApp RRHH Hilary
        
                                //inicio de envío de notificación por WhatsApp al Supervisor
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50661363126\"\n}",
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
                                //Final de envío de notificación por WhatsApp al Supervisor

                            //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50663710437\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Supervisor Leonardo
    
                        //inicio de envío de notificación por WhatsApp a Mario Moya
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50671181265\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Mario Moya
    
                        //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50664041755\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Elizabeth de BMW
    
                        //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                                CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su salida a almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683307411\"\n}",
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
                        //Final de envío de notificación por WhatsApp a Maritza de BMW
        
        
                                    }


                                    echo "<script>
                                Swal.fire({
                                icon: 'success',
                                title: 'Enhorabuena...!',
                                text: 'Hora de salida a almuerzo registrada..!',  
                                })
                                </script>";
                                }
                            } else {
                            if ($listR == "Entrada despues de Almuerzo") {
                                date_default_timezone_set('America/Costa_Rica');
                                $time = date("H:i");
                                $cons = "SELECT COUNT(HoraEntradaAlmuerzo) as contar from Fechaperfil where Cedula = '$cedula' AND Fecha = '$date'";
                                $consulta = mysqli_query($conexion, $cons);
                                $array = mysqli_fetch_array($consulta);
                                if ($array['contar'] > 0) {
                                    echo "<script>
                                    Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...!',
                                    text: 'Ya ha registrado su hora de entrada del almuerzo de hoy $date..!',  
                                    })
                                    </script>";
                                } else {
                                    $FechaPerfil->setEntradaAlmuerzo($time);
                                    $FechaPerfil->setDate($date);
                                    $FechaPerfil->setUser($cedula);
                                    echo '<p>' . $FechaPerfil->insertarHoraEntradaAlmuerzo() . '</p>';

                                    if(empty($_POST['Ub']) || $listR == ""){

                                        //Notificaciones sin Ubicación
        
                                    //inicio de envío de notificación por WhatsApp a César
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683528129\"\n}",
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
                                //Final de envío de notificación por WhatsApp a César
            
                                //inicio de envío de notificación por WhatsApp a Josué
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660731814\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Josué
            
                                //inicio de envío de notificación por WhatsApp a Andrea
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50687090676\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Andrea
        
                                //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662741130\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Emilio de Calidad
        
                                //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50660071814\"\n}",
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
                                //Final de envío de notificación por WhatsApp al administrador Jeffry Robles
        
                                        //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50662129007\"\n}",
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
                                    //Final de envío de notificación por WhatsApp RRHH Hilary
        
                                //inicio de envío de notificación por WhatsApp al Supervisor
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50661363126\"\n}",
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
                                //Final de envío de notificación por WhatsApp al Supervisor

                        //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50663710437\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                    //inicio de envío de notificación por WhatsApp a Mario Moya
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50671181265\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Mario Moya

                    //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50664041755\"\n}",
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
                    //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                    //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo, sin embargo, no se registró ninguna ubicación.\",\n  \"phone\":\"50683307411\"\n}",
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
                //Final de envío de notificación por WhatsApp a Maritza de BMW
        
        
                                    }else{
        
                                    //Notificaciones con Ubicación
        
                                    //inicio de envío de notificación por WhatsApp a César
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola César! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683528129\"\n}",
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
                                //Final de envío de notificación por WhatsApp a César
            
                                //inicio de envío de notificación por WhatsApp a Josué
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Josué! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660731814\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Josué
            
                                //inicio de envío de notificación por WhatsApp a Andrea
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Andrea! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50687090676\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Andrea
        
                                //inicio de envío de notificación por WhatsApp a Emilio de Calidad
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Emilio! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662741130\"\n}",
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
                                //Final de envío de notificación por WhatsApp a Emilio de Calidad
        
                                //inicio de envío de notificación por WhatsApp al administrador Jeffry Robles
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Jeffry! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50660071814\"\n}",
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
                                //Final de envío de notificación por WhatsApp al administrador Jeffry Robles
        
                                        //inicio de envío de notificación por WhatsApp a RRHH Hilary
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
                                            CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Hilary! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50662129007\"\n}",
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
                                    //Final de envío de notificación por WhatsApp RRHH Hilary
        
                                //inicio de envío de notificación por WhatsApp al Supervisor
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
                                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Supervisor! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50661363126\"\n}",
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
                                //Final de envío de notificación por WhatsApp al Supervisor

                    //inicio de envío de notificación por WhatsApp a Supervisor Leonardo
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Leonardo! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50663710437\"\n}",
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
                //Final de envío de notificación por WhatsApp a Supervisor Leonardo

                //inicio de envío de notificación por WhatsApp a Mario Moya
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Mario! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50671181265\"\n}",
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
                //Final de envío de notificación por WhatsApp a Mario Moya

                //inicio de envío de notificación por WhatsApp a Elizabeth de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Elizabeth! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50664041755\"\n}",
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
                //Final de envío de notificación por WhatsApp a Elizabeth de BMW

                //inicio de envío de notificación por WhatsApp a Maritza de BMW
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
                        CURLOPT_POSTFIELDS => "{\n  \"message\":\"Hola Maritza! El colaborador $nombre, con la cédula $cedula ha registrado su entrada después de almuerzo desde la ubicación: https://www.google.com/maps/search/?api=1&query=$latitude%2C$longitude\",\n  \"phone\":\"50683307411\"\n}",
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
                //Final de envío de notificación por WhatsApp a Maritza de BMW
        
        
                                    }

                                    echo "<script>
                                Swal.fire({
                                icon: 'success',
                                title: 'Enhorabuena...!',
                                text: 'Hora de entrada del almuerzo registrada..!',  
                                })
                                </script>";
                                }
                            } else {
                                if(empty($_POST['ced']) || $listR == ""){
                                echo "<script>
                                Swal.fire({
                                icon: 'error',
                                title: 'Oops...!',
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
                title: 'Lo sentimos!',
                text: 'La cédula ingresada no corresponde a ningún colaborador de Ecokhemia!',  
                })
                </script>";
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

    ?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>TW-Asistencia</title>
        <link rel="stylesheet" href="css/EdicionAR.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>

    <body>
        <form action="frmAcceder.php" class="form-box" method="POST"> 
            <p><img alt="" width="280" height="216" src="image/logo1.png"></p> 
            <h3 class="form-title">Iniciar Sesion</h3>
            <input type="text" placeholder="Cedula" name="ced" id="ced" autofocus>
            <input type="password" placeholder="Contraseña" name="passc" id="passc">
            <div>
               <!-- <input type="checkbox" name="00">
                <p>¿Mantener sesion iniciada?</p>--> 
            </div>

            <input type="submit" value="Acceder" name="btverificar" id="btverificar">
            <p>¿Olvidaste tu contraseña? <a class="volver" href="/frmRecuperarContrasena.php">Recuperar</a></p>
            <p><a class="volver" href="./index.php">Volver atras</a></p>
            <?php
            if (isset($_POST['btverificar'])) {
                require './config.php';
                $cedula = $_POST['ced'];
                $pass = $_POST['passc'];
                $q = "SELECT COUNT(*) as contar from usurios where cedula = '$cedula' AND pass = '$pass'";
                $consulta = mysqli_query($conexion, $q);
                $array = mysqli_fetch_array($consulta);
                if ($array['contar'] = 1) {
                    $_SESSION['cedula'] = $cedula;
                    if($cedula == "205610630"){
                    echo '<script type="text/javascript">
                    window.location = "/admin.php"
                    </script>';
                    }else{
                    echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'Acceso no autorizado al sistema..!',  
                })
                </script>";
                    }
                } else {
                    if(empty($_POST['ced']) || empty($_POST['passc'])){
                    echo "<script>
                Swal.fire({
                icon: 'warning',
                title: 'Oops...!',
                text: 'Debe digitar todos los Datos.',  
                })
                </script>";
                    }else{
                        echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'Datos incorrectos.',  
                })
                </script>";
                    }
                  }
                }
            ?>
        </form>
    </body>
</html>
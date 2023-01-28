<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Ecokhemia</title> 
        <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0">
        <link rel="stylesheet" href="css/EdicionAR.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>  
    <body>
        <form action="frmRecuperarContrasena.php" class="form-box" method ="POST"> 
            <p><img alt="" width="280" height="135" src="image/Sinfondo.png"></p> 
            <h3 class="form-title">Recuperar Contraseña</h3>
            <input type="text" placeholder="Cedula" name="ced" id="ced">
            <input type="password" placeholder="Nueva Contraseña" name="new" id="new" >
            <input type="password" placeholder="Confirmar Contraseña" name="conf" id="conf">
            <div>
                <!-- <p> ¿Acepta todos los terminos? </p>
                <input type="checkbox" name="00"> -->
            </div>
            <input type="submit" value="Actualizar contraseña" name="btGuardar" id="btGuardar"> 
            <p>¿Ya has cambiado la contraseña? <a class="" href="frmAcceder.php">Volver al inicio de sesión</a></p>

            <?php
            if (isset($_POST['btGuardar'])) {
                require_once './class/Recuperar.php';
                $Recuperar = new Recuperar();
                session_start();
                $cedula = $_POST['ced'];
                $new = $_POST['new'];
                $conf = $_POST['conf'];
                $usuario = $cedula;
                if (empty($_POST['ced']) || empty($_POST['new']) || empty($_POST['conf'])) {
                    echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'Debe digitar todos los Datos.',  
                })
                </script>";
                } else {
                    if (strlen(($_POST["new"])) < 8 ) {
                            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'La contraseña debe tener un minimo de 8 caracteres..!',  
                })
                </script>";
                    }else{
                    if ($new == $conf) {
                        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia", 3306);

                        if ($conexion->connect_errno) {
                            echo '<p>El servidor no está disponible.</p>';
                            exit();
                        }
                        $q = "SELECT COUNT(*) as contar from Perfiles where Cedula = '$cedula'";
                        $consulta = mysqli_query($conexion, $q);
                        $array = mysqli_fetch_array($consulta);
                        if ($array['contar'] > 0) {
                            $_SESSION['cedula'] = $cedula;
                            $Recuperar->setContra($new);
                            $Recuperar->setUser($usuario);
                            echo '<p>' . $Recuperar->ActualizarContraseña(). '</p>';
                            echo "<script>
                            Swal.fire({
                            icon: 'success',
                            title: 'Felicidades...!',
                            text: 'La contraseña fue actualizada con éxito..!',  
                            })
                            </script>";
                        }else{
                            echo "<script>
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...!',
                            text: 'Perfil no encontrado..!',  
                            })
                            </script>";
                        }
                    } else {
                        if ($new != $conf) {
                            echo "<script>
                            Swal.fire({
                            icon: 'error',
                            title: 'Oops...!',
                            text: ' La contraseña y la confirmación no coinciden..!',  
                            })
                            </script>";
                        }
                      }
                   }
                }
            }
            ?>
        </form>
    </body>
</html>
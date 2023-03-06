<?php
session_start();
?>

<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ecokhemia</title> 
        <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0">
        <link rel="stylesheet" href="css/main.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    </head>  
    <body>
        <form action="frmRegistro.php" class="form-box" method ="POST"> 
            <p><img alt="" width="280" height="135" src="image/Sinfondo.png"></p> 
            <h3 class="form-title">Registrarse</h3>
            <input type="text" placeholder="Nombre Completo" name="usuarioc" id="usuarioc">
            <input type="text" placeholder="Cedula" name="ced" id="ced">
            <input type="text" placeholder="Password" name="passc" id="passc">
            <div>
                <!-- <p> ¿Acepta todos los terminos? </p>
                <input type="checkbox" name="00"> -->
            </div>
            <input type="submit" value="Registrar" name="btverificarc" id="btverificarc">
            <p><a class="" href="./index.php">Volver atras</a></p>
      <?php

                try {
                    echo "<script>
                        Swal.fire({
                                    icon: 'info',
                                    title: 'Importante',
                                    text: 'Hola!',  
                                })
                            </script>";
                } catch (Exception $e) {
                     log_exception($e);
                }
            ?>
            <?php
            echo getcwd();
            require './class/Registro.php';
            try {
            $Registro = new Registro();
            if (isset($_POST['btverificarc'])) {
                if (empty($_POST['usuarioc']) || empty($_POST['passc']) || empty($_POST['ced'])) {
                    echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'Debe digitar todos los Datos.',  
                })
                </script>";
                } else {
                if (strlen(($_POST['passc'])) < 8) {
                            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'La contraseña debe tener un minimo de 8 caracteres..!',  
                })
                </script>";
                        } else {
                            $usuarioc = $_POST['usuarioc'];
                            $ced = $_POST['ced'];
                            $passc = $_POST['passc'];
                            $Registro->setUsuario($usuarioc);
                            $Registro->setCedula($ced);
                            $Registro->setContraseña($passc);
                            echo '<p>' .$Registro->insertarPerfil(). '</p>';
                        }
                }
            }         /* Todo fue OK si llegamos a esta línea */
        } catch (Exception $e) {
                                /* Podemos finalizar la ejecución con un mensaje o mostrar HTML con él */
                                echo sprintf("Error!: %s", $e->getMessage());
                                }
            ?>
        </form>
    </body>
    <footer>
        <div class="footer">
            Developed by: Ticoworks. 2016-2023
        </div>
        <div class="footer-padding"></div>
    </footer>
</html>

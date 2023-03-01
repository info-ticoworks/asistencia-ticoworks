<?php
session_start();
include './config.php';
echo $_SESSION['cedula'];
if(!isset($_SESSION['cedula'])){
    // header ("Location: rediriges a la pagina de logueo". ) 
    header("Location: ./index.php");
}
echo 'Hola';

?>

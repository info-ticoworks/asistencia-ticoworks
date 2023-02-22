<?php
require './config.php';
class Registro {

    private $usuario;
    private $contraseña;
    private $cedula;
    
    public function __construct() {
        $this->usuario = "";
        $this->contraseña = "";
        $this->cedula = "";
    }
    
    public function getUsuario() {
        return $this->usuario;
    }

    public function getContraseña() {
        return $this->contraseña;
    }

    public function getCedula() {
        return $this->cedula;
    }

    public function setUsuario($usuario): void {
        $this->usuario = $usuario;
    }

    public function setContraseña($contraseña): void {
        $this->contraseña = $contraseña;
    }

    public function setCedula($cedula): void {
        $this->cedula = $cedula;
    }

    function insertarPerfil() {
        //$conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");
        if ($conexion->connect_errno) {
            exit();
             echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No se ha podido conectar con el servidor..!',  
                })
                </script>";
                echo '<script>console.log("TicoWorks says: No se pudo conectar")</script>';
        } else {
        echo '<script>console.log("TicoWorks says: Conexión Establecida")</script>';
        }
        
        $comando = $conexion->prepare('INSERT INTO Perfiles(Cedula, Nombre, Password) VALUES (?,?,?);');
        
            echo '<script>console.log("TicoWorks says: Paso 1")</script>';
            if ($comando) {
            echo '<script>console.log("TicoWorks says: Paso 2")</script>';
            $comando->bind_param("sss", $this->cedula, $this->usuario, $this->contraseña);
            $comando->execute();
            echo '<script>console.log("TicoWorks says: Paso 3")</script>';
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
                echo "<script>
                Swal.fire({
                icon: 'success',
                title: 'Felicidades...!',
                text: 'Perfil creado con éxito..!',
                })
                </script>";
            } else {
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'Ya existe un perfil con esos datos..!',  
                })
                </script>";
            }
            $conexion->close();
        } else {
            echo '<script>console.log("TicoWorks says: No se pudo agregar perfil...")</script>';
           $conexion->close();
            exit();
        }
      }
}
<?php
$idEmpresa = $_SESSION['idEmpresa'];
require './config.php';

class Registro {

    private $usuario;
    private $apellido1;
    private $apellido2;
    private $contraseña;
    private $cedula;
    private $telefono;
    private $correo;
    private $tipoUsuario;
    private $wsNotif;
    private $idEmpresa;
   
    public function __construct() {
        $this->usuario = "";
        $this->apellido1 = "";
        $this->apellido2 = "";
        $this->contraseña = "";
        $this->cedula = "";
        $this->telefono = "";
        $this->correo = "";
        $this->tipoUsuario = "";
        $this->wsNotif = "";
        $this->idEmpresa = $_SESSION['idEmpresa'];
    }
    
    public function getUsuario() {
        return $this->usuario;
    }

    public function getApellido1() {
        return $this->apellido1;
    }

    public function getApellido2() {
        return $this->apellido2;
    }

    public function getContraseña() {
        return $this->contraseña;
    }

    public function getCedula() {
        return $this->cedula;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function gettipoUsuario() {
        return $this->tipoUsuario;
    }

    public function getwsNotif() {
        return $this->wsNotif;
    }

    public function setUsuario($usuario): void {
        $this->usuario = $usuario;
    }

    public function setApellido1($apellido1): void {
        $this->apellido1 = $apellido1;
    }

    public function setApellido2($apellido2): void {
        $this->apellido2 = $apellido2;
    }

    public function setContraseña($contraseña): void {
        $this->contraseña = $contraseña;
    }

    public function setCedula($cedula): void {
        $this->cedula = $cedula;
    }

    public function setTelefono($telefono): void {
        $this->telefono = $telefono;
    }

    public function setCorreo($correo): void {
        $this->correo = $correo;
    }

    public function settipoUsuario($tipoUsuario): void {
        $this->tipoUsuario = $tipoUsuario;
    }

    public function setwsNotif($wsNotif): void {
        $this->wsNotif = $wsNotif;
    }

    function insertarPerfil() {
        include './config.php';
        if ($conexion->connect_errno) {
            exit();
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Lo sentimos...!',
                text: 'No se ha podido conectar con el servidor..!',  
            })
            </script>";
            echo '<script>console.log("TicoWorks says: No se pudo conectar")</script>';
        } else {
            echo '<script>console.log("TicoWorks says: Conexión Establecida")</script>';
        }

        $sql = "SELECT * FROM usuarios where cedula = $this->cedula";
        if($result = mysqli_query($conexion, $sql)){
            if(mysqli_num_rows($result) == 0){
                $comando = $conexion->prepare('INSERT INTO usuarios(cedula, nombre, apellido1, apellido2, pass, telefono, correo, idTipoUsuario, wsNotif, idEmpresa) VALUES (?,?,?,?,?,?,?,?,?,?);');
                echo '<script>console.log("TicoWorks says: Paso 1")</script>';
                if ($comando) {
                    echo '<script>console.log("TicoWorks says: Paso 2")</script>';
                    $comando->bind_param("ssssssssss", $this->cedula, $this->usuario, $this->apellido1, $this->apellido2, $this->contraseña, $this->telefono, $this->correo, $this->tipoUsuario, $this->wsNotif, $this->idEmpresa);
                    $comando->execute();
                    echo '<script>console.log("TicoWorks says: Paso 3")</script>';
                    echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Felicidades...!',
                        text: 'Usuario creado con éxito..!',
                    })
                    </script>";
                    $conexion->close();
                } else {
                    echo '<script>console.log("TicoWorks says: No se pudo agregar perfil...")</script>';
                    $conexion->close();
                    exit();
                }
            } else {
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Lo sentimos...!',
                text: 'El usuario con la cédula $this->cedula ya existe..!',
                })
                </script>";
            }
        }
    }
}
?>
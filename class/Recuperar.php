<?php
class Recuperar {
    
    private $contra;
    private $user;
    
    public function __construct() {
        $this->contra = "";
        $this->user = "";
    }

    public function getContra() {
        return $this->contra;
    }

    public function getUser() {
        return $this->user;
    }

    public function setContra($contra): void {
        $this->contra = $contra;
    }

    public function setUser($user): void {
        $this->user = $user;
    }

        function ActualizarContraseña() {
        
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia", 3306);
        
        if ($conexion->connect_errno) {
             echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No se ha podido conectar con el servidor..!',  
                })
                </script>";
            exit();
        }
        
        $instruccionSQL = 'UPDATE Perfiles SET Contraseña = ? WHERE cedula = ?';

        $comando = $conexion->prepare($instruccionSQL);

        if ($comando){
            $comando->bind_param("ss",$this->contra,$this->user);

            $comando->execute();
            $retornar = $conexion->affected_rows;
            $conexion->close();
            return $retornar;
        } else {
             echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible actualizar la base de datos..!',  
                })
                </script>";
            exit();
        }
    }
}
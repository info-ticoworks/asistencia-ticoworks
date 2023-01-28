<?php

class Fecha {
   
   
    private $Fecha;
    
    public function __construct() {
        $this->Fecha = "";
    }

    public function getFecha() {
        return $this->Fecha;
    }

    public function setFecha($Fecha): void {
        $this->Fecha = $Fecha;
    }
    
    
    function insertarFecha() {
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");

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

        $instruccionSQL = 'INSERT INTO Fechas(Fecha) VALUES(?);';
        $comando = $conexion->prepare($instruccionSQL);

        if ($comando) {
           
            $comando->bind_param("s",  $this->Fecha);
            $comando->execute();
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
            } else {
            }
            $conexion->close();
        } else {
            $conexion->close();
            exit();
        }
    }
}


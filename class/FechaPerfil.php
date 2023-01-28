<?php
echo '<script>console.log("Carga de FechaPerfil - Paso 1")</script>';
class FechaPerfil {
    private $user;
    private $date;
    private $ingreso;
    private $salida;
    private $salidaAlmuerzo;
    private $entradaAlmuerzo;
    private $ubicacion;
    
    public function __construct() {
        $this->user = "";
        $this->date = "";
        $this->ingreso = "";
        $this->salida = "";
        $this->salidaAlmuerzo = "";
        $this->entradaAlmuerzo = "";
        $this->ubicacion = "";
    }

    public function getUser() {
        return $this->user;
    }

    public function getDate() {
        return $this->date;
    }

    public function getIngreso() {
        return $this->ingreso;
    }

    public function getSalida() {
        return $this->salida;
    }

    public function getSalidaAlmuerzo() {
        return $this->salidaAlmuerzo;
    }

    public function getEntradaAlmuerzo() {
        return $this->entradaAlmuerzo;
    }

    public function getUbicacion() {
        return $this->ubicacion;
    }

    public function setUser($user): void {
        $this->user = $user;
    }

    public function setDate($date): void {
        $this->date = $date;
    }

    public function setIngreso($ingreso): void {
        $this->ingreso = $ingreso;
    }

    public function setSalida($salida): void {
        $this->salida = $salida;
    }

    public function setSalidaAlmuerzo($salidaAlmuerzo): void {
        $this->salidaAlmuerzo = $salidaAlmuerzo;
    }

    public function setEntradaAlmuerzo($entradaAlmuerzo): void {
        $this->entradaAlmuerzo = $entradaAlmuerzo;
    }

    public function setUbicacion($ubicacion): void {
        $this->ubicacion = $ubicacion;
    }

            
function insertarFechaPerfil() {
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");; 

        if ($conexion->connect_errno) {
            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible conectar con el servidor..!',  
                })
                </script>";
            exit();
        }

        $instruccionSQL = 'INSERT INTO Fechaperfil(Cedula, Fecha, Ubicacion) VALUES(?, ?, ?);';
        $comando = $conexion->prepare($instruccionSQL);

        if ($comando) {
            
            $comando->bind_param("sss",  $this->user, $this->date, $this->ubicacion);
            $comando->execute();
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
            } else {
            }
            $conexion->close();
            

            
        } else {
             echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible añadir el registro en la base de datos..!',  
                })
                </script>";
            $conexion->close();
            exit();
        }
}    

function insertarHoraIngreso() {
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");

        if ($conexion->connect_errno) {
            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible conectar con el servidor..!',  
                })
                </script>";
            exit();
        }

        $instruccionSQL = 'UPDATE Fechaperfil SET HoraIngreso = ? WHERE Fecha = ? AND Cedula = ?';
        $comando = $conexion->prepare($instruccionSQL);

        if ($comando) {
            $comando->bind_param("sss",  $this->ingreso, $this->date, $this->user);
            $comando->execute();
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
            } else {
            }
            $conexion->close();
        } else {
             echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible añadir la hora en la base de datos..!',  
                })
                </script>";
            $conexion->close();
            exit();
        }
    }
    
function insertarHoraSalida() {
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");

        if ($conexion->connect_errno) {
            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible conectar con el servidor..!',  
                })
                </script>";
            exit();
        }

        $instruccionSQL = 'UPDATE Fechaperfil SET HoraSalida = ? WHERE Fecha = ? AND Cedula = ?';
        $comando = $conexion->prepare($instruccionSQL);

        if ($comando) {
            
            $comando->bind_param("sss",  $this->salida,  $this->date, $this->user);
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
    
function insertarHoraSalidaAlmuerzo() {
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");

        if ($conexion->connect_errno) {
            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible conectar con el servidor..!',  
                })
                </script>";
            exit();
        }

        $instruccionSQL = 'UPDATE Fechaperfil SET HoraSalidaAlmuerzo = ? WHERE Fecha = ? AND Cedula = ?';
        $comando = $conexion->prepare($instruccionSQL);

        if ($comando) {
            
            $comando->bind_param("sss",  $this->salidaAlmuerzo,  $this->date, $this->user);
            $comando->execute();
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
            } else {
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'La hora de salida a almuerzo ya ha sido insertada..!',  
                })
                </script>";
            }
            $conexion->close();
        } else {
            $conexion->close();
            exit();
        }
    }
    
    function insertarHoraEntradaAlmuerzo() {
        $conexion = new mysqli("107.180.13.125", "asist-ecok", "CkowfYQ34JJdQ8Um4ILE", "asistencia-ecokhemia");

        if ($conexion->connect_errno) {
            echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'No fue posible conectar con el servidor..!',  
                })
                </script>";
            exit();
        }

        $instruccionSQL = 'UPDATE Fechaperfil SET HoraEntradaAlmuerzo = ? WHERE Fecha = ? AND Cedula = ?';
        $comando = $conexion->prepare($instruccionSQL);

        if ($comando) {
            
            $comando->bind_param("sss",  $this->entradaAlmuerzo,  $this->date, $this->user);
            $comando->execute();
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
            } else {
                echo "<script>
                Swal.fire({
                icon: 'error',
                title: 'Oops...!',
                text: 'La hora de entrada del almuerzo ya ha sido insertada..!',  
                })
                </script>";
            }
            $conexion->close();
        } else {
            $conexion->close();
            exit();
        }
    }
}
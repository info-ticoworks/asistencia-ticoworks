<?php
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

            
function insertarUsuarioIngreso() {
    include './config.php';
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
        $instruccionSQL = 'INSERT INTO marcas(cedula, fecha, ubicacion, idTipoMarca) VALUES(?, ?, ?, 1);';
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

function insertarUsuarioSalida() {
    include './config.php';
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
        $instruccionSQL = 'INSERT INTO marcas(cedula, fecha, ubicacion, idTipoMarca) VALUES(?, ?, ?, 4);';
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

function insertarUsuarioSalidaAlmuerzo() {
    include './config.php';
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
        $instruccionSQL = 'INSERT INTO marcas(cedula, fecha, ubicacion, idTipoMarca) VALUES(?, ?, ?, 2);';
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

function insertarUsuarioEntradaAlmuerzo() {
    include './config.php';
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
        $instruccionSQL = 'INSERT INTO marcas(cedula, fecha, ubicacion, idTipoMarca) VALUES(?, ?, ?, 3);';
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
    include './config.php';
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

        $instruccionSQL = 'UPDATE marcas SET hora = ? WHERE fecha = ? AND cedula = ? AND idTipoMarca = 1;';
        $comando = $conexion->prepare($instruccionSQL);

    if ($comando) {
            $comando->bind_param("sss",  $this->ingreso, $this->date, $this->user);
            $comando->execute();
            if ($conexion->affected_rows > 0) {
                $pk = $conexion->insert_id;
            } else {
            }
            $conexion->close();
            echo '<script>console.log("Paso 2")</script>';
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
    include './config.php';
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

        $instruccionSQL = 'UPDATE marcas SET hora = ? WHERE fecha = ? AND cedula = ? AND idTipoMarca = 4';
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
    include './config.php';
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

        $instruccionSQL = 'UPDATE marcas SET hora = ? WHERE Fecha = ? AND Cedula = ? AND idTipoMarca = 2';
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
        include './config.php';
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

        $instruccionSQL = 'UPDATE marcas SET hora = ? WHERE Fecha = ? AND Cedula = ? AND idTipoMarca = 3';
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
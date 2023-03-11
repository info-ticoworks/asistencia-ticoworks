<?php 
class Conexion{	  
    public static function Conectar() {        
        define('servidor', '51.222.14.197');
        define('nombre_bd', 'AsistenciaTW');
        define('usuario', 'tw-dbusr');
        define('password', '5paE2Tuznc2z7HhhMGR8');					        
        $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');			
        try{
            $conexion = new PDO("mysql:host=".servidor."; dbname=".nombre_bd, usuario, password, $opciones);			
            return $conexion;
        }catch (Exception $e){
            die("El error de Conexión es: ". $e->getMessage());
        }
    }
}
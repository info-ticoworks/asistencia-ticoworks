<?php
function Conexion_pozos(){
    $db=mysql_connect("51.222.14.197","tw-dbusr","5paE2Tuznc2z7HhhMGR8") or die("No se conecto al servidor");
            mysql_select_db("AsistenciaTW",$db) or die ("No se conecto a la base de datos");
            return $db;
}
$dbx=Conexion_pozos();
?>
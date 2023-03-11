<?php 

	class crud{
		public function agregar($datos){
			$obj= new conectar();
			$conexion=$obj->conexion();

			$sql="INSERT into marcas (horaIngreso,horaSalida,cedula)values ('$datos[0]','$datos[1]','$datos[2]')";
			return mysqli_query($conexion,$sql);
		}

		public function obtenDatos($id){
			$obj= new conectar();
			$conexion=$obj->conexion();

			$sql="SELECT id,horaIngreso,horaSalida,cedula from marcas where id='$id'";
			$result=mysqli_query($conexion,$sql);
			$ver=mysqli_fetch_row($result);

			$datos=array(
				'id' => $ver[0],
				'horaIngreso' => $ver[1],
				'horaSalida' => $ver[2],
				'cedula' => $ver[3]
				);
			return $datos;
		}

		public function actualizar($datos){
			$obj= new conectar();
			$conexion=$obj->conexion();

			$sql="UPDATE marcas set horaIngreso='$datos[0]',horaSalida='$datos[1]',cedula='$datos[2]'where id='$datos[3]'";
			return mysqli_query($conexion,$sql);
		}
		
		public function eliminar($id){
			$obj= new conectar();
			$conexion=$obj->conexion();

			$sql="DELETE from marcas where id='$id'";
			return mysqli_query($conexion,$sql);
		}
	}

 ?>
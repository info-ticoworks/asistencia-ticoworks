

<?php 

	class conectar{
		public function conexion(){
			$conexion=mysqli_connect('51.222.14.197',
										'tw-dbusr',
										'5paE2Tuznc2z7HhhMGR8',
										'AsistenciaTW');
			return $conexion;
		}
	}


 ?>
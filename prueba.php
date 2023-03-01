<?php
session_start();
include './config.php';
$cedula = $_SESSION['cedula'];
if(!isset($cedula)){
    // header ("Location: rediriges a la pagina de logueo".)
    header("Location: ./index.php");
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">

<table id="marcas" class="table table-striped" style="width:70%">
			<thead>
        		<tr>
            		<th>ID</th>
            		<th>Entrada</th>
            		<th>Salida a almuerzo</th>
					<th>Entrada después de almuerzo</th>
					<th>Salida</th>
					<th>Cédula</th>
					<th>Fecha</th>
					<th>Ubicación</th>
        		</tr>
			</thead>
			<?php $resultado = mysqli_query($conexion,
				"SELECT * FROM marcas
				inner join usuario on marcas.cedula=usuario.cedula LIMIT 10"
				);
				mysqli_close($conexion);
        		while ($fila = mysqli_fetch_assoc($resultado)) { ?>
				<tbody>
        			<tr>
            			<td><?php echo $fila['id']; ?></td>
            			<td><?php echo $fila['horaIngreso']; ?></td>
            			<td><?php echo $fila['horaSalidaAlmuerzo']; ?></td>
						<td><?php echo $fila['horaEntradaAlmuerzo']; ?></td>
						<td><?php echo $fila['horaSalida']; ?></td>
						<td><?php echo $fila['cedula']; ?></td>
						<td><?php echo $fila['fecha']; ?></td>
						<td><?php echo $fila['ubicacion']; ?></td>
        			</tr>
				</tbody>
        	<?php } ?>
			<tfoot>
            <tr>
				<th>ID</th>
            	<th>Entrada</th>
            	<th>Salida a almuerzo</th>
				<th>Entrada después de almuerzo</th>
				<th>Salida</th>
				<th>Cédula</th>
				<th>Fecha</th>
				<th>Ubicación</th>
            </tr>
        </tfoot>
    	</table>




		<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>


	<script>
		$(document).ready( function () {
    		$('#marcas').DataTable({
				responsive: true,
				ajax: '/get_data.php'
			});
	 	});
	</script>
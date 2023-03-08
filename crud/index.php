





<?php require_once "vistas/parte_superior.php"?>

<!--INICIO del cont principal-->
<div class="container">
    <h1>Tabla de Usuarios</h1>

<?php
include_once './bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT * FROM usuarios inner join tipoUsuario on usuarios.idTipoUsuario=tipoUsuario.idTipoUsuario";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
        <div class="row">
            <div class="col-lg-12">            
            <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal">Nuevo</button>    
            </div>    
        </div>    
    </div>    
    <br>  
<div class="container">
        <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">        
                        <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Primer Apellido</th>                                
                                <th>Segundo Apellido</th>
                                <th>Teléfono</th>
                                <th>E-mail</th>
                                <th>Tipo de Usuario</th>
                                <th>Notificaciones por WhatsApp</th>


                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php                            
                            foreach($data as $dat) {                                                        
                            ?>
                            <tr>
                                <td><?php echo $dat['cedula'] ?></td>
                                <td><?php echo $dat['nombre'] ?></td>
                                <td><?php echo $dat['apellido1'] ?></td>
                                <td><?php echo $dat['apellido2'] ?></td>
                                <td><?php echo $dat['telefono'] ?></td>
                                <td><?php echo $dat['correo'] ?></td>
                                <td><?php echo $dat['nombretipoUsuario'] ?></td>
                                <?php
                                if ($dat['wsNotif'] == 0) {
                                    ?>
                                <td>Sí</td>
                                <?php
                                } else if ($dat['wsNotif'] == 1) {
                                ?>
                                <td>No</td>
                                <?php
                                }
                                ?>    
                                <td></td>
                            </tr>
                            <?php
                                }
                            ?>                                
                        </tbody>        
                       </table>                    
                    </div>
                </div>
        </div>  
    </div>    
      
<!--Modal para CRUD-->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form id="formPersonas">    
            <div class="modal-body">
                <div class="form-group">
                <label for="nombre" class="col-form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre">
                </div>
                <div class="form-group">
                <label for="pais" class="col-form-label">País:</label>
                <input type="text" class="form-control" id="pais">
                </div>                
                <div class="form-group">
                <label for="edad" class="col-form-label">Edad:</label>
                <input type="number" class="form-control" id="edad">
                </div>            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
            </div>
        </form>    
        </div>
    </div>
</div>  
      
    
    
</div>
<!--FIN del cont principal-->

<?php require_once "vistas/parte_inferior.php"?>
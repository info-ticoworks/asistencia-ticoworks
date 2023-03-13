<?php require_once "vistas/parte_superior.php"?>

<!--INICIO del cont principal-->
<div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Lista de Usuarios</h1>
        <p class="mb-4">Detalle de los usuarios actuales</p>
    
    
<!-- Inicio de Tabla -->
<div class="card shadow mb-4">
 <?php
include_once './bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT cedula, nombre, apellido1, apellido2, telefono, correo, nombretipoUsuario, wsNotif, nombreEmpresa FROM usuarios
            inner join tipoUsuario on usuarios.idTipoUsuario=tipoUsuario.idTipoUsuario
            inner join empresa on usuarios.idEmpresa=empresa.idEmpresa
            ORDER BY apellido1 asc";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card-header py-3">
            <div class="col-lg-12">            
            <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal">Nuevo</button>    
            </div>    
    </div>    
<div class="card-body">
                    <div class="table-responsive">        
                        <table id="tablaPersonas" class="table table-bordered" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Primer Apellido</th>                                
                                <th>Segundo Apellido</th>
                                <th>Teléfono</th>
                                <th>E-mail</th>
                                <th>Tipo de Usuario</th>
                                <th>Recibir WhatsApp</th>
                                <th>Empresa</th>
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
                                <td class="text-center"><?php echo $dat['telefono'] ?></td>
                                <td><?php echo $dat['correo'] ?></td>
                                <td><?php echo $dat['nombretipoUsuario'] ?></td>
                                <?php
                                    if ($dat['wsNotif'] == 0) {
                                    ?>
                                    <td class="text-center">No</td>
                                    <?php
                                } else {
                                    ?>
                                    <td class="text-center">Sí</td>
                                    <?php
                                    }
                                ?>
                                <td><?php echo $dat['nombreEmpresa'] ?></td>
                                <td></td>
                            </tr>
                            <?php
                                }
                            ?>                                
                        </tbody>        
                       </table>                    
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
                <label for="newid" class="col-form-label">Cédula: *</label>
                <input type="number" placeholder="Digitar ID tal y como aparece en la cédula." class="form-control" id="newid" required>
                </div>
                <div class="form-group">
                <label for="nombre" class="col-form-label">Nombre: *</label>
                <input type="text" class="form-control" id="nombre" required>
                </div>
                <div class="form-group">
                <label for="apellido1" class="col-form-label">Primer Apellido: *</label>
                <input type="text" class="form-control" id="apellido1" required>
                </div>                
                <div class="form-group">
                <label for="apellido2" class="col-form-label">Segundo Apellido: *</label>
                <input type="text" class="form-control" id="apellido2" required>
                </div>  
                <div class="form-group">
                <label for="pass1" class="col-form-label">Password:</label>
                <input type="password" class="form-control" id="pass1">
                </div>
                <div class="form-group">
                <label for="pass2" class="col-form-label">Repetir password:</label>
                <input type="password" class="form-control" id="pass2">
                </div>
                <div class="form-group">
                <label for="telefono" class="col-form-label">Teléfono: *</label>
                <input type="tel" pattern="[0-9]{8}" placeholder="El número debe ser de 8 dígitos, sin guiones ni espacios*" class="form-control" id="telefono" required>
                </div>      
                <div class="form-group">
                <label for="correo" class="col-form-label">Correo Electrónico:</label>
                <input type="email" pattern="[^@\s]+@[^@\s]+.[^@\s]" placeholder="Formato: usuario@dominio.sufijo" class="form-control" id="correo">
                </div>
                <div class="form-group">
                <label for="nombretipoUsuario" class="col-form-label">Tipo de Usuario: *</label>
                <select class="form-control" id="nombretipoUsuario" required>
                    <option selected disabled>Elija un tipo de usuario.</option>
                    <option name="operario">Operario</option>
                    <option name="supervisor">Supervisor</option>
                    <option name="gerente">Gerente</option>
                    <option name="administrador">Administrador</option>
                </select>
                </div>
                <div class="form-group">
                <label for="wsVerif" class="col-form-label">Recibir notificaciones por WhatsApp:</label>
                <input type="checkbox" class="custom-checkbox" id="wsVerif" name="wsVerif" value="yes">
                </div>
                <div class="form-group">
                <label for="nombreEmpresa" class="col-form-label">Empresa: *</label>
                <select class="form-control" id="nombreEmpresa" required>
                    <option selected disabled>Elija la empresa del usuario.</option>
                    <option name="operario">TicoWorks</option>
                    <option name="supervisor">Ecokhemia</option>
                    <option name="gerente">Regixpro</option>
                </select>
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
<!-- /.container-fluid -->
    
</div>
<!--FIN del cont principal-->

<?php require_once "vistas/parte_inferior.php"?>
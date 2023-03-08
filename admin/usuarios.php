<!-- INICIO DE LA PARTE SUPERIOR DE LA PAGINA... CESAR SEGURA -->
<?php require_once "./vistas/parte_superior.php"?>;

<!-- INICIO DE LA PARTE PRINCIPAL DE LA PAGINA... CESAR SEGURA -->

                <!-- Begin Page Content -->
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

                        $consulta = "SELECT * FROM usuarios inner join tipoUsuario on usuarios.idTipoUsuario=tipoUsuario.idTipoUsuario";
                        $resultado = $conexion->prepare($consulta);
                        $resultado->execute();
                        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="card-body">
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
                                                <th>Notificaciones por WhatsApp</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="text-center">
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
                                        </tfoot>
                                        <tbody class="text-center">
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
                                            <label for="cedula" class="col-form-label">Cédula:</label>
                                            <input type="text" class="form-control" id="cedula">
                                            </div>                                            
                                            <div class="form-group">
                                            <label for="nombre" class="col-form-label">Nombre:</label>
                                            <input type="text" class="form-control" id="nombre">
                                            </div>
                                            <div class="form-group">
                                            <label for="apellido1" class="col-form-label">Primer Apellido:</label>
                                            <input type="text" class="form-control" id="apellido1">
                                            </div>                
                                            <div class="form-group">
                                            <label for="apellido2" class="col-form-label">Segundo Apellido:</label>
                                            <input type="number" class="form-control" id="apellido2">
                                            </div>
                                            <div class="form-group">
                                            <label for="telefono" class="col-form-label">Segundo Apellido:</label>
                                            <input type="number" class="form-control" id="telefono">
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
            <!-- End of Main Content -->

<!-- INICIO DE LA PARTE INFERIOR DE LA PAGINA... CESAR SEGURA -->
<?php require_once "./vistas/parte_inferior.php"?>;

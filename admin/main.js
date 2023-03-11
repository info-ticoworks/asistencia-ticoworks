
$(document).ready(function(){
    console.log("Documento Listo!");
    console.log("Carga de Tabla...");
    tablaPersonas = $("#tablaPersonas").DataTable({
       "columnDefs":[{
        "targets": -1,
        "data":null,
        "defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btnEditar'>Editar</button><button class='btn btn-danger btnBorrar'>Borrar</button></div></div>"  
       }],
        
        //Para cambiar el lenguaje a español
    "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast":"Último",
                "sNext":"Siguiente",
                "sPrevious": "Anterior"
             },
             "sProcessing":"Procesando...",
        }
    });
    
$("#btnNuevo").click(function(){
    console.log("Click en botón de Nuevo...");
    $("#formPersonas").trigger("reset");
    $(".modal-header").css("background-color", "#1cc88a");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Nuevo Usuario");            
    $("#modalCRUD").modal("show"); 
    
 
    id=null;
    opcion = 1; //alta
  
});    
    
var fila; //capturar la fila para editar o borrar el registro
    
//botón EDITAR   
$(document).on("click", ".btnEditar", function(){
    console.log("Click en botón de Editar...");
    try {
    fila = $(this).closest("tr");
    id = fila.find('td:eq(0)').text();
    nombre = fila.find('td:eq(1)').text();
    apellido1 = fila.find('td:eq(2)').text();
    apellido2 = fila.find('td:eq(3)').text();
    pass1 = '';
    pass2 = '';
    telefono = parseInt(fila.find('td:eq(4)').text());
    correo = fila.find('td:eq(5)').text();
    idtipoUsuario = fila.find('td:eq(6)').text();
    wsNotif = fila.find(fila.find('td:eq(7)').text());
    
    $("#id").val(id);
    $("#nombre").val(nombre);
    $("#apellido1").val(apellido1);
    $("#apellido2").val(apellido2);
    $("#pass1").val(pass1);
    $("#pass2").val(pass2);
    $("#telefono").val(telefono);
    $("#correo").val(correo);
    $("#idtipoUsuario").val(idtipoUsuario);
    $("#wsNotif").val(wsNotif);
    console.log("id: ",id);
    console.log("Nombre: ",nombre);
    console.log("Primer Apellido: ",apellido1);
    console.log("Segundo Apellido: ",apellido2);
    console.log("Password 1: ",pass1);
    console.log("Password 2: ",pass2);
    console.log("teléfono: ",telefono);
    console.log("Correo: ",correo);
    console.log("Tipo de Usuario: ",idtipoUsuario);
    console.log("Notificaciones por WhatsApp: ",wsNotif);
    opcion = 2; //editar
    
    $(".modal-header").css("background-color", "#4e73df");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Editar Persona");            
    $("#modalCRUD").modal("show");
} catch (error) {
    console.error(error);
    // Expected output: ReferenceError: nonExistentFunction is not defined
    // (Note: the exact output may be browser-dependent)
} 
});

//botón BORRAR
$(document).on("click", ".btnBorrar", function(){
    console.log("Click en botón de Borrar..."); 
    fila = $(this);
    id = parseInt($(this).closest("tr").find('td:eq(0)').text());
    opcion = 3 //borrar
    console.log("id: ",id);
    var respuesta = confirm("¿Está seguro de eliminar el registro: "+id+"?");
    if(respuesta){
        console.log("Borrado, Paso 1");
        $.ajax({
            url: "bd/crud.php",
            type: "POST",
            dataType: "json",
            data: {opcion:opcion, id:id},
            success: function(){
                tablaPersonas.row(fila.parents('tr')).remove().draw();
            },
            error: function(XMLHttpRequest, textStatus, ReferenceError) { 
                // alert("Status: " + textStatus); alert("Error: " + errorThrown);
                console.log("Status: " + XMLHttpRequest);
                console.log("Status: " + textStatus);
                console.log("Error: " + ReferenceError);
            }
        });
    }
console.log("Final de botón de Borrar...");
});

//botón NUEVO    
$("#formPersonas").submit(function(e){
    e.preventDefault();    
    nombre = $.trim($("#nombre").val());
    apellido1 = $.trim($("#apellido1").val());
    apellido2 = $.trim($("#apellido2").val());
    pass1 = $.trim($("#pass1").val());
    pass2 = $.trim($("#pass2").val());
    telefono = $.trim($("#telefono").val());
    correo = $.trim($("#correo").val());
    idtipoUsuario = $.trim($("#idtipoUsuario").val());
    wsNotif = $.trim($("#wsNotif").val());    
    $.ajax({
        url: "./bd/crud.php",
        type: "POST",
        dataType: "json",
        data: {id:id, nombre:nombre, apellido1:apellido1, apellido2:apellido2, pass1:pass1, pass2:pass2, telefono:telefono, correo:correo, idtipoUsuario:idtipoUsuario, wsNotif:wsNotif, opcion:opcion},
        success: function(data){
            console.log(data);
            id = data[0].id;            
            nombre = data[0].nombre;
            apellido1 = data[0].apellido1;
            apellido2 = data[0].apellido2;
            telefono = data[0].telefono;
            correo = data[0].correo;
            idtipoUsuario = data[0].idtipoUsuario;
            wsNotif = data[0].wsNotif;
            if(opcion == 1){tablaPersonas.row.add([id,nombre,apellido1,apellido2,telefono,correo,idtipoUsuario,wsNotif]).draw();}
            else{tablaPersonas.row(fila).data([id,nombre,apellido1,apellido2,telefono,correo,idtipoUsuario,wsNotif]).draw();}            
        },
        error: function(XMLHttpRequest, textStatus, ReferenceError) { 
            // alert("Status: " + textStatus); alert("Error: " + errorThrown);
            console.log("Status: " + XMLHttpRequest);
            console.log("Status: " + textStatus);
            console.log("Error: " + ReferenceError);
        }      
    });
    $("#modalCRUD").modal("hide");   
    
});    
});


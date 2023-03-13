$(document).ready(function() {
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );

$(document).ready(function(){
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
    $("#formPersonas").trigger("reset");
    $(".modal-header").css("background-color", "#1cc88a");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Nueva Persona");            
    $("#modalCRUD").modal("show");
    //Seteo de algunas opciones al presionar el botón de Nuevo
    document.getElementById('pass1').required = true;
    document.getElementById('pass1').placeholder = 'Campo Obligatorio *';
    document.getElementById('pass2').required = true;
    document.getElementById('pass2').placeholder = 'Campo Obligatorio *';
    document.getElementById('wsVerif').checked = false;
    id=null;
    opcion = 1; //alta
});    
    
var fila; //Capturar la fila para editar o borrar el registro
    
//botón EDITAR    
$(document).on("click", ".btnEditar", function(){
    fila = $(this).closest("tr");
    id = parseInt(fila.find('td:eq(0)').text());
    nombre = fila.find('td:eq(1)').text();
    apellido1 = fila.find('td:eq(2)').text();
    apellido2 = fila.find('td:eq(3)').text();
    pass1 = '';
    pass2 = '';
    telefono = parseInt(fila.find('td:eq(4)').text());
    correo = fila.find('td:eq(5)').text();
    nombretipoUsuario = fila.find('td:eq(6)').text();
    if (fila.find('td:eq(7)').text() == 'Sí') {
        document.getElementById('wsVerif').checked = true;
    } else if (fila.find('td:eq(7)').text() == 'No') {
        document.getElementById('wsVerif').checked = false;
    }
    nombreEmpresa = fila.find('td:eq(8)').text();
    //Seteo de algunas opciones al presionar el botón de Editar
    document.getElementById('pass1').required = false;
    document.getElementById('pass1').placeholder = 'Cambiar contraseña';
    document.getElementById('pass2').required = false;
    document.getElementById('pass2').placeholder = 'Repetir nueva contraseña';

    //Captura de datos para enviar a MySQL
    $("#newid").val(id);
    $("#nombre").val(nombre);
    $("#apellido1").val(apellido1);
    $("#apellido2").val(apellido2);
    $("#pass1").val(pass1);
    $("#pass2").val(pass2);
    $("#telefono").val(telefono);
    $("#correo").val(correo);
    $("#nombretipoUsuario").val(nombretipoUsuario);
    $("#wsVerif.value").val(wsVerif);
    $("#nombreEmpresa").val(nombreEmpresa);
    opcion = 2; //editar
    
    $(".modal-header").css("background-color", "#4e73df");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Editar Persona");            
    $("#modalCRUD").modal("show"); 
    console.log("Edición de Usuario - Paso 1...");
});

//botón BORRAR
$(document).on("click", ".btnBorrar", function(){    
    fila = $(this);
    id = parseInt($(this).closest("tr").find('td:eq(0)').text());
    nombre = $(this).closest("tr").find('td:eq(1)').text();
    apellido1 = $(this).closest("tr").find('td:eq(2)').text();
    opcion = 3 //borrar
    Swal.fire({
        title: 'Está seguro que desea eliminar el usuario '+ nombre +' '+ apellido1 +'?',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        denyButtonText: `Don't save`,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: "bd/crud.php",
                    type: "POST",
                    dataType: "json",
                    data: {opcion:opcion, id:id},
                    success: function(){
                        tablaPersonas.row(fila.parents('tr')).remove().draw();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'El usuario '+ nombre +' '+ apellido1 +' fue eliminado con éxito',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) { 
                        // alert("Status: " + textStatus); alert("Error: " + errorThrown);
                        console.log("Status: " + textStatus);
                        console.log("Error: " + errorThrown);
                    }
                });
            }
      })
});
    
$("#formPersonas").submit(function(e){
    e.preventDefault();
    newid = $.trim($("#newid").val());
    nombre = $.trim($("#nombre").val());
    apellido1 = $.trim($("#apellido1").val());
    apellido2 = $.trim($("#apellido2").val());
    pass1 = $.trim($("#pass1").val());
    pass2 = $.trim($("#pass2").val());
    telefono = $.trim($("#telefono").val());
    correo = $.trim($("#correo").val());
    nombretipoUsuario = $.trim($("#nombretipoUsuario").val());
    wsNotif = '';
    x = document.getElementById("wsVerif").checked;
    if (x == false) {
         wsNotif = 0;
    } else {
         wsNotif = 1;
    }
    nombreEmpresa = $.trim($("#nombreEmpresa").val());

    if (pass1 == pass2){
        $.ajax({
            url: "bd/crud.php",
            type: "POST",
            dataType: "json",
            data: {id:id, newid:newid, nombre:nombre, apellido1:apellido1, apellido2:apellido2, pass1:pass1, pass2:pass2, telefono:telefono, correo:correo, nombretipoUsuario:nombretipoUsuario, wsNotif:wsNotif, nombreEmpresa:nombreEmpresa, opcion:opcion},
            success: function(data){
                //Datos desde el Select de MySQL a la tabla.
                console.log(data);
                id = data[0].cedula;
                nombre = data[0].nombre;
                apellido1 = data[0].apellido1;
                apellido2 = data[0].apellido2;
                telefono = data[0].telefono;
                correo = data[0].correo;
                nombreTipoUsuario = data[0].nombreTipoUsuario;
                wsNotif = data[0].wsNotif;
                if (wsNotif == 0) {
                    wsVerif = 'No';
                } else {
                    wsVerif = 'Sí';
                }
                nombreEmpresa = data[0].nombreEmpresa;
                if(opcion == 1){
                    tablaPersonas.row.add([id,nombre,apellido1,apellido2,telefono,correo,nombretipoUsuario,wsVerif,nombreEmpresa]).draw();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'El usuario '+ nombre +' '+ apellido1 +' fue creado exitosamente',
                        showConfirmButton: false,
                        timer: 2000
                      })
                }else{
                    tablaPersonas.row(fila).data([id,nombre,apellido1,apellido2,telefono,correo,nombretipoUsuario,wsVerif,nombreEmpresa]).draw();
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'El usuario '+ nombre +' '+ apellido1 +' fue editado exitosamente.',
                        showConfirmButton: false,
                        timer: 2000
                      })
                }            
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Ocurrio un error! El ID '+ newid +' ya existe o los datos son erróneos.',
                    footer: '<a href="">Why do I have this issue?</a>',
                    timer: 3000
                })
            }      
        });
        $("#modalCRUD").modal("hide");   

    } else {
        Swal.fire({
            icon: 'error',
            title: 'Lo sentimos...',
            text: 'Las contraseñas no coinciden.',
            footer: 'Favor revisar la información y volver a intentar',
            timer: 3000
        })
    }

 
    
});    
    
});
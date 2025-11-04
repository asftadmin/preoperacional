let tabla;

function init(){
    $("#cond_form").on("submit", function(e){
        guardaryeditar(e);
    });
}

$.post("../../controller/Usuario.php?op=comboUsuarioCond",function(data, status){
    $('#conductor_usuario').html(data);
});

$.post("../../controller/Vehiculo.php?op=comboVehiculo",function(data, status){
    $('#conductor_vehiculo').html(data);
});

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#cond_form")[0]);
 
    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/Conductor.php?op=editarLicencia",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
            console.log(datos);
            var data = JSON.parse(datos);
            $('#cond_form')[0].reset();
            $('#modalcond').modal('hide');
            $('#cond_data').DataTable().ajax.reload();
                swal({
                    title: "Correcto", 
                    text: data.message, 
                    type: "success",
                    confirmButtonClass: "btn-success",
                });
        }
    });
}

$(document).ready(function(){

    tabla=$('#cond_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [		          
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
                ],
        "ajax":{
            url: '../../controller/Conductor.php?op=listarLicencia',
            type : "post",
            dataType : "json",	
            data: tabla,			    		
            error: function(e){
                console.log(e.responseText);	
            }
        },

        
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 7,
        "autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }     
    }).DataTable(); 
});

function editar(cond_id){
    $('#mdltitulo').html('Editar Registro');

    $.post("../../controller/Conductor.php?op=mostrarConductor", { cond_id : cond_id}, function(data) { 
        data = JSON.parse(data);
        $('#cond_id').val(data.cond_id);

        $('#cond_expedicion_licencia').val(data.cond_expedicion_licencia);
        $('#cond_vencimiento_licencia').val(data.cond_vencimiento_licencia);
        $('#conductor_usuario').val(data.conductor_usuario);
        $('#cond_categoria_licencia').val(data.cond_categoria_licencia);
        $('#conductor_vehiculo').val(data.conductor_vehiculo);
    }); 

    $('#modalcond').modal('show');
}

function editarLicencia(cond_id){
    $('#mdltitulo').html('Registrar Licencia');
    $.post("../../controller/Conductor.php?op=mostrarLicencia", { cond_id : cond_id}, function(data) { 
        data = JSON.parse(data);
        $('#cond_id').val(data.cond_id);
        $('#cond_expedicion_licencia').val(data.cond_expedicion_licencia);
        $('#cond_vencimiento_licencia').val(data.cond_vencimiento_licencia);
        $('#conductor_usuario').val(data.conductor_usuario);
        $('#cond_categoria_licencia').val(data.cond_categoria_licencia);
        $('#conductor_vehiculo').val(data.conductor_vehiculo);
    }); 

    $('#modalcond').modal('show');
}

init();
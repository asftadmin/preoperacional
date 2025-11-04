let tabla;

function init(){
    $("#actividad_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevaAct", function(){
    $('#act_id').val('');
    $('#mdltitulo').html('Nueva Actividad');
    $('#actividad_form')[0].reset();
    $('#modalActividad').modal('show');
});

$.post("../../controller/TipoVehiculo.php?op=combotipovehi",function(data, status){
    $('#act_tipo').html(data);
});
function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#actividad_form")[0]);
    let cerrarSwal = true; // Variable booleana para controlar si se debe cerrar el SweetAlert

    // Mostrar el SweetAlert con un spinner
    swal({
        title: "Cargando..",
        text: '<div class="spinner-border text-info" role="status"></div>',
        html: true,
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/Actividades.php?op=guardaryeditaractividad",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
           
           console.log(datos);
           var data = JSON.parse(datos);
            $('#actividad_form')[0].reset();
            $('#modalActividad').modal('hide');
            $('#Act_data').DataTable().ajax.reload();
            if (data.status.trim().toLowerCase()  === "error") {
                swal({
                    title: "Error", 
                    text: data.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                });
            } else {
                swal({
                    title: "Correcto", 
                    text: data.message, 
                    type: "success",
                    confirmButtonClass: "btn-success",
                });
            }
        }
    });
}

$(document).ready(function(){

    $.post("../../controller/Operaciones.php?op=comboOperaciones",function(data, status){
        $('#fallasid_oper').html(data);
    }); 
    

    tabla=$('#Act_data').dataTable({
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
            url: '../../controller/Actividades.php?op=listarActividad',
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


function editar(act_id){
    $('#mdltitulo').html('Editar Actividad');

    $.post("../../controller/Actividades.php?op=mostrarActividad", { act_id : act_id}, function(data) { 
        data = JSON.parse(data);
        $('#act_id').val(data.act_id);
        $('#act_nombre').val(data.act_nombre);
        $('#act_tarifa').val(data.act_tarifa);
        $('#act_unidades').val(data.act_unidades);
        $('#act_tipo').val(data.act_tipo);
        
    }); 

    $('#modalActividad').modal('show');
}

$("#act_tarifa").on({
    "focus": function (event) {
        $(event.target).select();
    },
    "keyup": function (event) {
        $(event.target).val(function (index, value ) {
            return value.replace(/\D/g, "")
                        .replace(/([0-9])([0-9]{3})$/, '$1.$2')
                        .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
        });
    }
});

function eliminar(act_id){
    swal({
        title:"Eliminar Actividad",
        text: "Estas seguro de eliminar la Actividad?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm:false,
    },
    
    function(isConfirm){
        if(isConfirm){
    
        $.post("../../controller/Actividades.php?op=eliminarActividad", { act_id : act_id}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#Act_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Actividad",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Actividad",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}


init();
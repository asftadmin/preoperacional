let tabla;

function init(){
    $("#falla_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevafalla", function(){
    $('#id_fallas').val('');
    $('#mdltitulo').html('Nueva Falla');
    $('#falla_form')[0].reset();
    $('#modalfalla').modal('show');
});

$.post("../../controller/Operaciones.php?op=comboOperaciones",function(data, status){
    $('#oper_id').html(data);
}); 
function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#falla_form")[0]);
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
        url:"../../controller/Fallas.php?op=guardaryeditarfalla",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
           console.log(datos);
           var data = JSON.parse(datos);
            $('#falla_form')[0].reset();
            $('#modalfalla').modal('hide');
            $('#falla_data').DataTable().ajax.reload();
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
    

    tabla=$('#falla_data').dataTable({
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
            url: '../../controller/Fallas.php?op=listarFalla',
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

function editar(id_fallas){
    $('#mdltitulo').html('Editar Falla');

   
    $.post("../../controller/Fallas.php?op=mostrarFallas", { id_fallas : id_fallas}, function(data) { 
        data = JSON.parse(data);
        $('#id_fallas').val(data.id_fallas);
        $('#fallas_nombre').val(data.fallas_nombre);
        $('#fallasid_oper').val(data.fallasid_oper);
        
    }); 

    $('#modalfalla').modal('show');
}

function eliminar(id_fallas){
    swal({
        title:"Eliminar Fallas",
        text: "Estas seguro de eliminar la Falla?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm:false,
    },
    
    function(isConfirm){
        if(isConfirm){
    
        $.post("../../controller/Fallas.php?op=eliminarFalla", { id_fallas : id_fallas}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#falla_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Falla",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Falla",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}


init();
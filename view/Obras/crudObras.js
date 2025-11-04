let tabla;

function init(){
    $("#obras_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevaObra", function(){
    $('#obras_id').val('');
    $('#mdltitulo').html('Nueva Obra');
    $('#obras_form')[0].reset();
    $('#modalObras').modal('show');
    $('#pre_calificar').html('<select class="form-control select2bs4" id="obra_estado" name="obra_estado"><option selected disabled>--Seleccione el estado--</option><option value="1" id="Activa">ACTIVA</option><option value="0" id="Noactiva">NO ACTIVA</option></select>');
    $('#pre_tipo').html('<select class="form-control select2bs4" id="tipo_obra" name="tipo_obra"><option selected disabled>--Seleccione el Tipo--</option><option value="1" id="Asfalto">ASFALTO</option><option value="2" id="Concreto">CONCRETO</option></select>');

});


function guardaryeditar(e){
    
    e.preventDefault();
    let formData = new FormData($("#obras_form")[0]);
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

    /*URL DEL CONTROLADOR OP INSERT - OBRA */
    
    $.ajax({
        url:"../../controller/Obras.php?op=guardaryeditarobras",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
           
           console.log(datos);
           var data = JSON.parse(datos);
            $('#obras_form')[0].reset();
            $('#modalObras').modal('hide');
            $('#obra_data').DataTable().ajax.reload();
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
 
    tabla=$('#obra_data').dataTable({
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
            url: '../../controller/Obras.php?op=listarobras',
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

function editar(obras_id){
    
    $('#mdltitulo').html('Editar Obra');
    $('#pre_calificar').html('<select class="form-control select2bs4" id="obra_estado" name="obra_estado"><option selected disabled>--Seleccione el estado--</option><option value="1" id="Activa">ACTIVA</option><option value="0" id="Noactiva">NO ACTIVA</option></select>');
    $('#pre_tipo').html('<select class="form-control select2bs4" id="tipo_obra" name="tipo_obra"><option selected disabled>--Seleccione el Tipo--</option><option value="1" id="Asfalto">ASFALTO</option><option value="2" id="Concreto">CONCRETO</option></select>');

    $.post("../../controller/Obras.php?op=mostrarobras", { obras_id : obras_id}, function(data) { 
        data = JSON.parse(data);
        console.log(data); // Verifica los valores retornados
        $('#obras_id').val(data.obras_id);
        $('#obras_codigo').val(data.obras_codigo);
        $('#obras_nom').val(data.obras_nom);
        $('#obra_estado').val(data.obra_estado);
        $('#tipo_obra').val(data.tipo_obra);
    }); 

    $('#modalObras').modal('show');
}

function eliminar(obras_id){
    swal({
        title:"Eliminar Obra",
        text: "Estas seguro de eliminar la Obra?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm:false,
    },
    
    function(isConfirm){
        if(isConfirm){
    
        $.post("../../controller/Obras.php?op=eliminarobras", { obras_id : obras_id}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#obra_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Obra",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Obra",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}


init();
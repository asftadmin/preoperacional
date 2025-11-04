
let tabla;

function init(){
  
    $("#tipovehi_form").on("submit", function(e){
        guardaryeditar(e);
    });

}


/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevotipo", function(){
    $('#tipo_id').val('');
    $('#mdltitulo').html('Nuevo Tipo de Vehiculo');
    $('#tipovehi_form')[0].reset();
    $('#modaltipovehi').modal('show');
});

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#tipovehi_form")[0]);
 
    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/TipoVehiculo.php?op=guardaryeditartipovehi",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
            console.log(datos);
            var data = JSON.parse(datos);
            $('#tipovehi_form')[0].reset();
            $('#modaltipovehi').modal('hide');
            $('#tipovehi_data').DataTable().ajax.reload();

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

    tabla=$('#tipovehi_data').dataTable({
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
            url: '../../controller/TipoVehiculo.php?op=listarTipoVehiculo',
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

function editar(tipo_id){
    $('#mdltitulo').html('Editar Tipo de Vehiculo');

    $.post("../../controller/TipoVehiculo.php?op=mostrartipovehi", { tipo_id : tipo_id}, function(data) { 
        data = JSON.parse(data);
        $('#tipo_id').val(data.tipo_id);
        $('#tipo_nombre').val(data.tipo_nombre);

    }); 

    $('#modaltipovehi').modal('show');
}

function eliminar(tipo_id){
    swal({
        title: "Eliminar Tipo de Vehiculo",
        text: "Estas seguro de eliminar el vehiculo?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm:false,
    },
    
    function(isConfirm){
        if(isConfirm){
        

        $.post("../../controller/TipoVehiculo.php?op=eliminartipovehi", { tipo_id : tipo_id}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#tipovehi_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Tipo de Vehiculo",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Tipo de Vehiculo",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}

init();
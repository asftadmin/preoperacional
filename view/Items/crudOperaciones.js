

let tabla;

function init(){
    $("#oper_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevooper", function(){
    $('#oper_id').val('');
    $('#mdltitulo').html('Nueva Operacion');
    
    $('#oper_form')[0].reset();
    $('#modaloper').modal('show');
});


function guardaryeditar(e) {
    e.preventDefault();
    let formData = new FormData($("#oper_form")[0]);
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

    // Llamada AJAX
    $.ajax({
        url: "../../controller/Operaciones.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            console.log(datos);
            $('#oper_form')[0].reset();
            $('#modaloper').modal('hide');
            $('#oper_data').DataTable().ajax.reload();

            swal({
                title: "Correcto",
                text: "Datos guardados correctamente",
                type: "success",
                confirmButtonClass: "btn-success",
            });
            cerrarSwal = false; // No cerrar el SweetAlert en este caso
        }
    });
}






$(document).ready(function(){

    tabla=$('#oper_data').dataTable({
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
            url: '../../controller/Operaciones.php?op=listar',
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

function editar(oper_id){
        $('#mdltitulo').html('Editar Operacion');
        $('#lbestado').html('Estado: *');
        $('#oper_estado').html('<select class="form-control select2bs4" id="oper_estado" name="oper_estado"><option value="1" id="activo">Activo</option><option value="0" id="inactivo">Inactivo</option></select>');

        $.post("../../controller/Operaciones.php?op=mostrar", { oper_id : oper_id}, function(data) { 
            data = JSON.parse(data);
            $('#oper_id').val(data.oper_id);
            $('#oper_nombre').val(data.oper_nombre);
            if (data.oper_estado === 1) {
                $('#activo').prop('selected', true);
            } else {
                $('#inactivo').prop('selected', true);
            }
        }); 

        $('#modaloper').modal('show');
}

function eliminar(oper_id){

        swal({
            title:"Eliminar Operacion",
            text: "Estas seguro de eliminar la Operacion?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
         
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/Operaciones.php?op=eliminar", { oper_id : oper_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#oper_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar Operacion",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar Operacion",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();


let tabla;

function init(){
    $("#mtprm_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevamtprm", function(){
    $('#mtprm_id').val('');
    $('#mdltitulo').html('Nueva Materia');
    $('#lblinea').html('Linea: *');
    $('#mtprm_linea').html('<select class="form-control select2bs4" id="mtprm_linea" name="mtprm_linea"><option value="1" id="concreto">CONCRETO</option><option value="0" id="asfalto">ASFALTO</option><option value="2" id="obra">OBRA</option></select>');
    $('#mtprm_form')[0].reset();
    $('#modalmtprm').modal('show');
});


function guardaryeditar(e) {
    e.preventDefault();
    let formData = new FormData($("#mtprm_form")[0]);
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
        url: "../../controller/MateriaPrima.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            console.log(datos);
            $('#mtprm_form')[0].reset();
            $('#modalmtprm').modal('hide');
            $('#mtprm_data').DataTable().ajax.reload();

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

    tabla=$('#mtprm_data').dataTable({
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
            url: '../../controller/MateriaPrima.php?op=listar',
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

function editar(mtprm_id){
        $('#mdltitulo').html('Editar Materia Prima');
        $('#lblinea').html('Linea: *');
        $('#mtprm_linea').html('<select class="form-control select2bs4" id="mtprm_linea" name="mtprm_linea"><option value="1" id="concreto">CONCRETO</option><option value="0" id="asfalto">ASFALTO</option><option value="2" id="obra">OBRA</option></select>');

        $.post("../../controller/MateriaPrima.php?op=mostrar", { mtprm_id : mtprm_id}, function(data) { 
            data = JSON.parse(data);
            $('#mtprm_id').val(data.mtprm_id);
            $('#mtprm_nombre').val(data.mtprm_nombre);
            if (data.mtprm_linea === 1) {
                $('#concreto').prop('selected', true);
            } else {
                $('#asfalto').prop('selected', true);
            }
        }); 

        $('#modalmtprm').modal('show');
}

function eliminar(mtprm_id){

        swal({
            title:"Eliminar Materia Prima",
            text: "Estas seguro de eliminar la Materia Prima?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
         
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/MateriaPrima.php?op=eliminar", { mtprm_id : mtprm_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#mtprm_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar Materia Prima",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar Materia Prima",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();
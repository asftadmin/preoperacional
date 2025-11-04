let tabla;

function init(){
    $("#permiso_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevopermiso", function(){
    $('#permiso_id').val('');
    $('#mdltitulo').html('Nuevo Permiso');
    $('#permiso_form')[0].reset();
    $('#lbestado').html('Permiso: *');
    $('#permiso').html('<select class="form-control select2bs4" id="permiso" name="permiso"><option value="Si" id="Si">SI</option><option value="No" id="No">NO</option></select>');
    $('#modalpermiso').modal('show');
});

$.post("../../controller/Menu.php?op=comboVistas",function(data, status){
    $('#permiso_menu').html(data);
}); 
$.post("../../controller/Rol.php?op=comboRol_Desp",function(data, status){
    $('#permiso_rol').html(data);
}); 

$(document).ready(function () {
    // Escuchar cuando se muestra el modal
    $("#modalpermiso").on("shown.bs.modal", function () {
      // Inicializar Select2 y evitar problemas de z-index con dropdownParent
      $("#permiso_menu").select2({
        dropdownParent: $("#modalpermiso"),
      });
      $("#permiso_rol").select2({
        dropdownParent: $("#modalpermiso"),
      });
    });
  });
$(document).ready(function(){

    tabla=$('#permiso_data').dataTable({
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
            url: '../../controller/Permiso.php?op=listar',
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
function guardaryeditar(e) {
    e.preventDefault();
    let formData = new FormData($("#permiso_form")[0]);
    let cerrarSwal = true; // Variable booleana para controlar si se debe cerrar el SweetAlert

    $.ajax({
        url:"../../controller/Permiso.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
            console.log(datos);
            var data = JSON.parse(datos);
            $('#permiso_form')[0].reset();
            $('#modalpermiso').modal('hide');
            $('#permiso_data').DataTable().ajax.reload();

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
function editar(permiso_id){
    $('#mdltitulo').html('Editar Permiso');
   
    $.post("../../controller/Permiso.php?op=mostrar", { permiso_id : permiso_id}, function(data) { 
        data = JSON.parse(data);
        $('#permiso_id').val(data.permiso_id);
        $('#permiso_menu').val(data.permiso_menu);
        $('#permiso_rol').val(data.permiso_rol);
        $('#permiso').val(data.permiso);
       
    }); 

    $('#modalpermiso').modal('show');
}

function eliminar(permiso_id){
    swal({
        title:"Eliminar Permiso",
        text: "Estas seguro de eliminar el Permiso?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm:false,
    },
    
    function(isConfirm){
        if(isConfirm){
    
        $.post("../../controller/Permiso.php?op=eliminar", { permiso_id : permiso_id}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#permiso_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Permiso",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Permiso",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}

init();

let tabla;

function init(){
    $("#suboper_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevosuboper", function(){
    $('#suboper_id').val('');
    $('#mdltitulo').html('Nueva SubOperacion');
    $('#suboper_form')[0].reset();
    $('#modalsuboper').modal('show');
});

$(document).ready(function () {
    // Escuchar cuando se muestra el modal
    $("#modalsuboper").on("shown.bs.modal", function () {
      // Inicializar Select2 y evitar problemas de z-index con dropdownParent
      $("#suboper_oper").select2({
        dropdownParent: $("#modalsuboper"),
      });
      $("#suboper_vehi").select2({
        dropdownParent: $("#modalsuboper"),
      });
    });
  });

$.post("../../controller/Operaciones.php?op=comboOperaciones",function(data, status){
    $('#oper_id').html(data);
}); 

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#suboper_form")[0]);
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
        url:"../../controller/Suboperacion.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
           
           console.log(datos);
            $('#suboper_form')[0].reset();
            $('#modalsuboper').modal('hide');
            $('#suboper_data').DataTable().ajax.reload();
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

    $.post("../../controller/Operaciones.php?op=comboOperaciones",function(data, status){
        $('#suboper_oper').html(data);
    }); 

    $.post("../../controller/TipoVehiculo.php?op=combotipovehi",function(data, status){
        $('#suboper_vehi').html(data);
    });
    tabla=$('#suboper_data').dataTable({
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
            url: '../../controller/Suboperacion.php?op=listar',
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



function editar(suboper_id){
        $('#mdltitulo').html('Editar SubOperacion');

        $('#lbestado').html('Estado: *');
        $('#suboper_estado').html('<select class="form-control select2bs4" id="suboper_estado" name="suboper_estado"><option value="1" id="activo">Activo</option><option value="0" id="inactivo">Inactivo</option></select>');

        $.post("../../controller/Suboperacion.php?op=mostrar", { suboper_id : suboper_id}, function(data) { 
            data = JSON.parse(data);
            $('#suboper_id').val(data.suboper_id);
            $('#suboper_oper').val(data.suboper_oper);
            $('#suboper_nombre').val(data.suboper_nombre);
            $('#suboper_vehi').val(data.suboper_vehi);
            if (data.suboper_estado === 1) {
                $('#activo').prop('selected', true);
            } else {
                $('#inactivo').prop('selected', true);
            }
        }); 

        $('#modalsuboper').modal('show');
}

function eliminar(suboper_id){
        swal({
            title:"Eliminar SubOperacion",
            text: "Estas seguro de eliminar la SubOperacion?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/Suboperacion.php?op=eliminar", { suboper_id : suboper_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#suboper_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar SubOperacion",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar    SubOperacion",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();
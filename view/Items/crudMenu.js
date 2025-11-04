let tabla;

function init(){
    $("#menu_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevavista", function(){
    $('#menu_id').val('');
    $('#mdltitulo').html('Nueva Vista');
    $('#menu_form')[0].reset();
    $('#modalmenu').modal('show');
});


function guardaryeditar(e) {
    e.preventDefault();
    let formData = new FormData($("#menu_form")[0]);
    let cerrarSwal = true; // Variable booleana para controlar si se debe cerrar el SweetAlert

    $.ajax({
        url:"../../controller/Menu.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
            console.log(datos);
            var data = JSON.parse(datos);
            $('#menu_form')[0].reset();
            $('#modalmenu').modal('hide');
            $('#menu_data').DataTable().ajax.reload();

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

    tabla=$('#menu_data').dataTable({
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
            url: '../../controller/Menu.php?op=listar',
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

function editar(menu_id){
        $('#mdltitulo').html('Editar Vista');
       
        $.post("../../controller/Menu.php?op=mostrar", { menu_id : menu_id}, function(data) { 
            data = JSON.parse(data);
            $('#menu_id').val(data.menu_id);
            $('#menu_nom').val(data.menu_nom);
            $('#menu_ruta').val(data.menu_ruta);
            $('#menu_estado').val(data.menu_estado);
            $('#menu_icono').val(data.menu_icono);
            $('#menu_identi').val(data.menu_identi);
            $('#menu_grupo').val(data.menu_grupo);
        }); 

        $('#modalmenu').modal('show');
}

function eliminar(menu_id){

        swal({
            title:"Eliminar Vista del Menu",
            text: "Estas seguro de eliminar la Vista?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si",
            cancelButtonText: "No",
            closeOnConfirm:false,
         
        },
        
        function(isConfirm){
            if(isConfirm){
        
            $.post("../../controller/Menu.php?op=eliminar", { menu_id : menu_id}, function(data) { 

            }); 
            /*Recargamos el data table*/
            $('#menu_data').DataTable().ajax.reload();

            swal({
                title: "Eliminar Menu",
                text: "Registro Eliminado",
                type: "success",
                confirmButtonClass: "btn btn-success"
            });

            }else{
                swal({
                    title: "Eliminar Menu",
                    text: "No se elimino el registro",
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                });
            }
        });

}
    

init();
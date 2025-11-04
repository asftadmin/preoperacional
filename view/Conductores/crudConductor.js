let tabla;

function init(){
    $("#cond_form").on("submit", function(e){
        guardaryeditar(e);
    });
}


/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevocond", function(){
    $('#cond_id').val('');
    $('#mdltitulo').html('Asignar Vehiculo');
    $('#cond_form')[0].reset();
    $('#pre_cal').html('<select class="form-control select2bs4" id="rolcond" name="rolcond"><option selected disabled>--Seleccione el Rol--</option><option value="1" id="CondVolqueta">Conductor Volqueta</option><option value="2" id="CondMixer">Conductor Mixer</option><option value="3" id="CondVehiculo">Conductor Vehiculo</option><option value="4" id="OperMaqui">Operador Maquinaria</option></select>');
    $('#modalcond').modal('show');
});

$.post("../../controller/Usuario.php?op=comboUsuarioCond",function(data, status){
    $('#conductor_usuario').html(data);
});

function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#cond_form")[0]);
    formData.append("rolcond", $("#rolcond").val());
    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/Conductor.php?op=guardaryeditarConductor",
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

    tabla=$('#cond_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
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
            url: '../../controller/Conductor.php?op=listarConductor',
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
    $('#pre_cal').html('<select class="form-control select2bs4" id="rolcond" name="rolcond"><option selected disabled>--Seleccione el Rol--</option><option value="1" id="CondVolqueta">Conductor Volqueta</option><option value="2" id="CondMixer">Conductor Mixer</option><option value="3" id="CondVehiculo">Conductor Vehiculo</option><option value="4" id="OperMaqui">Operador Maquinaria</option></select>');


    $.post("../../controller/Conductor.php?op=mostrarConductor", { cond_id : cond_id}, function(data) { 
        data = JSON.parse(data);
        $('#cond_id').val(data.cond_id);
        $('#cond_expedicion_licencia').val(data.cond_expedicion_licencia);
        $('#cond_vencimiento_licencia').val(data.cond_vencimiento_licencia);
        $('#conductor_usuario').val(data.conductor_usuario);
        $('#cond_categoria_licencia').val(data.cond_categoria_licencia);
        $('#rolcond').val(data.rolcond);

    }); 

    $('#modalcond').modal('show');
}



function eliminar(cond_id){
    swal({
        title:"Eliminar",
        text: "Estas seguro de eliminar el Vehiculo Asignado?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm:false,
    },
    
    function(isConfirm){
        if(isConfirm){
    
        $.post("../../controller/Conductor.php?op=eliminarConductor", { cond_id : cond_id}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#cond_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Asignacion del Vehiculo",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Asignacion del Vehiculo",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}


init();
let tabla;

function init() {
    $("#vehiculos_form").on("submit", function(e){
        guardaryeditarcar(e);
    });
 }

var discounted = document.getElementById('vehi_polizaSi');
var no_discounted = document.getElementById('vehi_polizaNo')
var discount_percentage = document.getElementById('vehi_poliza_vence')

function updateStatus() {
  if (discounted.checked) {
    discount_percentage.disabled = false;
    document.getElementById("vehi_poliza_vence").style.display = 'block';
  } else {
    discount_percentage.disabled = true;
    document.getElementById("vehi_poliza_vence").style.display = 'none';
  }
}

discounted.addEventListener('change', updateStatus)
no_discounted.addEventListener('change', updateStatus)

 $.post("../../controller/TipoVehiculo.php?op=combotipovehi",function(data, status){
    $('#vehi_tipo').html(data);
});


 /* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevocar", function(){
    $('#vehi_id').val('');
    $('#mdltitulo').html('Nuevo Vehiculo');
    $('#vehiculos_form')[0].reset();
    $('#modalcar').modal('show');
});



function guardaryeditarcar(e){
    e.preventDefault();
    let formData = new FormData($("#vehiculos_form")[0]);

    $.ajax({
        url:"../../controller/Vehiculo.php?op=guardaryeditarvehiculo",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
            console.log(datos);
            var data = JSON.parse(datos);
            $('#vehiculos_form')[0].reset();
            $('#modalcar').modal('hide');
            $('#car_data').DataTable().ajax.reload();

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

    tabla=$('#car_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Blfrtip',
        "searching": true,
        lengthChange: true,
		"lengthMenu": [[10, 50, 100], [10, 50, 100]],
		pageLength: 10,
        colReorder: true,
        buttons: [		          
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
                ],
        "ajax":{
            url: '../../controller/Vehiculo.php?op=listarVehiculo',
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



function editar(vehi_id){
    $('#mdltitulo').html('Editar Vehiculo');
    
    $.post("../../controller/Vehiculo.php?op=mostrarVehiculo", { vehi_id : vehi_id}, function(data) { 
        data = JSON.parse(data);
        console.log(data.vehi_poliza);

        $('#vehi_id').val(data.vehi_id);
        $('#vehi_marca').val(data.vehi_marca);
        $('#vehi_placa').val(data.vehi_placa);
        $('#vehi_modelo').val(data.vehi_modelo);
        $('#vehi_soat_vence').val(data.vehi_soat_vence);
        $('#vehi_tecnicomecanica').val(data.vehi_tecnicomecanica);
        $('#vehi_tarjeta_propiedad').val(data.vehi_tarjeta_propiedad);
        if (data.vehi_poliza === "Si") {
            $('#vehi_polizaSi').prop('checked', true);
        } else {
            $('#vehi_polizaNo').prop('checked', true);
        }
        $('#vehi_poliza_vence').val(data.vehi_poliza_vence);
        $('#vehi_tipo').val(data.vehi_tipo);
		$('#vehi_costo').val(data.vehi_costo);

    }); 

    $('#modalcar').modal('show');
}


function eliminar(vehi_id){
    swal({
        title:"Eliminar Vehiculo",
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
    
        $.post("../../controller/Vehiculo.php?op=eliminarVehiculo", { vehi_id : vehi_id}, function(data) { 

        }); 
        /*Recargamos el data table*/
        $('#car_data').DataTable().ajax.reload();

        swal({
            title: "Eliminar Vehiculo",
            text: "Registro Eliminado",
            type: "success",
            confirmButtonClass: "btn btn-success"
        });

        }else{
            swal({
                title: "Eliminar Vehiculo",
                text: "No se elimino el registro",
                type: "error",
                confirmButtonClass: "btn btn-danger"
            });
        }
    });

}

init();
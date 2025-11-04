
let tabla;

function init(){

    $("#repdia_form_final").on("submit", function(e){
        kilmetrajeFinal(e);
        console.log("click");
    });
}



$('#btnbuscar').click(function(){

    var repdia_recib =$('#repdia_recib').val(); 
    console.log(repdia_recib); 
    
    tabla=$('#cerrarAct_data').dataTable({
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
            url: '../../controller/VerReporteDiario.php?op=listarActividadesCerrar',
            type : "post",
            dataType : "json",	
            data: {repdia_recib : repdia_recib},			    		
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

function kilmetrajeFinal(e) {
    e.preventDefault();

    let formData = new FormData($("#repdia_form_final")[0]); 
    
   
        $.ajax({
        url:"../../controller/VerReporteDiario.php?op=editarKilo",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        
        success: function(datos) {
            console.log(datos);
            var data = JSON.parse(datos);
            $('#modalKilometraje').modal('hide');
            $('#cerrarAct_data').DataTable().ajax.reload();

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


function editar(repdia_id){
    $('#mdltitulo').html('KM / HR Final');
    
    $.post("../../controller/VerReporteDiario.php?op=mostrarKilo", { repdia_id : repdia_id }, function(data) {
        data = JSON.parse(data); 
        console.log(data.repdia_id);
        $('#repdia_id').val(data.repdia_id);
        $('#repdia_kilo_final').val(data.repdia_kilo_final);

    });

    $('#modalKilometraje').modal('show');
}

init();
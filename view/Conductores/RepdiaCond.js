
function init(){
}

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function (a) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-uk-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

$(document).ready(function(){
    var repdia_cond = $('#user_id').val();
 
    tabla = $('#repdia_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": false,
        lengthChange: false,
        colReorder: true,
        "ajax": {
            url: '../../controller/VerReporteDiario.php?op=listarReporteCond',
            type: "post",
            dataType: "json",
            data: { repdia_cond: repdia_cond }, 
            error: function(e){
                console.log(e.responseText);
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 1 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[1, "desc"]], // Ordenar la columna de fechas en orden descendente
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 5,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    }).DataTable(); 
});

function verRepdia_cond(repdia_recib) {
    console.log(repdia_recib);
    window.location.href = BASE_URL +'/view/ConsultarReportesDiarios/Detalle.php?ID=' + repdia_recib; //http://181.204.219.154:3396/preoperacional 
}

function firma(repdia_recib) {
    $('#mdltitulo').html('Firma Autorizado');

    $.post("../../controller/VerReporteDiario.php?op=mostrarReporte", { repdia_recib: repdia_recib }, function(data) {
        data = JSON.parse(data);

        if (data.error) {
            console.error(data.error);
            return;
        }

        // Mostrar los valores en el modal
        $('#repdia_recib').val(data.repdia_recib);

        // Obtener el contexto del canvas
        const canvas = document.getElementById('canvas'); // Cambia 'tuCanvasId' por el ID real de tu canvas
        const contexto = canvas.getContext('2d');

        if (data.repdia_firma) {
            // Mostrar la firma en el canvas
            const img = new Image();
            img.onload = function() {
                // Limpiar el canvas antes de dibujar
                contexto.clearRect(0, 0, canvas.width, canvas.height);

                // Dibujar la imagen en el canvas
                contexto.drawImage(img, 0, 0);
            };

            img.src = data.repdia_firma; // El Base64 de la firma
        } else {
            // Limpiar el canvas si no hay firma
            contexto.clearRect(0, 0, canvas.width, canvas.height);
        }

        $('#modalFirma').modal('show');
    });
}



init();
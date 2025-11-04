let tabla;
function init(){

}

function goBack() {
    history.back();
}

$(document).ready(function () {
    let repdia_placa = getURLParameter('ID');
    console.log(repdia_placa);

    tabla = $('#Frsd_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": false,
        "paging": false, // Deshabilitar la paginación
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7,
        "autoWidth": false,
        "language": {
            // Configuración de idioma
        },
        "ajax": {
            url: "../../controller/Finalizar.php?op=detalleGraficoFrsd",
            type: "POST",
            data: { repdia_placa: repdia_placa },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();

    $.ajax({
        url: "../../controller/Finalizar.php?op=datosFrsd",
        method: "POST",
        data: { repdia_placa: repdia_placa },
        dataType: "json",
        success: function (data) {
            $("#vehi_placa").val(data.vehi_placa);
            $("#repdia_fech").val(data.repdia_fech);
            $("#tipo_nombre").val(data.tipo_nombre);
            $("#repdia_placa").val(data.repdia_placa);
            
        }
    });

});
  

/*Catuptura el paramtro id */
var getURLParameter = function(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&'); // Corregido el nombre de la variable
    var sParameterName;
    for (var i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}


init();
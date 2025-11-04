let tabla;
function init(){

}

$('#btnvolver').click(function (vehi_id) {
    var vehi_id = $("#vehi_id").val();
    console.log(vehi_id);
    window.location.href = BASE_URL +'/view/Graficos/TablaGrafico.php?ID=' + vehi_id; //http://181.204.219.154:3396/preoperacional
  });

$(document).ready(function () {
    let repdia_placa = getURLParameter('ID');
    console.log(repdia_placa);

    tabla = $('#detalle_data').dataTable({
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
            url: "../../controller/Finalizar.php?op=detalleGraficoVolqueta",
            type: "POST",
            data: { repdia_placa: repdia_placa },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();

    $.ajax({
        url: "../../controller/Finalizar.php?op=datos",
        method: "POST",
        data: { repdia_placa: repdia_placa },
        dataType: "json",
        success: function (data) {
            $("#vehi_placa").val(data.vehi_placa);
            $("#repdia_fech").val(data.repdia_fech);
            $("#vehi_id").val(data.vehi_id);
            
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
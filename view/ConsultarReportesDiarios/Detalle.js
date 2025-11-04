let tabla;
function init(){

}
function goBack() {
    history.back();
}
$(document).ready(function () {
    let repdia_recib = getURLParameter('ID');
    console.log(repdia_recib);

    tabla = $('#detalle_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
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
            url: "../../controller/VerReporteDiario.php?op=detalle",
            type: "POST",
            data: { repdia_recib: repdia_recib },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();
 


    $.ajax({
        url: "../../controller/VerReporteDiario.php?op=datos",
        method: "POST",
        data: { repdia_recib: repdia_recib },
        dataType: "json",
        success: function (data) {
            $("#user_cedula").val(data.user_cedula);
            $("#conductor_nombre_completo").val(data.conductor_nombre_completo);
            $("#repdia_fech").val(data.repdia_fech);
            $("#repdia_recib").val(data.repdia_recib);
                       
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
let tabla;
function init(){
}
function goBack() {
    history.back();
}
$(document).ready(function () {
    let vehi_id = getURLParameter('ID');
    console.log(vehi_id);
    let repdia_obras = getURLParameter('repdia_obras');
    console.log(repdia_obras);
    let fecha_inicio = getURLParameter("fecha_inicio");
    console.log(fecha_inicio);
    let fecha_final = getURLParameter("fecha_final");
    console.log(fecha_final);

    tabla = $('#detalleCombustible_data').dataTable({
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
        "columnDefs": [
            { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[0, "desc"]],
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 5,
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
        } ,
        "ajax": {
            url: "../../controller/VerReporteDiario.php?op=detalleConsumibles",
            type: "POST",
            data: { vehi_id : vehi_id, repdia_obras : repdia_obras, fecha_inicio : fecha_inicio, fecha_final : fecha_final  },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();

    $.ajax({
        url: "../../controller/VerReporteDiario.php?op=datosConsumibles",
        method: "POST",
        data: { vehi_id : vehi_id, repdia_obras : repdia_obras, fecha_inicio : fecha_inicio, fecha_final : fecha_final},
        dataType: "json",
        success: function (data) {
            $("#vehi_placa").val(data.vehi_placa);
            $("#tipo_nombre").val(data.tipo_nombre);
            $("#obras_nom").val(data.obras_nom);
        }
    });
});
  
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
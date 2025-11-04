let tabla;
function init(){

}
function goBack() {
    history.back();
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

$(document).ready(function () {
    let vehi_id = getURLParameter('ID');
    console.log(vehi_id);

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
            url: "../../controller/Vehiculo.php?op=detalleMaquinaria",
            type: "POST",
            data: { vehi_id: vehi_id },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[0, "desc"]],
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
    });
 


    $.ajax({
        url: "../../controller/Vehiculo.php?op=datos",
        method: "POST",
        data: { vehi_id: vehi_id },
        dataType: "json",
        success: function (data) {
            $("#tipo_nombre").val(data.tipo_nombre);
            $("#vehi_placa").val(data.vehi_placa);
            $("#vehi_estado").val(data.vehi_estado);
            
           
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
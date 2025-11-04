let tabla;
function init(){

}
function goBack() {
    history.back();
}
$(document).ready(function () {
    let pre_formulario = getURLParameter('ID');
    console.log(pre_formulario);

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
            url: "../../controller/VerPreoperacional.php?op=detalle",
            type: "POST",
            data: { pre_formulario: pre_formulario },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();
 


    $.ajax({
        url: "../../controller/VerPreoperacional.php?op=datos",
        method: "POST",
        data: { pre_formulario: pre_formulario },
        dataType: "json",
        success: function (data) {
            $("#user_cedula").val(data.user_cedula);
            $("#conductor_nombre_completo").val(data.conductor_nombre_completo);
            $("#vehi_placa").val(data.vehi_placa);
            $("#tipo_nombre").val(data.tipo_nombre);
            $("#pre_observaciones").val(data.pre_observaciones);
            $("#pre_kilometraje_inicial").val(data.pre_kilometraje_inicial);
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

$(document).ready(function () {
    let pre_fallas = getURLParameter('ID');
    console.log(pre_fallas);

    tabla = $('#detalleForm_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
       
        "searching": false,
        "paging": false, // Deshabilitar la paginación
        lengthChange: false,
        colReorder: true,
        
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7,
        "autoWidth": false,
        "language": {
            // Configuración de idioma
        },
        "ajax": {
            url: "../../controller/VerPreoperacional.php?op=detalleForm",
            type: "POST",
            data: { pre_fallas: pre_fallas },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();

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
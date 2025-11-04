let tabla;
function init(){

}
function goBack() {
    history.back();
}
$(document).ready(function () {
    let alista_codigo = getURLParameter('ID');
    console.log(alista_codigo);

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
            url: "../../controller/VerAlistamiento.php?op=detalle",
            type: "POST",
            data: { alista_codigo: alista_codigo },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            },
        }
    }).DataTable();
 


    $.ajax({
        url: "../../controller/VerAlistamiento.php?op=datos",
        method: "POST",
        data: { alista_codigo: alista_codigo },
        dataType: "json",
        success: function (data) {
            $("#alista_fecha").val(data.alista_fecha);
            $("#obras_nom").val(data.obras_nom);
            $("#residente_nombre_completo").val(data.residente_nombre_completo);
            $("#alista_observaciones").val(data.alista_observaciones);
            $("#cedula_inspec").val(data.cedula_inspec);
            $("#inspector_nombre_completo").val(data.inspector_nombre_completo);
            
            // Condición para el nombre del conductor
            if (data.conductor_nombre_completo) {
                $("#conductor_nombre_completo").val(data.conductor_nombre_completo);
            } else {
                $("#conductor_nombre_completo").val(""); // Espacio en blanco si no hay conductor registrado
            }
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
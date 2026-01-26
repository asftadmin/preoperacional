// Función para obtener el parámetro de la URL
var getURLParameter = function (sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&');
    var sParameterName;
    for (var i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

//Adquirir datos para el detalle ticket
$(document).ready(function () {
    var reporte_id = getURLParameter('id');

    if (!reporte_id) {
        alert('No se encontró el ID del ticket en la URL');
        return;
    }

    $.ajax({
        url: "../../controller/ReporteMtto.php?op=detalleReporte",
        method: "POST",
        data: { id: reporte_id },
        dataType: "json", // Esperamos una respuesta JSON
        success: function (data) {
            console.log("Respuesta completa:", data);

            if (data.status === 'success') {
                $('#readMessage').html(data.html);
            } else {
                alert(data.message || 'Error desconocido');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            alert("Error al cargar los detalles del ticket");
        }
    });

});


function importarRepuestos() {

    let idVehiculo = $("#id_vehiculo").val();
    let idReporte = $("#id_reporte").val();

    if (!idVehiculo || !idReporte) {
        Swal.fire("Error", "No se encontró información del reporte o vehículo.", "error");
        return;
    }

    // Guardar valores dentro del modal
    $("#modal_id_vehiculo").val(idVehiculo);
    $("#modal_id_reporte").val(idReporte);
    $("#modal_text_vehiculo").val(idVehiculo);

    // Abrir modal
    $("#modalImportar").modal("show");

    // Cargar items del mes actual automáticamente
    //cargarItemsSiesa();
}


$(function () {
    $('#modal_rango_fechas').daterangepicker({
        locale: { format: 'YYYY-MM-DD' },
        opens: 'right'
    });
});


function cargarItemsSiesa() {

    let vehiculo = $("#modal_id_vehiculo").val();
    let fechas = $("#modal_rango_fechas").val();

    $.ajax({
        url: "../../controller/ReporteMtto.php?op=importar_siesa",
        type: "POST",
        data: { vehiculo: vehiculo, fechas: fechas },
        dataType: "json",
        success: function (data) {

            if (data.status !== "success") {
                Swal.fire("Error", data.message, "error");
                return;
            }

            let html = "";

            data.items.forEach(item => {
                html += `
                    <tr>
                        <td><input type="checkbox" class="checkItem" value="${item.id}" data-json='${JSON.stringify(item)}'></td>
                        <td>${item.documento}</td>
                        <td>${item.descripcion}</td>
                        <td>${item.cantidad}</td>
                        <td>$${item.valor}</td>
                    </tr>`;
            });

            $("#tablaSiesa tbody").html(html);
        }
    });
}



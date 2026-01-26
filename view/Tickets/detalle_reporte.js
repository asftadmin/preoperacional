var tablaSiesa;
var tablaRepuestos = null;

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

//Funcion para abrir el modal

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

    moment.locale('es'); // español global

    $('#modal_rango_fechas').daterangepicker(
        {
            locale: {
                format: "YYYY-MM-DD",
                separator: " / ",
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar",
                fromLabel: "Desde",
                toLabel: "Hasta",
                customRangeLabel: "Personalizado",
                weekLabel: "S",
                daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                monthNames: [
                    "Enero", "Febrero", "Marzo", "Abril",
                    "Mayo", "Junio", "Julio", "Agosto",
                    "Septiembre", "Octubre", "Noviembre", "Diciembre"
                ],
                firstDay: 1
            },

            // Opciones disponibles
            autoApply: false,               // aplica automáticamente
            showDropdowns: true,            // desplegar meses/años
            showWeekNumbers: false,         // semanas del año
            autoUpdateInput: true,          // actualiza el input
            opens: "right",                 // posición del calendario
            drops: "down",                  // hacia abajo

            startDate: moment().startOf("month"),
            endDate: moment().endOf("month"),

            ranges: {
                "Hoy": [moment(), moment()],
                "Ayer": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Últimos 7 días": [moment().subtract(6, "days"), moment()],
                "Últimos 30 días": [moment().subtract(29, "days"), moment()],
                "Este mes": [moment().startOf("month"), moment().endOf("month")],
                "Mes pasado": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month")
                ],
                "Este año": [moment().startOf("year"), moment().endOf("year")],
                "Año pasado": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year")
                ]
            }
        }
    );

});

//funcion para inicializar tabla de Items Siesa
$(document).ready(function () {

    tablaSiesa = $("#tablaSiesa").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 10,
        autoWidth: false,
        responsive: {
            details: {
                type: "column",
                target: 0
            }
        },
        order: [[3, "asc"]],
        scrollX: false,

        language: {
            decimal: ",",
            thousands: ".",
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna ascendente",
                sortDescending: ": activar para ordenar la columna descendente"
            }
        },

        columnDefs: [
            { className: "control", orderable: false, targets: 0 },
            { orderable: false, targets: 1 },
            {
                targets: [4, 7],   // columnas que se ocultarán primero
                responsivePriority: 1
            },
            {
                targets: [2, 3],
                responsivePriority: 2
            },
            { visible: false, targets: [8, 9] }

        ]
    });

    $('#modalImportar').on('shown.bs.modal', function () {
        tablaSiesa.columns.adjust().responsive.recalc();
    });

})


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

            // LIMPIAR TABLA
            tablaSiesa.clear();

            // AGREGAR FILAS
            data.items.forEach(item => {

                tablaSiesa.row.add([
                    "",
                    `
                    <input type="checkbox" class="checkItem" 
                        value="${item.id}" 
                        data-json='${JSON.stringify(item)}'
                    >
                    `,
                    item.fecha.substring(0, 10),
                    item.documento,
                    item.descripcion,
                    item.cantidad,
                    "$" + Number(item.valor).toLocaleString(),
                    item.notas,
                    item.proveedor,
                    item.referencia
                ]);

            });

            tablaSiesa.draw(); // refrescar tabla
        }
    });
}

function importarSeleccionados() {

    let idReporte = $("#modal_id_reporte").val();
    let seleccionados = [];

    $(".checkItem:checked").each(function () {
        seleccionados.push($(this).data("json"));
    });

    if (seleccionados.length === 0) {
        Swal.fire("Advertencia", "Seleccione al menos un ítem para importar", "warning");
        return;
    }

    $.ajax({
        url: "../../controller/ReporteMtto.php?op=guardar_insumos",
        type: "POST",
        data: {
            reporte: idReporte,
            items: JSON.stringify(seleccionados)
        },
        dataType: "json",

        success: function (data) {

            if (data.status === "success") {

                Swal.fire({
                    icon: "success",
                    title: "Importación exitosa",
                    text: "Los repuestos fueron agregados al reporte.",
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();   // ⬅️ REFRESCAR LA PÁGINA
                });

            } else {
                Swal.fire("Error", data.message, "error");
            }
        },

        error: function (xhr) {
            Swal.fire("Error", "No se pudo importar los ítems", "error");
        }
    });
}

function eliminarRepuesto(idItem) {


    Swal.fire({
        title: "¿Eliminar repuesto?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "../../controller/ReporteMtto.php?op=delete_item",
                type: "POST",
                data: { id: idItem },
                dataType: "json",

                success: function (data) {
                    if (data.status === "success") {

                        Swal.fire({
                            icon: "success",
                            title: "Eliminado",
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();   // ⬅️ REFRESCA LA PÁGINA
                        });

                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                },

                error: function () {
                    Swal.fire("Error", "No se pudo eliminar el repuesto.", "error");
                }
            });
        }
    });
}






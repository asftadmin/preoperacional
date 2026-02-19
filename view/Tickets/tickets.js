let tablaOpen = null;
let tablaRevision = null;
let tablaCerrados = null;
function init() {

    inicializarTabla();
    inicializarTablaR();
    inicializarTablaC();

    initSelect2Dynamic();

    $("#orden_form_close").on("submit", function (e) {
        cerrarOrdenTrabajo(e);
    });

    $("#solicitud_form").on("submit", function (e) {
        guardarSolicitud(e);
    });

    

}

// Botones de carpeta
$('#abiertos-btn').on('click', function () {
    cargarSolicitudes('Open');
    if (tablaRevision) tablaRevision.clear().destroy();
});

$('#revision-btn').on('click', function () {
    cargarSolicitudesR('Revision');
    if (tablaOpen) tablaOpen.clear().destroy();
});

$('#cerrados-btn').on('click', function () {
    cargarSolicitudesC('Cerrados');
    if (tablaCerrados) tablaCerrados.clear().destroy();
});

function initSelect2Dynamic() {

    $(".select2bs4").each(function () {

        let $select = $(this);

        // Evitar inicialización doble
        if ($select.hasClass("select2-hidden-accessible")) {
            return;
        }

        // Detectar si estamos dentro de algún modal
        let modalPadre = $select.closest(".modal");

        $select.select2({
            theme: "bootstrap4",
            width: "100%",
            dropdownParent: modalPadre.length ? modalPadre : $(document.body)
        });

    });
}

$('.modal').on('shown.bs.modal', function () {
    initSelect2Dynamic();
});


function inicializarTabla() {
    tablaOpen = $('#tableTickets').DataTable({
        destroy: true, // permite reiniciar sin conflictos
        processing: true,
        serverSide: false, // tu backend ya devuelve todo
        searching: false,
        lengthChange: false,
        responsive: true,
        pageLength: 7,
        autoWidth: false,
        language: {
            sProcessing: "Procesando...",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando un total de 0 registros",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        },
        ajax: {
            url: '../../controller/tickets.php?op=listarTicketsOpen',
            type: "POST",
            dataType: "json",
            dataSrc: function (json) {
                console.log("Respuesta del servidor:", json);
                if (json && json.aaData) {
                    // convertir las filas [[],[],[]] en objetos
                    return json.aaData.map(function (r) {
                        return {
                            no_solicitud: r[0],
                            placa: r[1],
                            fecha: r[2],
                            acciones: r[3]
                        };
                    });
                }
                return [];
            },
            error: function (xhr) {
                console.error("Error en AJAX:", xhr.responseText);
            }
        },
        columns: [
            { data: "no_solicitud", title: "No.Solicitud" },
            { data: "placa", title: "Placa" },
            { data: "fecha", title: "Fecha" },
            { data: "acciones", title: "Acciones" }
        ]
    });
}


function cargarSolicitudes(tipo = 'Open') {
    const nuevaURL = `../../controller/tickets.php?op=listarTickets${tipo}`;
    if (tablaOpen) {
        tablaOpen.ajax.url(nuevaURL).load(); // recarga datos sin reiniciar tabla
    } else {
        inicializarTabla();
    }
}

function cargarSolicitudesR(tipo = 'Revision') {
    const nuevaURL = `../../controller/tickets.php?op=listarTickets${tipo}`;
    if (tablaRevision) {
        tablaRevision.ajax.url(nuevaURL).load(); // recarga datos sin reiniciar tabla
    } else {
        inicializarTablaR();
    }
}

function cargarSolicitudesC(tipo = 'Cerrados') {
    const nuevaURL = `../../controller/tickets.php?op=listarTickets${tipo}`;
    if (tablaCerrados) {
        tablaCerrados.ajax.url(nuevaURL).load(); // recarga datos sin reiniciar tabla
    } else {
        inicializarTablaC();
    }
}

function inicializarTablaR() {
    tablaRevision = $('#tableTktRev').DataTable({
        destroy: true, // permite reiniciar sin conflictos
        processing: true,
        serverSide: false, // tu backend ya devuelve todo
        searching: false,
        lengthChange: false,
        responsive: true,
        pageLength: 7,
        autoWidth: false,
        language: {
            sProcessing: "Procesando...",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando un total de 0 registros",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        },
        ajax: {
            url: '../../controller/tickets.php?op=listarTicketsRevision',
            type: "POST",
            data: function (d) {
                d.placa = $("#filtroPlaca").val();
                d.fechas = $("#filtroFecha").val();
            },
            dataType: "json",
            dataSrc: function (json) {
                console.log("Respuesta del servidor:", json);
                if (json && json.aaData) {
                    // convertir las filas [[],[],[]] en objetos
                    return json.aaData.map(function (r) {
                        return {
                            no_orden: r[0],
                            no_solicitud: r[1],
                            placa: r[2],
                            fecha: r[3],
                            tipo: r[4],
                            estado: r[5],
                            acciones: r[6]
                        };
                    });
                }
                return [];
            },
            error: function (xhr) {
                console.error("Error en AJAX:", xhr.responseText);
            }
        },
        columns: [
            { data: "no_orden", title: "No.Orden" },
            { data: "no_solicitud", title: "No.Solicitud" },
            { data: "placa", title: "Placa" },
            { data: "fecha", title: "Fecha Solc." },
            { data: "tipo", title: "Tipo Mant." },
            { data: "estado", title: "Estado" },
            { data: "acciones", title: "Acciones" }
        ]
    });
}

function inicializarTablaC() {
    tablaCerrados = $('#tableTktCerrado').DataTable({
        destroy: true, // permite reiniciar sin conflictos
        processing: true,
        serverSide: false, // tu backend ya devuelve todo
        searching: false,
        lengthChange: false,
        responsive: true,
        pageLength: 7,
        autoWidth: false,
        language: {
            sProcessing: "Procesando...",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando un total de 0 registros",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        },
        ajax: {
            url: '../../controller/tickets.php?op=listarTicketsCerrados',
            type: "POST",
            data: function (d) {
                d.placa = $("#filtroPlaca").val();
                d.fechas = $("#filtroFecha").val();
            },
            dataType: "json",
            dataSrc: function (json) {
                console.log("Respuesta del servidor:", json);
                if (json && json.aaData) {
                    // convertir las filas [[],[],[]] en objetos
                    return json.aaData.map(function (r) {
                        return {
                            no_reporte: r[0],
                            no_orden: r[1],
                            placa: r[2],
                            fecha_solicitud: r[3],
                            estado: r[4],
                            estado_num: r[5],
                            acciones: r[6]
                        };
                    });
                }
                return [];
            },
            error: function (xhr) {
                console.error("Error en AJAX:", xhr.responseText);
            }
        },
        columns: [
            { data: "no_reporte", title: "No.Orden" },
            { data: "no_orden", title: "No.Solicitud" },
            { data: "placa", title: "Placa" },
            { data: "fecha_solicitud", title: "Fecha Solc." },
            { data: "estado", title: "Estado" },
            { data: "estado_num", visible: false },
            { data: "acciones", title: "Acciones" }
        ],
        columnDefs: [
            { targets: 5, visible: false }, // ← oculta la columna estado_num
        ]
    });
    $('#tableTktCerrado').on('draw.dt', function () {

        $('#tableTktCerrado tbody tr').each(function () {

            let rowData = tablaCerrados.row(this).data();

            // Validar que rowData exista
            if (!rowData) return;

            let estado = rowData.estado_num;

            let botonPDF = $(this).find(".btn-pdf");

            if (estado != 2) {
                botonPDF.prop("disabled", true).css("opacity", "0.4");
            }
        });

    });

}




function verDetalleTicket(ticketID) {

    $.ajax({
        url: '../../controller/tickets.php?op=detalleTicket',
        type: 'GET',
        data: { id: ticketID },
        success: function (response) {
            const detalle = JSON.parse(response);

            if (detalle.status && detalle.status === 'error') {
                $('#readMessage').html(detalle.html);
                Swal.fire('Error', detalle.message, 'error');
            } else {

                $('#readMessage').html(detalle.html);

                // Opcional: Actualizar la URL sin recargar (HTML5 History API)
                history.pushState(null, null, `?id=${ticketID}`);

            }
        },
        error: function (xhr, status, error) {
            Swal.fire('Error', 'Hubo un error al obtener los detalles: ' + error, 'error');
        }

    });
}

function verOTM(codi_otm) {
    console.log(codi_otm);
    var url = BASE_URL + '/view/PDF/OrdenesTrabajo.php?ID=' + codi_otm;
    window.open(url, '_blank');
}



function ver(ticketID) {
    console.log(ticketID);
    window.location.href = BASE_URL + '/view/tickets/detalle_ticket.php?id=' + ticketID; //http://181.204.219.154:3396/preoperacional
}

// Manejar el botón "Volver" (si lo implementas)
$(document).on('click', '.btn-volver-tickets', function () {
    $('#readMessage').html(`
        <div class="text-center py-5">
            <i class="fas fa-ticket-alt fa-4x text-muted"></i>
            <h4 class="mt-3">Selecciona un ticket para ver su detalle</h4>
        </div>
    `);
    history.pushState(null, null, 'tickets.php');
});

$(document).ready(function () {
    $('#btnCrearOrden').on('click', function () {
        let solicitud = $('#ticket_id').val();
        $('#mdltitulo').html('No. Solicitud: ' + solicitud);
        $('#modalOrdenMtto').modal('show');  // Abrir el modal
    });

});

function cerrarOTM(ticketID) {
    $('#mdltitulo').html('Cerrar Orden de Trabajo');
    $.post("../../controller/OrdenTrabajo.php?op=verOrdenes", { ticketID: ticketID }, function (data) {
        data = JSON.parse(data);
        $('#codi_orden').val(data.codi_otm);
        $('#num_orden').val(data.num_otm);

    });
    $('#modalCerrarOrden').modal('show');
}

// Mostrar/Ocultar campo SIESA automáticamente
$('input[name="requiere_compra"]').on('change', function () {

    if ($(this).val() == "1") {
        $("#campo_siesa").slideDown();
        $("#num_siesa").attr("required", true);
    } else {
        $("#campo_siesa").slideUp();
        $("#num_siesa").val("");
        $("#num_siesa").attr("required", false);
    }
});

$('input[name="equipo_operativo"]').on('change', function () {

    if ($(this).val() == "2") {
        $("#campo_observaciones").slideDown();
        $("#observaciones_pdtes").attr("required", true);
    } else {
        $("#campo_observaciones").slideUp();
        $("#observaciones_pdtes").val("");
        $("#observaciones_pdtes").attr("required", false);
    }
});

$.post("../../controller/Obras.php?op=comboObras", function (data) {
    $('#selectObras').html(data);
});


function cerrarOrdenTrabajo(e) {

    e.preventDefault(); // Detiene el submit tradicional del formulario

    let formData = new FormData($("#orden_form_close")[0]);

    // Obtener valores de radio button
    let equipo_operativo = $('input[name="equipo_operativo"]:checked').val();
    let requiere_compra = $('input[name="requiere_compra"]:checked').val();
    //let solicitud_siesa = $("#num_siesa").val().trim();

    // ================================
    // VALIDACIONES CORRECTAS
    // ================================

    // Validación RADIO: equipo operativo
    if (typeof equipo_operativo === "undefined") {
        Swal.fire("Campo requerido", "Debe seleccionar si el equipo quedó operativo.", "warning");
        return;
    }

    // Validación RADIO: requiere compra
    if (typeof requiere_compra === "undefined") {
        Swal.fire("Campo requerido", "Debe indicar si requiere solicitud de compra.", "warning");
        return;
    }


    // ================================
    // ENVÍO AJAX
    // ================================
    $.ajax({
        url: "../../controller/OrdenTrabajo.php?op=close_otm",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",

        success: function (data) {

            if (data.status === "success") {

                Swal.fire({
                    icon: "success",
                    title: "Orden cerrada correctamente",
                    text: "Se generó el reporte de mantenimiento."
                }).then(() => {
                    // Cerrar modal
                    $("#modalCerrarOrden").modal("hide");

                    // Recargar tabla
                    $("#tableTktRev").DataTable().ajax.reload();
                });

            } else {
                Swal.fire("Error", data.message, "error");
            }
        },

        error: function () {
            Swal.fire("Error", "No se pudo cerrar la OT.", "error");
        }
    });
}

function verReporte(codigo_reporte) {
    console.log(codigo_reporte);
    window.location.href = BASE_URL + '/view/Tickets/detalle_reporte.php?id=' + codigo_reporte; //http://181.204.219.154:3396/preoperacional
}

function verPdf(reporteID) {
    console.log(reporteID);
    var url = BASE_URL + '/view/PDF/ReporteMtto.php?id=' + reporteID; //http://181.204.219.154:3396/preoperacional
    window.open(url, '_blank');
}




$('#filtroFecha').daterangepicker({
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
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ],
        firstDay: 1
    },
    ranges: {
        'Hoy': [moment(), moment()],
        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
        'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
        'Este mes': [moment().startOf('month'), moment().endOf('month')],
        'Mes pasado': [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month')
        ],

        // NUEVOS RANGOS
        'Año actual': [moment().startOf('year'), moment().endOf('year')],
        'Año pasado': [
            moment().subtract(1, 'year').startOf('year'),
            moment().subtract(1, 'year').endOf('year')
        ],
        'Primer semestre': [
            moment().startOf('year'),
            moment().startOf('year').add(5, 'months').endOf('month')
        ],
        'Segundo semestre': [
            moment().startOf('year').add(6, 'months'),
            moment().endOf('year')
        ],
        'Trimestre actual': [
            moment().startOf('quarter'),
            moment().endOf('quarter')
        ]
    },
    opens: "right",
    drops: "down",
    autoUpdateInput: false
});

$('#filtroFecha').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + " / " + picker.endDate.format('YYYY-MM-DD'));
});


$("#btnBuscar").click(function () {
    tablaCerrados.ajax.reload();
});

$("#btnLimpiar").click(function () {
    $("#filtroPlaca").val("").trigger("change");
    $("#filtroFecha").val("");
    tablaCerrados.ajax.reload();
});


$.post("../../controller/Vehiculo.php?op=comboVehiculo", function (data, status) {
    $('#filtroPlaca').html(data);
});

$("#btnBuscarSoli").click(function () {
    tablaRevision.ajax.reload();
});

$("#btnLimpiarSoli").click(function () {
    $("#filtroPlaca").val("").trigger("change");
    $("#filtroFecha").val("");
    tablaRevision.ajax.reload();
});



function cargarNumeroSolicitud() {
    $.post("../../controller/Tickets.php?op=numeroSolicitud", function (resp) {
        $("#num_solicitud").val(resp);
        $("#mdltitulo").html("No. Solicitud: " + resp);
    });
}


$(document).ready(function () {
    $('#btnAgregarTicket').on('click', function () {
        cargarNumeroSolicitud();
        $('#modalSolicitudMtto').modal('show');  // Abrir el modal
    });

});

$.post("../../controller/Usuario.php?op=comboUsuarioCond", function (data) {
    $('#conductor_solicitud').html(data);
});

$.post("../../controller/Vehiculo.php?op=comboVehiculo", function (data) {
    $('#vehiculo_solicitud').html(data);
});

/* $.post(
    "../../controller/Tickets.php?op=numeroSolicitud",
    function (data, status) {
        //console.log("Respuesta del servidor:", data);
        $("#num_solicitud").val(data);
    }
); */

function guardarSolicitud(e) {

    e.preventDefault();
    let num_solicitud = $('#num_solicitud').val();
    let conductor = $('#conductor_solicitud').val();
    let vehiculo = $('#vehiculo_solicitud').val();
    let falla = $('#desc_solicitud').val();
    let lectura = $('#lectura_solicitud').val();

    // ================================
    // VALIDACIONES
    // ================================
    if (!conductor) {
        Swal.fire("Campo requerido", "Debe seleccionar un conductor.", "warning");
        return;
    }

    if (!vehiculo) {
        Swal.fire("Campo requerido", "Debe seleccionar un vehículo.", "warning");
        return;
    }

    if (falla === "") {
        Swal.fire("Campo requerido", "Debe describir la falla reportada.", "warning");
        return;
    }

    if (lectura === "") {
        Swal.fire("Campo requerido", "Debe ingresar kilometraje u horometraje.", "warning");
        return;
    }


    let data = {
        num_solicitud: num_solicitud,
        conductor: conductor,
        vehiculo: vehiculo,
        falla: falla,
        lectura: lectura
    };

    console.log(data);

    $.ajax({

        url: '../../controller/Tickets.php?op=guardarSolicitudMtto',
        type: 'POST',
        data: data,
        success: function (response) {
            console.log(response);
            const result = JSON.parse(response);  // Asumimos que la respuesta es JSON

            // Mostrar mensaje de éxito o error con Swal
            if (result.status === 'success') {
                Swal.fire({
                    title: '¡Guardado Exitosamente!',
                    text: 'La solicitud fue creado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                $("#solicitud_form")[0].reset();
                // 👉 LIMPIAR SELECT2
                $("#conductor_solicitud").val("").trigger("change");
                $("#vehiculo_solicitud").val("").trigger("change");

                // Cerrar el modal
                $('#modalSolicitudMtto').modal('hide');
                cargarNumeroSolicitud();
                if (tablaOpen) {
                    tablaOpen.ajax.reload(null, false); // No perder la página actual
                }
            } else {
                Swal.fire({
                    title: 'Error',
                    text: result.message,
                    icon: 'error',
                    confirmButtonText: 'Intentar de nuevo'
                });
            }

        },
        error: function () {
            // En caso de error en la solicitud AJAX
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al crear la solicitud.',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo'
            });
        },

    });

}


init();
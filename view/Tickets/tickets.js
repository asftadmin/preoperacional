let tablaOpen = null;
let tablaRevision = null;
function init() {

    inicializarTabla();
    inicializarTablaR();

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
                            acciones: r[5]
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
            { data: "acciones", title: "Acciones" }
        ]
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

    if ($(this).val() == "1") {
        $("#campo_siesa").slideDown();
        $("#num_siesa").attr("required", true);
    } else {
        $("#campo_siesa").slideUp();
        $("#num_siesa").val("");
        $("#num_siesa").attr("required", false);
    }
});



init();
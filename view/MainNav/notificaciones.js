/*
 * =====================================================
 * NOTIFICACIONES - MESA DE SERVICIO
 * =====================================================
 */

let intervaloNotificaciones;


/*
 * INICIALIZACIÓN
 */
function init() {

    /*
     * Cargar contador al ingresar.
     */
    cargarContadorNotificaciones();


    /*
     * Al abrir la campana consultar las notificaciones.
     */
    $(document).on(
        "click",
        "#btnNotificacionesTickets",
        function () {

            cargarNotificaciones();

        }
    );


    /*
     * Seleccionar una notificación.
     */
    $(document).on(
        "click",
        ".notificacion-ticket",
        function (e) {

            e.preventDefault();

            let notificacion_id =
                $(this).data("notificacion");

            let ticket_id =
                $(this).data("ticket");

            marcarNotificacionLeida(
                notificacion_id,
                ticket_id
            );

        }
    );

        /*
     * Evitar intervalos duplicados.
     */
    if (intervaloNotificaciones) {

        clearInterval(
            intervaloNotificaciones
        );

    }


    /*
     * Actualizar contador cada 60 segundos.
     */
    intervaloNotificaciones =
        setInterval(
            cargarContadorNotificaciones,
            150000
        );

}


/*
 * =====================================================
 * CONTADOR DE NOTIFICACIONES
 * =====================================================
 */

function cargarContadorNotificaciones() {

    $.ajax({

        url:
            "../../controller/TicketsSistemas.php"
            + "?op=contadorNotificaciones",

        type: "GET",

        dataType: "json",

        success: function (data) {

            let total = 0;


            if (
                data.status === "success"
                &&
                data.data
            ) {

                total =
                    parseInt(
                        data.data.total || 0,
                        10
                    );

            }


            /*
             * Mostrar contador.
             */
            if (total > 0) {

                $("#contadorNotificacionesTickets")
                    .text(
                        total > 99
                            ? "99+"
                            : total
                    )
                    .removeClass("d-none");

            } else {

                $("#contadorNotificacionesTickets")
                    .text("0")
                    .addClass("d-none");

            }


            /*
             * Texto del encabezado.
             */
            if (total === 1) {

                $("#encabezadoNotificacionesTickets")
                    .text(
                        "1 notificación pendiente"
                    );

            } else {

                $("#encabezadoNotificacionesTickets")
                    .text(
                        total
                        + " notificaciones pendientes"
                    );

            }

        },

        error: function (e) {

            console.log(
                "Error consultando contador de notificaciones:"
            );

            console.log(
                e.responseText
            );

        }

    });

}


/*
 * =====================================================
 * LISTAR NOTIFICACIONES
 * =====================================================
 */

function cargarNotificaciones() {

    /*
     * Mostrar cargando.
     */
    $("#listaNotificacionesTickets").html(

        '<span class="dropdown-item text-center text-muted">'
        + '<i class="fas fa-spinner fa-spin mr-1"></i>'
        + 'Cargando...'
        + '</span>'

    );


    $.ajax({

        url:
            "../../controller/TicketsSistemas.php"
            + "?op=listarNotificaciones",

        type: "GET",

        dataType: "json",

        success: function (data) {

            $("#listaNotificacionesTickets")
                .empty();


            /*
             * Validar información.
             */
            if (
                data.status !== "success"
                ||
                !Array.isArray(data.data)
                ||
                data.data.length === 0
            ) {

                mostrarSinNotificaciones();

                return;

            }


            /*
             * Recorrer notificaciones.
             */
            $.each(
                data.data,
                function (index, item) {

                    agregarNotificacion(
                        item
                    );


                    /*
                     * Separador.
                     */
                    if (
                        index
                        <
                        data.data.length - 1
                    ) {

                        $("#listaNotificacionesTickets")
                            .append(
                                '<div class="dropdown-divider"></div>'
                            );

                    }

                }
            );

        },

        error: function (e) {

            console.log(
                "Error consultando notificaciones:"
            );

            console.log(
                e.responseText
            );


            $("#listaNotificacionesTickets")
                .html(

                    '<span class="dropdown-item text-center text-danger">'
                    + '<i class="fas fa-exclamation-circle mr-1"></i>'
                    + 'No fue posible cargar'
                    + '</span>'

                );

        }

    });

}


/*
 * =====================================================
 * AGREGAR NOTIFICACIÓN AL DROPDOWN
 * =====================================================
 */

function agregarNotificacion(item) {

    /*
     * Crear enlace.
     */
    let enlace =
        $("<a>", {

            href: "#",

            class:
                "dropdown-item notificacion-ticket"

        });


    enlace.attr(
        "data-notificacion",
        item.notificacion_id
    );


    enlace.attr(
        "data-ticket",
        item.ticket_id
    );


    /*
     * Validar si está leída.
     */
    let leida =
        item.leida === true
        ||
        item.leida === "true"
        ||
        item.leida === "t"
        ||
        item.leida === 1
        ||
        item.leida === "1";


    /*
     * Resaltar pendientes.
     */
    if (!leida) {

        enlace.addClass(
            "font-weight-bold"
        );

    }


    /*
     * Contenedor.
     */
    let contenido =
        $("<div>", {

            class: "d-flex"

        });


    /*
     * Icono.
     */
    let icono =
        $("<div>", {

            class: "mr-2 pt-1"

        });


    icono.append(

        $("<i>", {

            class:
                "fas fa-ticket-alt text-info"

        })

    );


    /*
     * Información.
     */
    let informacion =
        $("<div>", {

            class: "flex-grow-1"

        });


    /*
     * Número del ticket.
     */
    let ticket =
        $("<div>", {

            class: "text-sm"

        });


    ticket.text(

        item.ticket_numero
        + " · "
        + item.prioridad

    );


    /*
     * Mensaje.
     */
    let mensaje =
        $("<div>", {

            class:
                "text-sm text-muted"

        });


    mensaje.text(
        item.mensaje || ""
    );


    /*
     * Construcción.
     */
    informacion
        .append(ticket)
        .append(mensaje);


    contenido
        .append(icono)
        .append(informacion);


    enlace.append(
        contenido
    );


    /*
     * Agregar al dropdown.
     */
    $("#listaNotificacionesTickets")
        .append(
            enlace
        );

}


/*
 * =====================================================
 * SIN NOTIFICACIONES
 * =====================================================
 */

function mostrarSinNotificaciones() {

    $("#listaNotificacionesTickets")
        .html(

            '<span class="dropdown-item text-center text-muted">'
            + '<i class="fas fa-bell-slash mr-1"></i>'
            + 'Sin notificaciones'
            + '</span>'

        );

}


/*
 * =====================================================
 * MARCAR NOTIFICACIÓN COMO LEÍDA
 * =====================================================
 */

function marcarNotificacionLeida(
    notificacion_id,
    ticket_id
) {

    /*
     * Validar datos.
     */
    if (
        !notificacion_id
        ||
        !ticket_id
    ) {

        return;

    }


    /*
     * Token generado desde nav.php.
     */
    let csrf_token =
        $("#csrfNotificaciones").val();


    $.ajax({

        url:
            "../../controller/TicketsSistemas.php"
            + "?op=marcarNotificacionLeida",

        type: "POST",

        dataType: "json",

        data: {

            notificacion_id:
                notificacion_id,

            csrf_token:
                csrf_token

        },

        success: function (data) {

            /*
             * Actualizar contador.
             */
            cargarContadorNotificaciones();


            /*
             * Abrir gestión del ticket.
             */
            window.location.href =
                "../TicketsSistemas/gestion.php?id="
                + encodeURIComponent(
                    ticket_id
                );

        },

        error: function (e) {

            console.log(
                "Error marcando notificación:"
            );

            console.log(
                e.responseText
            );


            /*
             * Aunque falle la lectura,
             * permitimos ingresar al ticket.
             */
            window.location.href =
                "../TicketsSistemas/gestion.php?id="
                + encodeURIComponent(
                    ticket_id
                );

        }

    });

}


/*
 * =====================================================
 * DOCUMENT READY
 * =====================================================
 */

$(document).ready(
    function () {

        /*
         * La inicialización general
         * se ejecuta desde init().
         */

    }
);


/*
 * INICIAR
 */
init();
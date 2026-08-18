let tablaTicketsAtencion;
let graficoTiempoSolucion;

function init() {
    cargarKpiControlOperacion();
    cargarTicketsRequierenAtencion();
    cargarGraficoTiempoSolucion();
    cargarAntiguedadPendientes();
}

$(document).on(
    "click",
    "#btn_actualizar_control",
    function () {

        cargarKpiControlOperacion();
        cargarAntiguedadPendientes();
        cargarGraficoTiempoSolucion();
        /*
 * Tabla operativa
 */

        if (tablaTicketsAtencion) {

            tablaTicketsAtencion
                .ajax
                .reload(
                    null,
                    false
                );

        }

    }
);

function cargarKpiControlOperacion() {

    $.ajax({

        url: "../../controller/TicketsSistemas.php?op=kpiControlOperacion",

        type: "GET",

        dataType: "json",

        beforeSend: function () {

            $("#kpi_tickets_recibidos").text("...");
            $("#kpi_tickets_cerrados").text("...");
            $("#kpi_tickets_pendientes").text("...");
            $("#kpi_tickets_fuera_tiempo").text("...");

        },

        success: function (response) {

            if (
                !response ||
                response.status !== "success"
            ) {

                Swal.fire({
                    icon: "warning",
                    title: "Mesa de Servicio",
                    text: response.message || "No fue posible consultar los indicadores."
                });

                return;
            }


            const data = response.data || {};


            /*
             * Tickets recibidos hoy
             */
            $("#kpi_tickets_recibidos").text(
                data.recibidos_hoy ?? 0
            );


            /*
             * Tickets cerrados hoy
             */
            $("#kpi_tickets_cerrados").text(
                data.cerrados_hoy ?? 0
            );


            /*
             * Tickets pendientes
             */
            $("#kpi_tickets_pendientes").text(
                data.pendientes ?? 0
            );

            $("#kpi_tickets_pendientes_detalle").text(
                (data.sin_asignar ?? 0) + " sin asignar"
            );


            /*
             * Tickets fuera de tiempo
             */
            $("#kpi_tickets_fuera_tiempo").text(
                data.fuera_tiempo ?? 0
            );

            $("#kpi_tickets_fuera_tiempo_detalle").text(
                (data.fuera_tiempo_prioritario ?? 0)
                + " alta/crítica"
            );

        },

        error: function (xhr) {

            console.error(
                "Error KPI:",
                xhr.responseText
            );

            Swal.fire({
                icon: "error",
                title: "Mesa de Servicio",
                text: "No fue posible consultar los indicadores de operación."
            });

        }

    });

}

/*
 * =========================================================
 * TICKETS QUE REQUIEREN ATENCION
 * =========================================================
 */
function cargarTicketsRequierenAtencion() {

    /*
     * Si la DataTable ya existe,
     * la destruimos antes de volver a crearla.
     */
    if ($.fn.DataTable.isDataTable("#control_operacion_data")) {

        $("#control_operacion_data")
            .DataTable()
            .destroy();
    }


    tablaTicketsAtencion =
        $("#control_operacion_data")
            .DataTable({

                ajax: {

                    url:
                        "../../controller/TicketsSistemas.php"
                        + "?op=listarTicketsRequierenAtencion",

                    type: "GET",

                    /*
                     * El controller retorna:
                     *
                     * {
                     *   status,
                     *   message,
                     *   data
                     * }
                     *
                     * DataTables necesita recibir
                     * directamente el arreglo.
                     */
                    dataSrc: function (response) {

                        if (
                            !response ||
                            response.status !== "success"
                        ) {

                            Swal.fire({
                                icon: "warning",
                                title: "Mesa de Servicio",
                                text:
                                    response &&
                                        response.message
                                        ? response.message
                                        : "No fue posible consultar los tickets."
                            });

                            return [];
                        }


                        /*
                         * Actualizamos también el badge
                         * ubicado en el encabezado de la card.
                         */
                        $("#total_tickets_atencion")
                            .text(
                                response.data
                                    ? response.data.length
                                    : 0
                            );


                        return response.data || [];
                    }
                },


                columns: [

                    /*
                     * TICKET
                     */
                    {
                        data: "ticket_numero",
                        className: "text-center"
                    },


                    /*
                     * FECHA CREACION
                     */
                    {
                        data: "fecha_creacion",
                        className: "text-center"
                    },


                    /*
                     * SOLICITANTE
                     */
                    {
                        data: "empleado_nombre"
                    },


                    /*
                     * AREA
                     */
                    {
                        data: "empleado_area"
                    },


                    /*
                     * ASUNTO
                     */
                    {
                        data: "asunto"
                    },


                    /*
                     * CATEGORIA
                     */
                    {
                        data: "categoria",
                        className: "text-center"
                    },


                    /*
                     * PRIORIDAD
                     */
                    {
                        data: "prioridad",
                        className: "text-center",

                        render: function (data) {

                            switch (data) {

                                case "CRITICA":

                                    return '<span class="badge badge-danger">'
                                        + 'CRÍTICA'
                                        + '</span>';

                                case "ALTA":

                                    return '<span class="badge badge-warning">'
                                        + 'ALTA'
                                        + '</span>';

                                case "MEDIA":

                                    return '<span class="badge badge-info">'
                                        + 'MEDIA'
                                        + '</span>';

                                case "BAJA":

                                    return '<span class="badge badge-secondary">'
                                        + 'BAJA'
                                        + '</span>';

                                default:

                                    return data || "";
                            }
                        }
                    },


                    /*
                     * RESPONSABLE
                     */
                    {
                        data: "responsable",

                        render: function (data) {

                            if (
                                !data ||
                                data === "Sin asignar"
                            ) {

                                return '<span class="badge badge-danger">'
                                    + 'Sin asignar'
                                    + '</span>';
                            }

                            return data;
                        }
                    },


                    /*
                     * ANTIGÜEDAD
                     */
                    {
                        data: "horas_transcurridas",
                        className: "text-center",

                        render: function (data) {

                            const horas =
                                parseFloat(data || 0);


                            /*
                             * Menos de un día:
                             * mostramos horas.
                             */
                            if (horas < 24) {

                                return horas.toFixed(1)
                                    + " h";
                            }


                            /*
                             * Más de un día:
                             * mostramos días y horas.
                             */
                            const dias =
                                Math.floor(
                                    horas / 24
                                );

                            const horasRestantes =
                                Math.floor(
                                    horas % 24
                                );


                            return dias
                                + " d "
                                + horasRestantes
                                + " h";
                        }
                    },


                    /*
                     * SITUACION
                     */
                    {
                        data: "situaciones",
                        className: "text-center",

                        render: function (data) {

                            if (
                                !Array.isArray(data)
                            ) {
                                return "";
                            }


                            let html = "";


                            data.forEach(
                                function (situacion) {

                                    switch (situacion) {

                                        case "FUERA_TIEMPO":

                                            html +=
                                                '<span class="badge badge-danger mr-1">'
                                                + 'Fuera de tiempo'
                                                + '</span>';

                                            break;


                                        case "CRITICA":

                                            html +=
                                                '<span class="badge badge-danger mr-1">'
                                                + 'Crítica'
                                                + '</span>';

                                            break;


                                        case "ALTA":

                                            html +=
                                                '<span class="badge badge-warning mr-1">'
                                                + 'Alta'
                                                + '</span>';

                                            break;


                                        case "SIN_ASIGNAR":

                                            html +=
                                                '<span class="badge badge-secondary mr-1">'
                                                + 'Sin asignar'
                                                + '</span>';

                                            break;
                                    }
                                }
                            );


                            return html;
                        }
                    },


                    /*
                     * ESTADO
                     */
                    {
                        data: "estado",
                        className: "text-center",

                        render: function (data) {

                            switch (data) {

                                case "ABIERTO":

                                    return '<span class="badge badge-primary">'
                                        + 'ABIERTO'
                                        + '</span>';


                                case "EN_PROCESO":

                                    return '<span class="badge badge-info">'
                                        + 'EN PROCESO'
                                        + '</span>';


                                case "EN_ESPERA":

                                    return '<span class="badge badge-warning">'
                                        + 'EN ESPERA'
                                        + '</span>';


                                default:

                                    return data || "";
                            }
                        }
                    },


                    /*
                     * ACCION
                     */
                    {
                        data: "ticket_id",
                        className: "text-center",
                        orderable: false,

                        render: function (data) {

                            return `
                            <button
                                type="button"
                                class="btn btn-primary btn-sm btnGestionarTicket"
                                data-id="${data}"
                                title="Gestionar ticket">

                                <i class="fas fa-tools"></i>

                            </button>
                        `;
                        }
                    }

                ],


                /*
                 * El orden ya viene preparado desde backend.
                 */
                ordering: false,


                responsive: true,

                autoWidth: false,


                language: {

                    search:
                        "Buscar:",

                    lengthMenu:
                        "Mostrar _MENU_ registros",

                    info:
                        "Mostrando _START_ a _END_ de _TOTAL_ registros",

                    infoEmpty:
                        "No hay tickets que requieran atención",

                    zeroRecords:
                        "No se encontraron tickets",

                    paginate: {

                        previous:
                            "Anterior",

                        next:
                            "Siguiente"
                    }
                }

            });
}

/*
 * =========================================================
 * ANTIGÜEDAD DE TICKETS PENDIENTES
 * =========================================================
 *
 * Consulta los cuatro rangos de antigüedad
 * y actualiza directamente la tabla de la vista.
 */
function cargarAntiguedadPendientes() {

    $.ajax({

        url:
            "../../controller/TicketsSistemas.php"
            + "?op=antiguedadTicketsPendientes",

        type: "GET",

        dataType: "json",

        beforeSend: function () {

            $("#antiguedad_0_1").text("...");
            $("#antiguedad_2_3").text("...");
            $("#antiguedad_4_7").text("...");
            $("#antiguedad_mayor_7").text("...");

        },

        success: function (response) {

            if (
                !response ||
                response.status !== "success"
            ) {

                limpiarAntiguedadPendientes();

                Swal.fire({
                    icon: "warning",
                    title: "Mesa de Servicio",
                    text:
                        response &&
                        response.message
                            ? response.message
                            : "No fue posible consultar la antigüedad de los pendientes."
                });

                return;
            }


            const data = response.data || {};


            /*
             * 0 - 1 día
             */
            $("#antiguedad_0_1").text(
                data.rango_0_1 ?? 0
            );


            /*
             * 2 - 3 días
             */
            $("#antiguedad_2_3").text(
                data.rango_2_3 ?? 0
            );


            /*
             * 4 - 7 días
             */
            $("#antiguedad_4_7").text(
                data.rango_4_7 ?? 0
            );


            /*
             * Más de 7 días
             */
            $("#antiguedad_mayor_7").text(
                data.rango_mayor_7 ?? 0
            );


            /*
             * Total del backlog.
             *
             * Debe coincidir con el KPI de
             * tickets pendientes.
             */
            const total =
                (data.rango_0_1 ?? 0)
                + (data.rango_2_3 ?? 0)
                + (data.rango_4_7 ?? 0)
                + (data.rango_mayor_7 ?? 0);


            $("#total_antiguedad_pendientes")
                .text(total);

        },

        error: function (xhr) {

            limpiarAntiguedadPendientes();

            console.error(
                "Error antigüedad pendientes:",
                xhr.responseText
            );

            Swal.fire({
                icon: "error",
                title: "Mesa de Servicio",
                text: "No fue posible consultar la antigüedad de los tickets pendientes."
            });

        }

    });

}

/*
 * =========================================================
 * TIEMPO PROMEDIO DE SOLUCION VS SLA
 * =========================================================
 */
function cargarGraficoTiempoSolucion() {

    $.ajax({

        url:
            "../../controller/TicketsSistemas.php"
            + "?op=tiempoPromedioSolucionVsSla",

        type: "GET",

        dataType: "json",

        success: function (response) {

            if (
                !response ||
                response.status !== "success"
            ) {

                Swal.fire({
                    icon: "warning",
                    title: "Mesa de Servicio",
                    text:
                        response &&
                        response.message
                            ? response.message
                            : "No fue posible consultar el tiempo promedio de solución."
                });

                return;
            }


            const datos =
                response.data || [];


            /*
             * Fechas eje X.
             */
            const etiquetas =
                datos.map(
                    item => item.fecha
                );


            /*
             * Tiempo promedio real.
             */
            const promedioReal =
                datos.map(
                    item => item.promedio_real
                );


            /*
             * Meta SLA promedio ponderada.
             */
            const promedioSla =
                datos.map(
                    item => item.promedio_sla
                );


            /*
             * Cantidad de tickets cerrados
             * por cada día.
             */
            const ticketsCerrados =
                datos.map(
                    item => item.tickets_cerrados
                );


            const canvas =
                document.getElementById(
                    "grafico_tiempo_solucion"
                );


            if (!canvas) {
                return;
            }


            const ctx =
                canvas.getContext("2d");


            /*
             * Si el gráfico ya existe,
             * lo destruimos antes de reconstruirlo.
             */
            if (graficoTiempoSolucion) {

                graficoTiempoSolucion.destroy();

            }


            graficoTiempoSolucion =
                new Chart(
                    ctx,
                    {

                        type: "line",

                        data: {

                            labels:
                                etiquetas,

                            datasets: [

                                {
                                    label:
                                        "Promedio real",

                                    data:
                                        promedioReal,

                                    borderWidth: 2,

                                    fill: false,

                                    tension: 0.3
                                },

                                {
                                    label:
                                        "Meta SLA",

                                    data:
                                        promedioSla,

                                    borderWidth: 2,

                                    borderDash: [
                                        5,
                                        5
                                    ],

                                    fill: false,

                                    tension: 0.3
                                }

                            ]
                        },


                        options: {

                            responsive: true,

                            maintainAspectRatio: false,


                            plugins: {

                                legend: {

                                    display: true,

                                    position: "top"
                                },


                                /*
                                 * ETIQUETAS DE DATOS.
                                 *
                                 * Requiere:
                                 * chartjs-plugin-datalabels
                                 */
                                datalabels: {

                                    display: true,

                                    align: "top",

                                    anchor: "end",

                                    formatter:
                                        function (value) {

                                            return (
                                                parseFloat(value)
                                                .toFixed(1)
                                                + " h"
                                            );

                                        },

                                    font: {

                                        size: 10,

                                        weight: "bold"
                                    }

                                },


                                tooltip: {

                                    callbacks: {

                                        afterBody:
                                            function (
                                                tooltipItems
                                            ) {

                                                const index =
                                                    tooltipItems[0]
                                                    .dataIndex;


                                                return (
                                                    "Tickets cerrados: "
                                                    + ticketsCerrados[
                                                        index
                                                    ]
                                                );

                                            }

                                    }

                                }

                            },


                            scales: {

                                y: {

                                    beginAtZero: true,

                                    title: {

                                        display: true,

                                        text:
                                            "Horas promedio"
                                    }

                                },

                                x: {

                                    title: {

                                        display: true,

                                        text:
                                            "Fecha de cierre"
                                    }

                                }

                            }

                        },


                        /*
                         * Activamos el plugin
                         * de etiquetas de datos.
                         */
                        plugins: [
                            ChartDataLabels
                        ]

                    }
                );

        },


        error: function (xhr) {

            console.error(
                "Error gráfico tiempo solución:",
                xhr.responseText
            );

            Swal.fire({
                icon: "error",
                title: "Mesa de Servicio",
                text:
                    "No fue posible cargar el gráfico de tiempo promedio de solución."
            });

        }

    });

}


/*
 * RESTABLECER VALORES.
 */
function limpiarAntiguedadPendientes() {

    $("#antiguedad_0_1").text("0");

    $("#antiguedad_2_3").text("0");

    $("#antiguedad_4_7").text("0");

    $("#antiguedad_mayor_7").text("0");

    $("#total_antiguedad_pendientes").text("0");

}

init();
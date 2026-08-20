let filtrosTicketsGerencial = {
    fecha_inicio: '',
    fecha_final: '',
};

let chartComportamientoTickets = null;

let chartAntiguedadBacklog = null;

let chartTicketsCategoria = null;

let chartTicketsArea = null;

/*
 * =========================================================
 * INICIALIZACIÓN
 * =========================================================
 */

function init() {
    inicializarRangoFechasGerencial();

    cargarDashboardGerencial();
}

/*
 * =========================================================
 * RANGO DE FECHAS
 * =========================================================
 */

function inicializarRangoFechasGerencial() {
    let fechaInicio = moment().startOf('month');
    let fechaFinal = moment();

    $('#rango_fechas_gerencial').daterangepicker({
        startDate: fechaInicio,

        endDate: fechaFinal,

        autoUpdateInput: true,

        locale: {
            format: 'DD/MM/YYYY',

            separator: ' - ',

            applyLabel: 'Aplicar',

            cancelLabel: 'Cancelar',

            fromLabel: 'Desde',

            toLabel: 'Hasta',

            customRangeLabel: 'Personalizado',

            weekLabel: 'S',

            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],

            monthNames: [
                'Enero',
                'Febrero',
                'Marzo',
                'Abril',
                'Mayo',
                'Junio',
                'Julio',
                'Agosto',
                'Septiembre',
                'Octubre',
                'Noviembre',
                'Diciembre',
            ],

            firstDay: 1,
        },

        ranges: {
            Hoy: [moment(), moment()],

            Ayer: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],

            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],

            'Este mes': [moment().startOf('month'), moment().endOf('month')],

            'Mes anterior': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month'),
            ],
        },
    });

    /*
     * Periodo inicial:
     * primer día del mes hasta la fecha actual.
     */

    filtrosTicketsGerencial.fecha_inicio = fechaInicio.format('YYYY-MM-DD');

    filtrosTicketsGerencial.fecha_final = fechaFinal.format('YYYY-MM-DD');

    /*
     * Cuando el usuario aplica un nuevo rango.
     */

    $('#rango_fechas_gerencial').on('apply.daterangepicker', function (ev, picker) {
        filtrosTicketsGerencial.fecha_inicio = picker.startDate.format('YYYY-MM-DD');

        filtrosTicketsGerencial.fecha_final = picker.endDate.format('YYYY-MM-DD');
    });
}

/*
 * =========================================================
 * BOTÓN CONSULTAR
 * =========================================================
 */

$(document).on('click', '#btn_consultar_gerencial', function () {
    cargarDashboardGerencial();
});

/*
 * =========================================================
 * BOTÓN LIMPIAR
 * =========================================================
 */

$(document).on('click', '#btn_limpiar_gerencial', function () {
    let fechaInicio = moment().startOf('month');

    let fechaFinal = moment();

    $('#rango_fechas_gerencial').data('daterangepicker').setStartDate(fechaInicio);

    $('#rango_fechas_gerencial').data('daterangepicker').setEndDate(fechaFinal);

    $('#rango_fechas_gerencial').val(
        fechaInicio.format('DD/MM/YYYY') + ' - ' + fechaFinal.format('DD/MM/YYYY'),
    );

    filtrosTicketsGerencial.fecha_inicio = fechaInicio.format('YYYY-MM-DD');

    filtrosTicketsGerencial.fecha_final = fechaFinal.format('YYYY-MM-DD');

    cargarDashboardGerencial();
});

/*
 * =========================================================
 * CARGAR DASHBOARD
 * =========================================================
 */

function cargarDashboardGerencial() {
    mostrarCargandoDashboard();

    $.ajax({
        /*
         * IMPORTANTE:
         *
         * Ajustar esta URL al nombre REAL del controlador
         * existente para Mesa de Servicio.
         *
         * No crear el case ni el archivo hasta validar
         * la estructura actual del proyecto.
         */

        url: '../../controller/TicketsSistemas.php?op=dashboardGerencial',

        type: 'GET',

        dataType: 'json',

        data: {
            fecha_inicio: filtrosTicketsGerencial.fecha_inicio,

            fecha_final: filtrosTicketsGerencial.fecha_final,
        },

        success: function (response) {
            if (response.status === 'success' && response.data) {
                cargarKpisGerenciales(response.data.kpis);

                renderizarComportamientoTickets(response.data.comportamiento);

                renderizarAntiguedadBacklog(response.data.backlog);

                renderizarTicketsCategoria(response.data.categorias);

                renderizarTicketsArea(response.data.areas);
            } else {
                limpiarDashboardGerencial();

                Swal.fire({
                    icon: 'warning',

                    title: 'Sin información',

                    text:
                        response.message || 'No se encontraron datos para el periodo seleccionado.',
                });
            }
        },

        error: function (xhr) {
            limpiarDashboardGerencial();

            console.error('Error dashboard gerencial:', xhr.responseText);

            Swal.fire({
                icon: 'error',

                title: 'Error',

                text: 'No fue posible cargar los indicadores de la Mesa de Servicio.',
            });
        },
    });
}

/*
 * =========================================================
 * KPI PRINCIPALES
 * =========================================================
 */

function cargarKpisGerenciales(kpis) {
    kpis = kpis || {};

    $('#kpi_tickets_recibidos').text(formatearNumero(kpis.recibidos || 0));

    $('#kpi_tickets_cerrados').text(formatearNumero(kpis.cerrados || 0));

    $('#kpi_tickets_pendientes').text(formatearNumero(kpis.pendientes || 0));

    $('#kpi_cumplimiento').text(formatearPorcentaje(kpis.cumplimiento || 0));

    /*
     * Resumen ejecutivo.
     */

    $('#indicador_porcentaje_cierre').text(formatearPorcentaje(kpis.porcentaje_cierre || 0) + '%');

    $('#indicador_cumplimiento').text(formatearPorcentaje(kpis.cumplimiento || 0) + '%');

    $('#indicador_backlog_critico').text(formatearNumero(kpis.backlog_critico || 0));
}

/*
 * =========================================================
 * COMPORTAMIENTO DE TICKETS
 * =========================================================
 */

/*
 * =========================================================
 * 1. COMPORTAMIENTO DE LAS SOLICITUDES
 * =========================================================
 */

function renderizarComportamientoTickets(data) {
    if (!Array.isArray(data) || data.length === 0) {
        mostrarSinDatos(
            '#contenedor_comportamiento_tickets',
            'No existen datos de comportamiento para el periodo.',
        );

        return;
    }

    /*
     * Destruir gráfico anterior.
     */
    if (chartComportamientoTickets !== null) {
        chartComportamientoTickets.destroy();
        chartComportamientoTickets = null;
    }

    /*
     * Crear canvas.
     */
    $('#contenedor_comportamiento_tickets').html(`
        <div style="position: relative; height: 320px;">
            <canvas id="chart_comportamiento_tickets"></canvas>
        </div>
    `);

    let etiquetas = [];
    let recibidos = [];
    let cerrados = [];

    data.forEach(function (item) {
        etiquetas.push(item.fecha || '');

        recibidos.push(parseInt(item.recibidos || 0, 10));

        cerrados.push(parseInt(item.cerrados || 0, 10));
    });

    let canvas = document.getElementById('chart_comportamiento_tickets');

    if (!canvas) {
        return;
    }

    chartComportamientoTickets = new Chart(canvas, {
        type: 'line',

        data: {
            labels: etiquetas,

            datasets: [
                {
                    label: 'Tickets recibidos',

                    data: recibidos,

                    borderColor: '#17a2b8',

                    backgroundColor: 'rgba(23, 162, 184, 0.15)',

                    pointBackgroundColor: '#17a2b8',

                    pointBorderColor: '#17a2b8',

                    borderWidth: 2,

                    pointRadius: 4,

                    pointHoverRadius: 6,

                    tension: 0.25,

                    fill: false,
                },

                {
                    label: 'Tickets cerrados',

                    data: cerrados,

                    borderColor: '#28a745',

                    backgroundColor: 'rgba(40, 167, 69, 0.15)',

                    pointBackgroundColor: '#28a745',

                    pointBorderColor: '#28a745',

                    borderWidth: 2,

                    pointRadius: 4,

                    pointHoverRadius: 6,

                    tension: 0.25,

                    fill: false,
                },
            ],
        },

        options: {
            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                mode: 'index',

                intersect: false,
            },

            plugins: {
                legend: {
                    display: true,

                    position: 'top',
                },

                tooltip: {
                    enabled: true,
                },
            },

            scales: {
                x: {
                    grid: {
                        display: false,
                    },

                    ticks: {
                        autoSkip: true,

                        maxRotation: 0,

                        minRotation: 0,
                    },
                },

                y: {
                    beginAtZero: true,

                    title: {
                        display: true,

                        text: 'Cantidad de tickets',
                    },

                    ticks: {
                        precision: 0,

                        stepSize: 1,
                    },

                    grid: {
                        color: 'rgba(0,0,0,0.06)',
                    },
                },
            },
        },
    });
}

/*
 * =========================================================
 * 2. ANTIGÜEDAD DEL BACKLOG
 * =========================================================
 */

function renderizarAntiguedadBacklog(data) {
    if (!Array.isArray(data) || data.length === 0) {
        mostrarSinDatos('#contenedor_antiguedad_backlog', 'No existen tickets pendientes.');

        return;
    }

    /*
     * Destruir gráfico anterior.
     */
    if (chartAntiguedadBacklog !== null) {
        chartAntiguedadBacklog.destroy();
        chartAntiguedadBacklog = null;
    }

    let etiquetas = [];
    let valores = [];

    let totalBacklog = 0;

    data.forEach(function (item) {
        let total = parseInt(item.total || 0, 10);

        etiquetas.push(item.rango || '');

        valores.push(total);

        totalBacklog += total;
    });

    /*
     * Si todos los valores están en cero,
     * mostramos estado vacío.
     */
    if (totalBacklog === 0) {
        mostrarSinDatos('#contenedor_antiguedad_backlog', 'No existen tickets pendientes.');

        return;
    }

    $('#contenedor_antiguedad_backlog').html(`

        <div style="position: relative; height: 230px;">

            <canvas
                id="chart_antiguedad_backlog">
            </canvas>

        </div>


        <div class="text-center mt-2">

            <small class="text-muted d-block">
                Total backlog
            </small>

            <h4 class="mb-0">
                ${formatearNumero(totalBacklog)}
            </h4>

        </div>

    `);

    let canvas = document.getElementById('chart_antiguedad_backlog');

    if (!canvas) {
        return;
    }

    chartAntiguedadBacklog = new Chart(canvas, {
        type: 'doughnut',

        data: {
            labels: etiquetas,

            datasets: [
                {
                    data: valores,

                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],

                    borderColor: '#ffffff',

                    borderWidth: 2,
                },
            ],
        },

        options: {
            responsive: true,

            maintainAspectRatio: false,

            cutout: '60%',

            plugins: {
                legend: {
                    display: true,

                    position: 'top',

                    labels: {
                        boxWidth: 14,

                        padding: 12,
                    },
                },

                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let valor = parseInt(context.raw || 0, 10);

                            return (
                                context.label + ': ' + valor + ' ticket' + (valor === 1 ? '' : 's')
                            );
                        },
                    },
                },

                /*
                 * Etiquetas visibles sobre la dona.
                 */
                datalabels: {
                    display: function (context) {
                        return context.dataset.data[context.dataIndex] > 0;
                    },

                    color: '#ffffff',

                    font: {
                        weight: 'bold',

                        size: 14,
                    },

                    formatter: function (value) {
                        return value;
                    },
                },
            },
        },
    });
}

/*
 * =========================================================
 * 3. TICKETS POR CATEGORÍA
 * =========================================================
 */

function renderizarTicketsCategoria(data) {
    if (!Array.isArray(data) || data.length === 0) {
        mostrarSinDatos('#contenedor_tickets_categoria', 'No existen datos por categoría.');

        return;
    }

    /*
     * Destruir gráfico anterior.
     */
    if (chartTicketsCategoria !== null) {
        chartTicketsCategoria.destroy();
        chartTicketsCategoria = null;
    }

    /*
     * Copiamos el arreglo antes de ordenar
     * para no modificar directamente
     * la respuesta original.
     */
    let datosOrdenados = data.slice();

    datosOrdenados.sort(function (a, b) {
        return parseInt(b.total || 0, 10) - parseInt(a.total || 0, 10);
    });

    let etiquetas = [];
    let valores = [];

    datosOrdenados.forEach(function (item) {
        etiquetas.push(item.categoria || 'Sin categoría');

        valores.push(parseInt(item.total || 0, 10));
    });

    /*
     * Ajustamos dinámicamente la altura
     * cuando existan muchas categorías.
     */
    let alturaGrafico = Math.max(320, etiquetas.length * 42);

    $('#contenedor_tickets_categoria').html(`

        <div
            style="
                position: relative;
                height: ${alturaGrafico}px;
            "
        >

            <canvas
                id="chart_tickets_categoria">
            </canvas>

        </div>

    `);

    let canvas = document.getElementById('chart_tickets_categoria');

    if (!canvas) {
        return;
    }

    chartTicketsCategoria = new Chart(canvas, {
        /*
         * Chart.js 3+
         *
         * Barras horizontales:
         * type bar + indexAxis y
         */
        type: 'bar',

        data: {
            labels: etiquetas,

            datasets: [
                {
                    label: 'Tickets',

                    data: valores,

                    backgroundColor: '#17a2b8',

                    borderColor: '#138496',

                    borderWidth: 1,

                    borderRadius: 3,

                    barThickness: 24,
                },
            ],
        },

        options: {
            indexAxis: 'y',

            responsive: true,

            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },

                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let valor = parseInt(context.raw || 0, 10);

                            return valor + ' ticket' + (valor === 1 ? '' : 's');
                        },
                    },
                },
            },

            scales: {
                x: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,

                        stepSize: 1,
                    },

                    grid: {
                        color: 'rgba(0,0,0,0.06)',
                    },
                },

                y: {
                    grid: {
                        display: false,
                    },

                    ticks: {
                        autoSkip: false,
                    },
                },
            },
        },
    });
}

/*
 * =========================================================
 * 4. TICKETS POR ÁREA
 * =========================================================
 */

function renderizarTicketsArea(data) {
    if (!Array.isArray(data) || data.length === 0) {
        mostrarSinDatos('#contenedor_tickets_area', 'No existen datos por área.');

        return;
    }

    /*
     * Destruir gráfico anterior.
     */
    if (chartTicketsArea !== null) {
        chartTicketsArea.destroy();
        chartTicketsArea = null;
    }

    let datosOrdenados = data.slice();

    datosOrdenados.sort(function (a, b) {
        return parseInt(b.total || 0, 10) - parseInt(a.total || 0, 10);
    });

    let etiquetas = [];
    let valores = [];

    datosOrdenados.forEach(function (item) {
        etiquetas.push(item.area || 'Sin área');

        valores.push(parseInt(item.total || 0, 10));
    });

    let alturaGrafico = Math.max(320, etiquetas.length * 45);

    $('#contenedor_tickets_area').html(`

        <div
            style="
                position: relative;
                height: ${alturaGrafico}px;
            "
        >

            <canvas
                id="chart_tickets_area">
            </canvas>

        </div>

    `);

    let canvas = document.getElementById('chart_tickets_area');

    if (!canvas) {
        return;
    }

    chartTicketsArea = new Chart(canvas, {
        type: 'bar',

        data: {
            labels: etiquetas,

            datasets: [
                {
                    label: 'Tickets',

                    data: valores,

                    backgroundColor: '#6c757d',

                    borderColor: '#5a6268',

                    borderWidth: 1,

                    borderRadius: 3,

                    barThickness: 24,
                },
            ],
        },

        options: {
            indexAxis: 'y',

            responsive: true,

            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },

                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let valor = parseInt(context.raw || 0, 10);

                            return valor + ' ticket' + (valor === 1 ? '' : 's');
                        },
                    },
                },
            },

            scales: {
                x: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,

                        stepSize: 1,
                    },

                    grid: {
                        color: 'rgba(0,0,0,0.06)',
                    },
                },

                y: {
                    grid: {
                        display: false,
                    },

                    ticks: {
                        autoSkip: false,
                    },
                },
            },
        },
    });
}

/*
 * =========================================================
 * LOADING
 * =========================================================
 */

function mostrarCargandoDashboard() {
    let loading = `

        <div class="text-center text-muted py-5">

            <i
                class="
                    fas
                    fa-spinner
                    fa-spin
                    fa-2x
                    mb-3
                "
            ></i>

            <p class="mb-0">

                Cargando información...

            </p>

        </div>

    `;

    $('#contenedor_comportamiento_tickets').html(loading);

    $('#contenedor_antiguedad_backlog').html(loading);

    $('#contenedor_tickets_categoria').html(loading);

    $('#contenedor_tickets_area').html(loading);
}

/*
 * =========================================================
 * SIN DATOS
 * =========================================================
 */

function mostrarSinDatos(contenedor, mensaje) {
    let html = `

        <div class="text-center text-muted py-5">

            <i
                class="
                    fas
                    fa-info-circle
                    fa-2x
                    mb-3
                "
            ></i>

            <p class="mb-0">

                ${escaparHtml(mensaje)}

            </p>

        </div>

    `;

    $(contenedor).html(html);
}

/*
 * =========================================================
 * LIMPIAR DASHBOARD
 * =========================================================
 */

function limpiarDashboardGerencial() {
    $('#kpi_tickets_recibidos').text('0');

    $('#kpi_tickets_cerrados').text('0');

    $('#kpi_tickets_pendientes').text('0');

    $('#kpi_cumplimiento').text('0');

    $('#indicador_porcentaje_cierre').text('0%');

    $('#indicador_cumplimiento').text('0%');

    $('#indicador_backlog_critico').text('0');

    mostrarSinDatos('#contenedor_comportamiento_tickets', 'No hay información disponible.');

    mostrarSinDatos('#contenedor_antiguedad_backlog', 'No hay información disponible.');

    mostrarSinDatos('#contenedor_tickets_categoria', 'No hay información disponible.');

    mostrarSinDatos('#contenedor_tickets_area', 'No hay información disponible.');
}

/*
 * =========================================================
 * FORMATEAR NÚMERO
 * =========================================================
 */

function formatearNumero(valor) {
    return new Intl.NumberFormat('es-CO').format(valor);
}

/*
 * =========================================================
 * FORMATEAR PORCENTAJE
 * =========================================================
 */

function formatearPorcentaje(valor) {
    let numero = parseFloat(valor);

    if (isNaN(numero)) {
        numero = 0;
    }

    return numero.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 1,
    });
}

/*
 * =========================================================
 * ESCAPAR HTML
 * =========================================================
 */

function escaparHtml(valor) {
    return $('<div>').text(valor).html();
}

/*
 * =========================================================
 * INICIAR
 * =========================================================
 */

init();

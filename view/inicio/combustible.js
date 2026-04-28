/* ── combustible.js ── */

$(document).ready(function () {

    /* ── daterangepicker filtros globales ── */
    $('#comb_fechas').daterangepicker({
        locale: {
            format: "YYYY-MM-DD",
            separator: " / ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1
        },
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Año actual': [moment().startOf('year'), moment().endOf('year')],
            'Año pasado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
        locale: {
            format: "YYYY-MM-DD",
            separator: " / ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            customRangeLabel: "Rango personalizado",  // <-- agrega esto
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1
        },
        opens: "right",
        drops: "down",
        autoUpdateInput: false
    });

    $('#comb_fechas').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#comb_fechas').on('cancel.daterangepicker', function () {
        $(this).val('');
    });

    /* ── cargar combos placas y obras ── */
    initSelect2Dynamic();
    cargarPlacasCombustible();

    /* ── buscar ── */
    $('#btnBuscarComb').on('click', function () {
        var fechas = $('#comb_fechas').val();
        var fechaIni = fechas ? fechas.split(' / ')[0] : '';
        var fechaFin = fechas ? fechas.split(' / ')[1] : '';
        var placa = $('#comb_placa').val() || '';
        var obra = $('#comb_obra').val() || '';

        /* detectar tipo del vehículo seleccionado */
        var tipoId = parseInt($('#comb_placa option:selected').data('tipo')) || 0;
        var esMaquinaria = [2, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 17].indexOf(tipoId) !== -1;

        if (esMaquinaria) {
            $('#tituloGrafico').text('EVOLUCIÓN GL/HORA — MAQUINARIA');
            $('#tituloTabla').text('DETALLE GL/HORA — MAQUINARIA');
            cargarHorometro(placa, obra, fechaIni, fechaFin);
        } else {
            $('#tituloGrafico').text('EVOLUCIÓN KM/GL — VEHÍCULOS');
            $('#tituloTabla').text('DETALLE KM/GL — VEHÍCULOS');
            cargarGraficoIndividual(placa, obra, fechaIni, fechaFin);
            cargarTablaKmGal(placa, obra, fechaIni, fechaFin);
        }
    });

    /* ── limpiar ── */
    $('#btnLimpiarComb').on('click', function () {
        /* limpiar filtros */
        $('#comb_fechas').val('');
        $('#comb_placa').val('').trigger('change');  // trigger limpia el combo obras
        $('#comb_obra').empty().append('<option value="">-- Todas --</option>');

        /* limpiar gráfico */
        $('#divGraficoIndividual').empty();
        $('#promedio_individual').empty();

        /* limpiar tabla */
        $('#tbodyKmGal').empty();
    });

});

function cargarPlacasCombustible() {
    $.ajax({
        url: '../../controller/Despachos.php?op=comboVehiculo',
        type: 'POST',
        success: function (html) {
            $('#comb_placa, #comb_placa_individual, #comb_placas_comparativo')
                .empty()
                .append('<option value="">-- Todas --</option>')
                .append(html);
        }
    });
}

/* ── al cambiar placa individual — cargar obras del vehículo ── */
$('#comb_placa').on('change', function () {
    var vehi_id = $(this).val();
    $('#comb_obra').empty().append('<option value="">-- Todas --</option>');

    if (!vehi_id) return;

    $.ajax({
        url: '../../controller/Despachos.php?op=listarObrasPorVehiculo',
        type: 'POST',
        dataType: 'json',
        data: { vehi_id: vehi_id },
        success: function (res) {
            if (res.success) {
                $.each(res.data, function (i, o) {
                    $('#comb_obra').append(
                        '<option value="' + o.obras_id + '">' + o.obras_codigo + " - " + o.obras_nom + '</option>'
                    );
                });
            }
        }
    });
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

/* ── gráfico individual ── */

function cargarGraficoIndividual(placa, obra, fechaIni, fechaFin) {
    if (!placa) {
        placa = $('#comb_placa_individual').val() || '';
    }

    if (!placa) {
        $('#divGraficoIndividual').html('<p class="text-center text-muted mt-4">Selecciona una placa.</p>');
        return;
    }

    $.ajax({
        url: '../../controller/Despachos.php?op=kmGalIndividual',
        type: 'POST',
        dataType: 'json',
        data: {
            vehi_id: placa,
            obra: obra,
            fechaIni: fechaIni,
            fechaFin: fechaFin
        },
        beforeSend: function () {
            $('#divGraficoIndividual').html('<p class="text-center text-muted mt-4"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>');
        },
        success: function (res) {
            if (res.success && res.data.length) {
                renderGraficoIndividual(res.data);
            } else {
                $('#divGraficoIndividual').html('<p class="text-center text-muted mt-4">Sin datos para mostrar.</p>');
            }
        },
        error: function () {
            $('#divGraficoIndividual').html('<p class="text-center text-danger mt-4">Error al cargar datos.</p>');
        }
    });
}

function renderGraficoIndividual(data) {
    $('#divGraficoIndividual').empty().html('<canvas id="canvasIndividual"></canvas>');

    var labels = data.map(function (d) { return d.desp_fech; });
    var valores = data.map(function (d) { return parseFloat(d.km_por_galon) || 0; });
    var promedio = (valores.reduce(function (a, b) { return a + b; }, 0) / valores.length).toFixed(2);

    var ctx = document.getElementById('canvasIndividual').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'KM/GL',
                data: valores,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#17a2b8',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ' KM/GL: ' + ctx.parsed.y.toFixed(2);
                        }
                    }
                },
                /* etiquetas sobre cada punto */
                datalabels: {
                    display: true,
                    align: 'top',
                    anchor: 'end',
                    color: '#17a2b8',
                    font: { size: 11, weight: 'bold' },
                    formatter: function (value) {
                        return value.toFixed(2);
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 45,
                        font: { size: 10 }
                    },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'km/gl',        // <-- título del eje Y
                        color: '#6c757d',
                        font: { size: 11 }
                    },
                    ticks: {
                        callback: function (value) {
                            return value.toFixed(1);  // <-- solo número sin medida
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    /* promedio debajo del gráfico */
    $('#promedio_individual').html(
        'Promedio: <strong style="color:#17a2b8">' + promedio + ' km/gl</strong>'
    );
}
function cargarTablaKmGal(placa, obra, fechaIni, fechaFin) {

    /* destruir si existe */
    if ($.fn.DataTable.isDataTable('#tablaKmGal')) {
        $('#tablaKmGal').DataTable().destroy();
    }
    $('#tbodyKmGal').empty();

    $.ajax({
        url: '../../controller/Despachos.php?op=kmGalIndividual',
        type: 'POST',
        dataType: 'json',
        data: {
            vehi_id: placa,
            obra: obra,
            fechaIni: fechaIni,
            fechaFin: fechaFin
        },
        success: function (res) {
            if (res.success && res.data.length) {
                $.each(res.data, function (i, d) {
                    $('#tbodyKmGal').append(
                        '<tr>' +
                        '<td>' + d.desp_fech + '</td>' +
                        '<td>' + d.desp_galones + '</td>' +
                        '<td>' + Number(d.km_hr_anterior).toLocaleString('es-CO') + '</td>' +
                        '<td>' + Number(d.km_hr_actual).toLocaleString('es-CO') + '</td>' +
                        '<td>' + Number(d.diferencia).toLocaleString('es-CO') + '</td>' +
                        '<td><strong>' + parseFloat(d.km_por_galon).toFixed(2) + '</strong></td>' +
                        '</tr>'
                    );
                });
            }

            $('#tablaKmGal').DataTable({
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay datos disponibles",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    }
                },
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                columnDefs: [
                    { targets: [1, 2, 3, 4, 5], className: 'text-right' }
                ],
                bDestroy: true
            });
        },
        error: function () {
            $('#tbodyKmGal').html(
                '<tr><td colspan="7" class="text-center text-danger">Error al cargar datos.</td></tr>'
            );
        }
    });
}

function cargarHorometro(placa, obra, fechaIni, fechaFin) {
    if (!placa) {
        $('#divGraficoIndividual').html('<p class="text-center text-muted mt-4">Selecciona una placa.</p>');
        return;
    }

    $.ajax({
        url: '../../controller/Despachos.php?op=glHoraIndividual',
        type: 'POST',
        dataType: 'json',
        data: {
            vehi_id:  placa,
            obra:     obra,
            fechaIni: fechaIni,
            fechaFin: fechaFin
        },
        beforeSend: function () {
            $('#divGraficoIndividual').html('<p class="text-center text-muted mt-4"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>');
        },
        success: function (res) {
            if (res.success && res.data && res.data.length) {
                renderGraficoHorometro(res.data);
                cargarTablaGlHora(res.data);
            } else {
                $('#divGraficoIndividual').html('<p class="text-center text-muted mt-4">Sin datos para mostrar.</p>');
                if ($.fn.DataTable.isDataTable('#tablaKmGal')) {
                    $('#tablaKmGal').DataTable().destroy();
                }
                $('#tbodyKmGal').empty();
            }
        },
        error: function () {
            $('#divGraficoIndividual').html('<p class="text-center text-danger mt-4">Error al cargar datos.</p>');
        }
    });
}

function cargarTablaGlHora(data) {
    if ($.fn.DataTable.isDataTable('#tablaKmGal')) {
        $('#tablaKmGal').DataTable().destroy();
    }

    var $tbody = $('#tbodyKmGal').empty();

    $.each(data, function (i, d) {
        $tbody.append(
            '<tr>' +
                '<td>' + d.desp_fech + '</td>' +
                '<td>' + d.desp_galones + '</td>' +
                '<td>' + Number(d.hr_anterior).toLocaleString('es-CO') + '</td>' +
                '<td>' + Number(d.hr_actual).toLocaleString('es-CO')   + '</td>' +
                '<td>' + Number(d.diferencia).toLocaleString('es-CO')  + '</td>' +
                '<td><strong>' + parseFloat(d.gl_por_hora).toFixed(2) + '</strong></td>' +
            '</tr>'
        );
    });

    $('#tablaKmGal').DataTable({
        language: {
            processing:   "Procesando...",
            search:       "Buscar:",
            lengthMenu:   "Mostrar _MENU_ registros",
            info:         "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty:    "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            zeroRecords:  "No se encontraron resultados",
            emptyTable:   "No hay datos disponibles",
            paginate: {
                first: "Primero", previous: "Anterior",
                next:  "Siguiente", last: "Último"
            }
        },
        order:      [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        columnDefs: [{ targets: [1,2,3,4,5], className: 'text-right' }],
        bDestroy:   true
    });
}

function renderGraficoHorometro(data) {
    $('#divGraficoIndividual').empty().html('<canvas id="canvasHorometro"></canvas>');

    var labels = data.map(function (d) { return d.desp_fech; });
    var valores = data.map(function (d) { return parseFloat(d.gl_por_hora) || 0; });
    var promedio = (valores.reduce(function (a, b) { return a + b; }, 0) / valores.length).toFixed(2);

    var ctx = document.getElementById('canvasHorometro').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'GL/HORA',
                data: valores,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23,162,184,0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#17a2b8',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ' GL/Hora: ' + ctx.parsed.y.toFixed(2);
                        }
                    }
                },
                datalabels: {
                    display: true,
                    align: 'top',
                    anchor: 'end',
                    color: '#17a2b8',
                    font: { size: 11, weight: 'bold' },
                    formatter: function (value) {
                        return value.toFixed(2);
                    }
                }
            },
            scales: {
                x: {
                    ticks: { maxRotation: 45, font: { size: 10 } },
                    grid: { display: false }
                },
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'gl/hora',
                        color: '#6c757d',
                        font: { size: 11 }
                    },
                    ticks: {
                        callback: function (value) { return value.toFixed(2); }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    $('#promedio_horometro').html(
        'Promedio: <strong style="color:#17a2b8">' + promedio + ' gl/hora</strong>'
    );
}



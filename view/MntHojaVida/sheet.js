function init(){
    initSelect2Dynamic();
}

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

$('#filtroFechas').daterangepicker({
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

/* ── poblar el input al aplicar ── */
$('#filtroFechas').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
});

/* ── limpiar al cancelar ── */
$('#filtroFechas').on('cancel.daterangepicker', function() {
    $(this).val('');
});

$(document).ready(function () {

    /* ── cargar combo vehículos ── */
    $.post(
        "../../controller/Vehiculo.php?op=comboVehiculoPreop",
        function (data, status) {
            $("#filtroVehiculo").html(data);
        }
    );
    

    /* ── buscar ── */
    $('#btnBuscar').on('click', function () {
        var id_vehiculo = $('#filtroVehiculo').val();
        var fechas = $('#filtroFechas').val();
        var tipo_mtto = $('#filtroTipoMtto').val();

        if (!id_vehiculo) {
            Swal.fire({ icon: 'warning', title: 'Selecciona un vehículo', showConfirmButton: false, timer: 1500 });
            return;
        }

        var fechaIni = '';
        var fechaFin = '';

        if (fechas !== '') {
            var rango = fechas.split(' / ');
            fechaIni = rango[0] || '';
            fechaFin = rango[1] || '';
        }

        $.ajax({
            url: '../../controller/HojaVida.php?op=listarHV',
            type: 'POST',
            dataType: 'json',
            data: {
                id_vehiculo: id_vehiculo,
                fechaIni: fechaIni,
                fechaFin: fechaFin,
                tipo_mtto: tipo_mtto
            },
            beforeSend: function () {
                $('#btnBuscar').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Buscando...');
            },
            success: function (res) {
                if (res.success) {
                    cargarEquipo(res.data.equipo, res.data.reportes);
                    cargarHistorial(res.data.reportes);
                    $('#cardEquipo').show();
                    $('#cardHistorial').show();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            },
            complete: function () {
                $('#btnBuscar').prop('disabled', false)
                    .html('<i class="fas fa-search"></i> Buscar');
            }
        });
    });

    /* ── limpiar ── */
    $('#btnLimpiar').on('click', function () {
        $('#filtroVehiculo').val('');
        $('#filtroFechas').val('');
        $('#filtroTipoMtto').val('');
        $('#cardEquipo').hide();
        $('#cardHistorial').hide();
        $('#tbodyHojaVida').empty();
    });

    /* ── expandir / colapsar actividades ── */
    $(document).on('click', '.btn-expandir', function () {
        var $btn = $(this);
        var $fila = $btn.closest('tr');
        var $actv = $fila.next('.fila-actividades');

        if ($actv.is(':visible')) {
            $actv.hide();
            $btn.html('▶').css('background', '#6c757d');
        } else {
            $actv.show();
            $btn.html('▼').css('background', '#007bff');
        }
    });

    /* ── exportar PDF ── */
$('#btnExportarPDF').on('click', function () {
    var id_vehiculo = $('#filtroVehiculo').val();
    var fechas      = $('#filtroFechas').val();
    var partes      = fechas ? fechas.split(' / ') : [];
    var fechaIni    = partes[0] ? partes[0].trim() : '';
    var fechaFin    = partes[1] ? partes[1].trim() : '';

    console.log('fechas raw:', fechas);
    console.log('fechaIni:', fechaIni);
    console.log('fechaFin:', fechaFin);
    console.log('URL:', BASE_URL + 'view/PDF/HojaVida.php?id_vehiculo=' + id_vehiculo + '&fechaIni=' + fechaIni + '&fechaFin=' + fechaFin);

    window.open(
        BASE_URL + '/view/PDF/HojaVida.php?id_vehiculo=' + id_vehiculo +
        '&fechaIni=' + fechaIni +
        '&fechaFin=' + fechaFin ,
        '_blank'
    );
});

});

/* ── poblar datos del equipo ── */
function cargarEquipo(equipo, reportes) {
    var totalValor = 0;

    $.each(reportes, function (i, rep) {
        totalValor += parseFloat(rep.total_valor_repuestos || 0);  // <-- usa el valor ya calculado en PHP
    });

    $('#equipoNombre').text(equipo.tipo_nombre       || '—');
    $('#equipoPlaca').text(equipo.vehi_placa         || '—');
    $('#equipoCodigo').text(equipo.vehi_codigo       || '—');
    $('#equipoUbicacion').text(equipo.vehi_ubicacion || '—');
    $('#equipoTotal').text(reportes.length + ' mantenimientos');
    $('#equipoValor').text(totalValor.toLocaleString('es-CO', { style: 'currency', currency: 'COP' }));
}

/* ── poblar historial ── */
function cargarHistorial(reportes) {
    var $tbody = $('#tbodyHojaVida');
    $tbody.empty();

    if (!reportes.length) {
        $tbody.append('<tr><td colspan="9" class="text-center">Sin registros encontrados.</td></tr>');
        return;
    }

    $.each(reportes, function (i, rep) {

        var badge = rep.nombre_tipo_mantenimiento === 'Preventivo'
            ? '<span class="badge badge-success">Preventivo</span>'
            : '<span class="badge badge-warning">Correctivo</span>';

        var kmAnt = rep.lectura_anterior ? Number(rep.lectura_anterior).toLocaleString('es-CO') : '—';
        var kmAct = rep.lect_soli ? Number(rep.lect_soli).toLocaleString('es-CO') : '—';
        var fecha = rep.fech_creac_soli ? rep.fech_creac_soli.substring(0, 10) : '—';

        /* fila principal */
        var filaPrincipal = '<tr>' +
            '<td class="text-center">' +
            '<button class="btn btn-sm btn-secondary btn-expandir" title="Ver actividades">▶</button>' +
            '</td>' +
            '<td>' + fecha + '</td>' +
            '<td>' + (rep.repo_mtto_num_reporte || '—') + '</td>' +
            '<td>' + (rep.num_otm || '—') + '</td>' +
            '<td>' + badge + '</td>' +
            '<td>' + kmAnt + '</td>' +
            '<td>' + kmAct + '</td>' +
            '<td>' + (rep.obras_nombre || '—') + '</td>' +
            '<td>' + Number(rep.total_valor_repuestos || 0).toLocaleString('es-CO', { style: 'currency', currency: 'COP' }) + '</td>'+
            '<td>' +
            '<div class="btn-group">' +
            '<button class="btn btn-sm btn-danger" onclick="verPDF(' + rep.repo_mtto_id + ')"><i class="fas fa-file-pdf"></i></button>' +
            '</div>' +
            '</td>' +
            '</tr>';

        /* fila actividades oculta */
        var filaActividades = '<tr class="fila-actividades" style="display:none;">' +
            '<td colspan="9" style="background:#eef2ff;padding:0">' +
            '<div style="padding:10px 20px">' +
            buildTablaActividades(rep.actividades) +
            '</div>' +
            '</td>' +
            '</tr>';

        $tbody.append(filaPrincipal + filaActividades);
    });
}

/* ── construir tabla de actividades ── */
function buildTablaActividades(repuestos) {
    if (!repuestos || !repuestos.length) {
        return '<p class="text-muted" style="margin:6px 0">Sin repuestos registrados.</p>';
    }

    var html = '<table class="table table-sm table-bordered" style="font-size:12px;margin:0">' +
        '<thead class="thead-light">' +
            '<tr>' +
                '<th>N°</th>' +
                '<th>Documento</th>' +
                '<th>Referencia</th>' +
                '<th>Cantidad</th>' +
                '<th>Vlr. Neto</th>' +
                '<th>Proveedor</th>' +
            '</tr>' +
        '</thead>' +
        '<tbody>';

    $.each(repuestos, function (i, rep) {
        html += '<tr>' +
            '<td>' + (i + 1) + '</td>' +
            '<td>' + (rep.rpts_docu  || '—') + '</td>' +
            '<td>' + (rep.rpts_refr  || '—') + '</td>' +
            '<td>' + (rep.rpts_cant  || '—') + '</td>' +
            '<td>' + (Number(rep.rpts_vlr_neto || 0).toLocaleString('es-CO', { style: 'currency', currency: 'COP' })) + '</td>' +
            '<td>' + (rep.rpts_prov  || '—') + '</td>' +
        '</tr>';
    });

    html += '</tbody></table>';
    return html;
}

/* ── ver PDF OTM ── */
function verPDF(id_repo) {
    window.open(BASE_URL + '/view/PDF/ReporteMtto.php?id=' + id_repo, '_blank');
}

init();
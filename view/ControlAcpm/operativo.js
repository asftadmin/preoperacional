let estadoOperativoAcpm = null;
let secuenciaOperativoAcpm = 0;
let guardandoOperativoAcpm = false;

function galonesOperativos(valor) {
    return Number(valor).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 4 }) + ' gal';
}

function mostrarEstadoOperativo(data) {
    estadoOperativoAcpm = data;
    $('#operativo_error').addClass('d-none').text('');
    const r = data.resumen;
    $('#kpi_disponibilidad_operativa, #resumen_disponibilidad_operativa').text(galonesOperativos(r.saldo_final));
    $('#operativo_apertura').text(galonesOperativos(r.apertura));
    $('#operativo_despachos').text('− ' + galonesOperativos(r.despachado_dia));
    $('#operativo_origen_apertura').text(r.origen_apertura === 'cierre'
        ? 'Apertura tomada del cierre guardado del ' + r.fecha_base + '.'
        : 'Apertura tomada del saldo inicial SIESA del corte y de los despachos posteriores.');
    $('#operativo_fecha').text(data.hoy);
    $('#operativo_diario').text('Apertura ' + galonesOperativos(r.apertura) + ' − despachado '
        + galonesOperativos(r.despachado_dia) + ' = disponible ' + galonesOperativos(r.saldo_final));
    const cierre = data.proximo_cierre;
    $('#operativo_fecha_cierre').text(cierre ? cierre.fecha : 'Todos los días están cerrados');
    $('#operativo_preview_cierre').text(cierre ? 'Apertura: ' + galonesOperativos(cierre.apertura)
        + ' · Despachado: ' + galonesOperativos(cierre.despachado_dia) + ' · Saldo de cierre: ' + galonesOperativos(cierre.saldo_final)
        : 'El próximo día abrirá con el último saldo de cierre.');
    $('#btn_cerrar_operativo').prop('disabled', !cierre || guardandoOperativoAcpm);
    const cuerpo = $('#operativo_cierres').empty();
    if (!data.cierres.length) $('<tr>').append($('<td>').attr('colspan', 6).text('Sin cierres registrados')).appendTo(cuerpo);
    data.cierres.forEach(function (c) {
        const tr = $('<tr>');
        [c.fecha, galonesOperativos(c.apertura), galonesOperativos(c.despachado), galonesOperativos(c.saldo_final),
            c.pendientes_documento, c.responsable].forEach(function (valor) { $('<td>').text(valor).appendTo(tr); });
        cuerpo.append(tr);
    });
}

function errorOperativo(xhr) {
    const mensaje = xhr && xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message : 'No fue posible actualizar el saldo disponible. Actualice e intente nuevamente.';
    $('#operativo_error').removeClass('d-none').text(mensaje);
    return mensaje;
}

function consultarEstadoOperativo() {
    const secuencia = ++secuenciaOperativoAcpm;
    return $.ajax({ url: '../../controller/ControlAcpm.php?op=estadoOperativo', dataType: 'json', cache: false })
        .done(function (respuesta) {
            if (secuencia === secuenciaOperativoAcpm && respuesta.status === 'success') mostrarEstadoOperativo(respuesta.data);
        }).fail(function (xhr) {
            if (secuencia !== secuenciaOperativoAcpm) return;
            estadoOperativoAcpm = null;
            $('#kpi_disponibilidad_operativa, #resumen_disponibilidad_operativa').text('Sin actualizar');
            $('#btn_cerrar_operativo').prop('disabled', true);
            errorOperativo(xhr);
        });
}

function guardarCierreAcpm(cierre) {
    if (guardandoOperativoAcpm) return;
    guardandoOperativoAcpm = true;
    ++secuenciaOperativoAcpm;
    $('#btn_cerrar_operativo').prop('disabled', true);
    return $.ajax({ url: '../../controller/ControlAcpm.php?op=cerrarDiaOperativo', type: 'POST', dataType: 'json',
        data: { fecha: cierre.fecha, firma: cierre.firma, csrf: $('meta[name="acpm-csrf"]').attr('content') }
    }).done(function (respuesta) {
        if (respuesta.status !== 'success') return;
        mostrarEstadoOperativo(respuesta.data);
        Swal.fire({ icon: 'success', title: 'Cierre guardado', text: 'Este saldo será la apertura del día siguiente.' });
    }).fail(function (xhr) {
        Swal.fire({ icon: 'warning', title: 'Cierre diario', text: errorOperativo(xhr) });
        consultarEstadoOperativo();
    }).always(function () {
        guardandoOperativoAcpm = false;
        if (estadoOperativoAcpm) mostrarEstadoOperativo(estadoOperativoAcpm);
    });
}

$(function () {
    consultarEstadoOperativo();
    $('#btn_actualizar_siesa').on('click', consultarEstadoOperativo);
    $('#btn_cerrar_operativo').on('click', function () {
        if (!estadoOperativoAcpm || !estadoOperativoAcpm.proximo_cierre) return;
        const cierre = estadoOperativoAcpm.proximo_cierre;
        Swal.fire({ icon: 'question', title: 'Cerrar el día ' + cierre.fecha,
            text: 'El día siguiente abrirá con ' + galonesOperativos(cierre.saldo_final) + '. El cierre guarda este saldo y protege los despachos del día.',
            showCancelButton: true, confirmButtonText: 'Guardar cierre', cancelButtonText: 'Cancelar'
        }).then(function (resultado) { if (resultado.isConfirmed) guardarCierreAcpm(cierre); });
    });
});

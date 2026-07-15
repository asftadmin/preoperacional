(function ($) {
    'use strict';
    const api = '../../controller/TicketsSistemas.php';
    const ticketId = $('#ticketId').val();
    const csrf = $('#csrfToken').val();

    function errorAjax(xhr) { return xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No fue posible completar la operación.'; }
    function valor(valor) { return valor === null || valor === undefined || valor === '' ? 'N/A' : valor; }
    function mostrarExitoYRecargar(titulo, mensaje) {
        return Swal.fire({
            icon: 'success',
            title: titulo,
            text: mensaje,
            confirmButtonText: 'Aceptar',
            timer: 2000,
            timerProgressBar: true,
            allowOutsideClick: false
        }).then(function () {
            window.location.reload();
        });
    }
    function claseEstado(estado) {
        return { ABIERTO: 'badge-primary', EN_PROCESO: 'badge-warning', EN_ESPERA: 'badge-info', RESUELTO: 'badge-success', CERRADO: 'badge-secondary', CANCELADO: 'badge-danger' }[estado] || 'badge-light';
    }
    function escapar(texto) { return $('<div>').text(valor(texto)).html(); }

    function pintarSeguimientos(items) {
        const contenedor = $('#listaSeguimientos').empty();
        if (!items.length) { contenedor.html('<p class="text-muted">No hay seguimientos.</p>'); return; }
        items.forEach(function (item) {
            const cambio = item.estado_anterior || item.estado_nuevo ? '<div class="text-muted small">Estado: ' + escapar(item.estado_anterior || 'N/A') + ' → ' + escapar(item.estado_nuevo || 'N/A') + '</div>' : '';
            contenedor.append('<div><i class="fas fa-comment bg-info"></i><div class="timeline-item"><span class="time"><i class="far fa-clock"></i> ' + escapar(item.fecha_creacion) + '</span><h3 class="timeline-header">' + escapar(item.tipo) + '</h3><div class="timeline-body" style="white-space:pre-wrap">' + escapar(item.comentario) + cambio + '</div></div></div>');
        });
        contenedor.append('<div><i class="far fa-clock bg-gray"></i></div>');
    }

    function cargarDetalle() {
        $.getJSON(api + '?op=detalle&id=' + encodeURIComponent(ticketId)).done(function (respuesta) {
            const data = respuesta.data, t = data.ticket;
            $('#numeroTicket').text(t.ticket_numero);
            $('#estadoBadge').attr('class', 'badge float-right ' + claseEstado(t.estado)).text(t.estado.replace(/_/g, ' '));
            $('#asuntoTicket').text(t.asunto); $('#descripcionTicket').text(t.descripcion);
            $('#tipoTicket').text(valor(t.tipo)); $('#categoriaTicket').text(valor(t.categoria)); $('#canalTicket').text(valor(t.canal));
            $('#fechaTicket').text(valor(t.fecha_creacion)); $('#ubicacionTicket').text(valor(t.ubicacion)); $('#equipoTicket').text(valor(t.equipo));
            const empleado = data.empleado_api || { documento: t.empleado_documento, nombre: t.empleado_nombre, correo: t.empleado_correo, cargo: t.empleado_cargo, area: t.empleado_area };
            $('#empleadoDocumento').text(valor(empleado.documento)); $('#empleadoNombre').text(valor(empleado.nombre)); $('#empleadoCorreo').text(valor(empleado.correo)); $('#empleadoCargo').text(valor(empleado.cargo)); $('#empleadoArea').text(valor(empleado.area));
            $('#origenEmpleado').text(data.empleado_api ? 'Información vigente de la API' : 'Información guardada con el ticket');
            if (!data.empleado_api && data.mensaje_api) $('#alertaApi').removeClass('d-none').text('No fue posible actualizar el empleado desde la API: ' + data.mensaje_api);
            $('#gestionEstado').val(t.estado); $('#gestionPrioridad').val(t.prioridad); $('#gestionSolucion').val(t.solucion || '');
            const responsables = $('#gestionResponsable').empty().append('<option value="">Sin asignar</option>');
            (data.responsables || []).forEach(function (r) { responsables.append(new Option(r.nombre + (r.rol ? ' · ' + r.rol : ''), r.user_id)); });
            responsables.val(t.responsable_id || '');
            pintarSeguimientos(data.seguimientos || []);
        }).fail(function (xhr) { Swal.fire('Error', errorAjax(xhr), 'error').then(function () { window.location.href = 'tickets.php'; }); });
    }

    $('#formGestion').on('submit', function (evento) {
        evento.preventDefault();
        const estado = $('#gestionEstado').val();
        if ((estado === 'RESUELTO' || estado === 'CERRADO') && !$.trim($('#gestionSolucion').val())) { Swal.fire('Solución requerida', 'Documente la solución antes de resolver o cerrar.', 'warning'); return; }
        const boton = $('#btnGuardarGestion').prop('disabled', true);
        const datos = $(this).serializeArray(); datos.push({ name: 'ticket_id', value: ticketId }, { name: 'csrf_token', value: csrf });
        $.ajax({ url: api + '?op=actualizarGestion', method: 'POST', data: $.param(datos), dataType: 'json' })
            .done(function (r) { mostrarExitoYRecargar('Gestión actualizada', r.message); })
            .fail(function (xhr) { Swal.fire('Error', errorAjax(xhr), 'error'); })
            .always(function () { boton.prop('disabled', false); });
    });

    $('#formSeguimiento').on('submit', function (evento) {
        evento.preventDefault();
        const formulario = this;
        $.ajax({ url: api + '?op=agregarSeguimiento', method: 'POST', dataType: 'json', data: { ticket_id: ticketId, comentario: $(formulario).find('[name="comentario"]').val(), csrf_token: csrf } })
            .done(function (r) {
                formulario.reset();
                mostrarExitoYRecargar('Seguimiento registrado', r.message);
            })
            .fail(function (xhr) { Swal.fire('Error', errorAjax(xhr), 'error'); });
    });

    cargarDetalle();
})(jQuery);

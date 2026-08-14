(function ($) {
    'use strict';

    const api = '../../controller/InventarioEquipos.php';
    const csrf = $('#csrfInventario').val();
    const equipoId = $('#equipoConsultaId').val();
    let catalogos = {};
    let responsables = [];
    let continuarRegistrando = false;

    function escapar(valor) {
        return $('<div>').text(valor === null || valor === undefined || valor === '' ? 'N/A' : valor).html();
    }

    function mensajeError(xhr) {
        return xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message : 'No fue posible completar la operación.';
    }

    function opciones(items) {
        let html = '<option value="">Seleccione</option>';
        (items || []).forEach(function (item) {
            html += '<option value="' + escapar(item.id) + '">' + escapar(item.nombre) + '</option>';
        });
        return html;
    }

    function opcionesResponsables() {
        let html = '<option value="">Seleccione</option>';
        responsables.forEach(function (item) {
            html += '<option value="' + escapar(item.id) + '">' + escapar(item.nombre)
                + (item.cargo ? ' · ' + escapar(item.cargo) : '') + '</option>';
        });
        return html;
    }

    function filaComponente() {
        return '<div class="row componente-inicial border rounded p-2 mb-2">'
            + '<div class="form-group col-md-2 mb-0"><label class="small">Tipo</label><select class="form-control componente-tipo">' + opciones(catalogos.tipos_componente) + '</select></div>'
            + '<div class="form-group col-md-2 mb-0"><label class="small">Estado</label><select class="form-control componente-estado">' + opciones(catalogos.estados_componente) + '</select></div>'
            + '<div class="form-group col-md-2 mb-0"><label class="small">Marca</label><input class="form-control componente-marca" maxlength="80"></div>'
            + '<div class="form-group col-md-2 mb-0"><label class="small">Modelo</label><input class="form-control componente-modelo" maxlength="100"></div>'
            + '<div class="form-group col-md-2 mb-0"><label class="small">Serial</label><input class="form-control componente-serial text-uppercase" maxlength="120"></div>'
            + '<div class="form-group col-md-1 mb-0"><label class="small">Observación</label><input class="form-control componente-observacion" maxlength="2000"></div>'
            + '<div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-outline-danger btn-quitar-fila"><i class="fas fa-times"></i></button></div>'
            + '</div>';
    }

    function obtenerComponentes() {
        const componentes = [];
        $('.componente-inicial').each(function () {
            const tipo = $(this).find('.componente-tipo').val();
            const estado = $(this).find('.componente-estado').val();
            if (tipo || estado) {
                componentes.push({
                    tipo_componente_id: tipo,
                    estado_componente_id: estado,
                    marca: $(this).find('.componente-marca').val(),
                    modelo: $(this).find('.componente-modelo').val(),
                    serial: $(this).find('.componente-serial').val(),
                    observacion: $(this).find('.componente-observacion').val()
                });
            }
        });
        return componentes;
    }

    function mostrarComponentesExistentes(componentes) {
        if (!componentes || !componentes.length) return;
        let html = '<div class="callout callout-info"><strong>Componentes asociados</strong>'
            + '<div class="table-responsive mt-2"><table class="table table-sm table-bordered mb-0">'
            + '<thead><tr><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Serial</th><th>Estado</th></tr></thead><tbody>';
        componentes.forEach(function (item) {
            html += '<tr><td>' + escapar(item.tipo) + '</td><td>' + escapar(item.marca)
                + '</td><td>' + escapar(item.modelo) + '</td><td>' + escapar(item.serial)
                + '</td><td>' + escapar(item.estado) + '</td></tr>';
        });
        html += '</tbody></table></div><small>Los componentes existentes se administran desde la bandeja del inventario.</small></div>';
        $('#componentesExistentes').html(html).removeClass('d-none');
    }

    function cargarEquipo() {
        if (!equipoId) return $.Deferred().resolve().promise();
        return $.getJSON(api + '?op=detalle&id=' + encodeURIComponent(equipoId)).done(function (respuesta) {
            const equipo = respuesta.data;
            Object.keys(equipo).forEach(function (campo) {
                const control = $('#formEquipoPagina [name="' + campo + '"]');
                if (control.length) control.val(equipo[campo]).trigger('change');
            });
            mostrarComponentesExistentes(equipo.componentes || []);
            $('#componentesNuevos').addClass('d-none');
        }).fail(function (xhr) {
            Swal.fire('No fue posible cargar el equipo', mensajeError(xhr), 'error')
                .then(function () { window.location.href = 'inventario.php'; });
        });
    }

    function iniciar() {
        $.getJSON(api + '?op=inicial').done(function (respuesta) {
            catalogos = respuesta.data.catalogos || {};
            responsables = respuesta.data.responsables || [];
            $('#equipoTipo').html(opciones(catalogos.tipos_equipo));
            $('#equipoEstado').html(opciones(catalogos.estados_equipo));
            $('#equipoUbicacion').html(opciones(catalogos.ubicaciones));
            $('#equipoCustodio').html(opcionesResponsables());
            $('#equipoTipo, #equipoEstado, #equipoUbicacion, #equipoCustodio')
                .select2({ theme: 'bootstrap4', width: '100%' });
            cargarEquipo();
        }).fail(function (xhr) {
            Swal.fire('No fue posible iniciar el formulario', mensajeError(xhr), 'error')
                .then(function () { window.location.href = 'inventario.php'; });
        });
    }

    $('#btnAgregarComponenteFila').on('click', function () {
        $('#componentesIniciales').append(filaComponente());
    });
    $(document).on('click', '.btn-quitar-fila', function () {
        $(this).closest('.componente-inicial').remove();
    });
    $('#formEquipoPagina button[type="submit"]').on('click', function () {
        continuarRegistrando = $(this).data('continuar') === 1;
    });

    $('#formEquipoPagina').on('submit', function (evento) {
        evento.preventDefault();
        const formulario = this;
        if (!formulario.checkValidity()) {
            formulario.reportValidity();
            return;
        }
        const botones = $(formulario).find('button[type="submit"]').prop('disabled', true);
        const datos = $(formulario).serializeArray();
        datos.push({ name: 'csrf_token', value: csrf });
        datos.push({ name: 'componentes_json', value: JSON.stringify(obtenerComponentes()) });
        $.ajax({
            url: api + '?op=guardarEquipo',
            method: 'POST',
            data: $.param(datos),
            dataType: 'json'
        }).done(function (respuesta) {
            Swal.fire('Equipo guardado', respuesta.message, 'success').then(function () {
                window.location.href = continuarRegistrando ? 'registrar_equipo.php' : 'inventario.php';
            });
        }).fail(function (xhr) {
            Swal.fire('No fue posible guardar', mensajeError(xhr), 'error');
        }).always(function () {
            botones.prop('disabled', false);
        });
    });

    iniciar();
})(jQuery);

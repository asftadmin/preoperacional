(function ($) {
    'use strict';
    const api = '../../controller/TicketsSistemas.php';
    const texto = $.fn.dataTable.render.text();
    let tabla;

    function mensajeError(xhr) {
        return xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No fue posible completar la operación.';
    }

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

    function cargarCategorias() {
        $.getJSON(api + '?op=categorias').done(function (respuesta) {
            const select = $('#categoriaTicket').empty().append('<option value="">Seleccione</option>');
            (respuesta.data || []).forEach(function (item) {
                select.append(new Option(item.nombre, item.categoria_id));
            });
        });
    }

    function inicializarEmpleado() {
        $('#empleadoSelect').select2({
            theme: 'bootstrap4', dropdownParent: $('#modalTicketSistema'), minimumInputLength: 3,
            placeholder: 'Digite el documento o nombre completo', allowClear: true,
            ajax: {
                url: api + '?op=buscarEmpleado', dataType: 'json', delay: 350,
                data: function (params) { return { q: $.trim(params.term || '') }; },
                processResults: function (respuesta) { return respuesta.data || { results: [] }; },
                transport: function (params, success, failure) {
                    const request = $.ajax(params);
                    request.then(success);
                    request.fail(function (xhr) { failure(xhr); });
                    return request;
                }
            }
        }).on('select2:select', function (evento) {
            const empleado = evento.params.data.empleado;
            $('#empleadoDocumento').val(empleado.documento);
            $('#empleadoNombre').text(empleado.nombre || 'N/A');
            $('#empleadoCorreo').text(empleado.correo || 'N/A');
            $('#empleadoCargo').text(empleado.cargo || 'N/A');
            $('#empleadoArea').text(empleado.area || 'N/A');
            $('#resumenEmpleado').removeClass('d-none');
        }).on('select2:clear', function () {
            $('#empleadoDocumento').val('');
            $('#resumenEmpleado').addClass('d-none');
        });
    }

    function cargarTabla() {
        if (tabla) { tabla.ajax.reload(); return; }
        tabla = $('#tablaTicketsSistemas').DataTable({
            responsive: true, autoWidth: false, pageLength: 10, order: [],
            ajax: {
                url: api + '?op=listar', dataSrc: function (respuesta) { return respuesta.data || []; },
                data: function (data) { data.estado = $('#filtroEstado').val(); data.documento = $('#filtroDocumento').val(); data.buscar = $('#filtroBuscar').val(); },
                error: function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); }
            },
            columns: [
                { data: 'ticket_numero', render: texto },
                { data: null, render: function (d, t, row) { return t === 'display' ? $('<div>').text(row.empleado_nombre + ' · ' + row.empleado_documento).html() : row.empleado_nombre; } },
                { data: 'asunto', render: texto }, { data: 'categoria', render: texto },
                { data: 'prioridad', render: texto }, { data: 'estado', render: texto },
                { data: 'responsable', defaultContent: 'Sin asignar', render: texto },
                { data: 'fecha_creacion', render: texto },
                { data: 'ticket_id', orderable: false, searchable: false, render: function (id) { return '<a class="btn btn-info btn-sm" href="gestion.php?id=' + encodeURIComponent(id) + '" title="Gestionar"><i class="fas fa-eye"></i></a>'; } }
            ],
            language: { emptyTable: 'No hay tickets registrados', info: 'Mostrando _START_ a _END_ de _TOTAL_', lengthMenu: 'Mostrar _MENU_', search: 'Buscar:', paginate: { previous: 'Anterior', next: 'Siguiente' } }
        });
    }

    $('#btnNuevoTicket').on('click', function () { $('#formTicketSistema')[0].reset(); $('#empleadoSelect').val(null).trigger('change'); $('#empleadoDocumento').val(''); $('#resumenEmpleado').addClass('d-none'); $('#modalTicketSistema').modal('show'); });
    $('#btnFiltrar').on('click', cargarTabla);
    $('#formTicketSistema').on('submit', function (evento) {
        evento.preventDefault();
        if (!$('#empleadoDocumento').val()) { Swal.fire('Campo requerido', 'Seleccione un empleado válido.', 'warning'); return; }
        const boton = $('#btnGuardarTicket').prop('disabled', true);
        $.ajax({ url: api + '?op=crear', method: 'POST', data: $(this).serialize(), dataType: 'json' })
            .done(function (respuesta) {
                $('#modalTicketSistema').modal('hide');
                mostrarExitoYRecargar('Ticket registrado', respuesta.message);
            })
            .fail(function (xhr) { Swal.fire('No se pudo guardar', mensajeError(xhr), 'error'); })
            .always(function () { boton.prop('disabled', false); });
    });

    cargarCategorias(); inicializarEmpleado(); cargarTabla();
})(jQuery);

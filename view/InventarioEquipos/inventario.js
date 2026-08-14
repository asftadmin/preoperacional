(function ($) {
    'use strict';

    const api = '../../controller/InventarioEquipos.php';
    const csrf = $('#csrfInventario').val();
    let catalogos = {};
    let responsables = [];
    let tablaInventario = null;
    let tablaPendientes = null;

    function escapar(valor) {
        return $('<div>').text(valor === null || valor === undefined || valor === '' ? 'N/A' : valor).html();
    }

    function mensajeError(xhr) {
        return xhr.responseJSON && xhr.responseJSON.message
            ? xhr.responseJSON.message : 'No fue posible completar la operación.';
    }

    /** Fuerza la generación del acta antes de dar por terminado el flujo visual. */
    function generarYMostrarActa(url) {
        return fetch(url, { credentials: 'same-origin' }).then(function (respuesta) {
            if (!respuesta.ok) throw new Error('No fue posible generar el acta CO-F-16.');
            return respuesta.blob();
        }).then(function (archivo) {
            const urlTemporal = URL.createObjectURL(archivo);
            window.open(urlTemporal, '_blank');
            window.setTimeout(function () { URL.revokeObjectURL(urlTemporal); }, 60000);
        });
    }

    function opciones(items, vacio) {
        let html = vacio === false ? '' : '<option value="">Seleccione</option>';
        (items || []).forEach(function (item) {
            html += '<option value="' + escapar(item.id) + '">' + escapar(item.nombre) + '</option>';
        });
        return html;
    }

    function opcionesResponsables(vacio) {
        let html = vacio === false ? '' : '<option value="">Seleccione</option>';
        responsables.forEach(function (item) {
            html += '<option value="' + escapar(item.id) + '">' + escapar(item.nombre)
                + (item.cargo ? ' · ' + escapar(item.cargo) : '') + '</option>';
        });
        return html;
    }

    function iniciarSelect2() {
        $('.filtro-catalogo, .responsable-select').select2({ theme: 'bootstrap4', width: '100%' });
        iniciarSelectorEquipo($('#asignacionEquipo'), false, false);
        iniciarSelectorEquipo($('#devolucionEquipo'), true, false);
        iniciarSelectorEquipo($('#trazabilidadEquipo'), false, true);
        iniciarSelectorEmpleado();
    }

    function iniciarSelectorEquipo($elemento, activos, todos) {
        $elemento.select2({
            theme: 'bootstrap4',
            width: '100%',
            minimumInputLength: 2,
            placeholder: 'Escriba nombre, serial o código',
            allowClear: true,
            ajax: {
                url: api + '?op=buscarEquipos',
                dataType: 'json',
                delay: 350,
                data: function (params) {
                    return { q: $.trim(params.term || ''), activos: activos ? 1 : 0, todos: todos ? 1 : 0 };
                },
                processResults: function (respuesta) {
                    return respuesta.data || { results: [] };
                }
            }
        });
    }

    function iniciarSelectorEmpleado() {
        const configuracion = function (placeholder) {
            return {
                theme: 'bootstrap4',
                width: '100%',
                minimumInputLength: 3,
                placeholder: placeholder,
                allowClear: true,
                ajax: {
                    url: api + '?op=buscarEmpleado',
                    dataType: 'json',
                    delay: 350,
                    data: function (params) { return { q: $.trim(params.term || '') }; },
                    processResults: function (respuesta) { return respuesta.data || { results: [] }; }
                }
            };
        };

        $('#asignacionEmpleado').select2(configuracion('Documento o nombre del empleado'))
        .on('select2:select', function (evento) {
            const empleado = evento.params.data.empleado;
            $('#asignacionDocumento').val(empleado.documento);
            $(this).data('empleado', empleado);
            cargarJefeSugerido(empleado);
            pintarResumenAsignacion();
        }).on('select2:clear', function () {
            $('#asignacionDocumento').val('');
            $(this).removeData('empleado');
            limpiarJefeAsignacion();
            pintarResumenAsignacion();
        });

        $('#asignacionJefeEmpleado').select2(configuracion('Documento o nombre del jefe'))
        .on('select2:select', function (evento) {
            const jefe = evento.params.data.empleado;
            $('#asignacionJefeDocumento').val(jefe.documento);
            $('#asignacionJefeCargo').val(jefe.cargo || '');
            $(this).data('empleado', jefe);
            pintarResumenAsignacion();
        }).on('select2:clear', function () {
            $('#asignacionJefeDocumento, #asignacionJefeCargo').val('');
            $(this).removeData('empleado');
            pintarResumenAsignacion();
        });
    }

    function limpiarJefeAsignacion() {
        $('#asignacionJefeDocumento, #asignacionJefeCargo').val('');
        $('#asignacionJefeEmpleado').empty().trigger('change').removeData('empleado');
    }

    function cargarJefeSugerido(empleado) {
        limpiarJefeAsignacion();
        if (!empleado.jefe_documento || !empleado.jefe_nombre) return;
        const jefe = {
            documento: empleado.jefe_documento,
            nombre: empleado.jefe_nombre,
            cargo: empleado.jefe_cargo || ''
        };
        const texto = jefe.documento + ' · ' + jefe.nombre;
        $('#asignacionJefeEmpleado')
            .append(new Option(texto, jefe.documento, true, true))
            .trigger('change')
            .data('empleado', jefe);
        $('#asignacionJefeDocumento').val(jefe.documento);
        $('#asignacionJefeCargo').val(jefe.cargo);
    }

    function configurarRangos() {
        $('.rango-fecha').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD', applyLabel: 'Aplicar', cancelLabel: 'Limpiar',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            }
        }).on('apply.daterangepicker', function (evento, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function () {
            $(this).val('');
        });
    }

    function rango(id) {
        const valor = $(id).val();
        if (!valor || valor.indexOf(' - ') === -1) return ['', ''];
        return valor.split(' - ');
    }

    function cargarInicial() {
        return $.getJSON(api + '?op=inicial').done(function (respuesta) {
            catalogos = respuesta.data.catalogos || {};
            responsables = respuesta.data.responsables || [];
            $('#filtroEstado').html('<option value="">Todos</option>' + opciones(catalogos.estados_equipo, false));
            $('#filtroTipo').html('<option value="">Todos</option>' + opciones(catalogos.tipos_equipo, false));
            $('#filtroUbicacion').html('<option value="">Todas</option>' + opciones(catalogos.ubicaciones, false));
            $('#filtroResponsable').html('<option value="">Todos</option>' + opcionesResponsables(false));
            $('#devolucionEstado').html(opciones(catalogos.estados_equipo));
            $('#devolucionUbicacion').html(opciones(catalogos.ubicaciones));
            $('#asignacionFuncionario, #devolucionFuncionario, #devolucionCustodio')
                .html(opcionesResponsables());
            $('#componenteTipo').html(opciones(catalogos.tipos_componente));
            $('#componenteEstado').html(opciones(catalogos.estados_componente));
            $('#mantenimientoTipo').html(opciones(catalogos.tipos_mantenimiento));
            $('#mantenimientoEstado').html(opciones(catalogos.estados_equipo));
            $('#mantenimientoResponsable').html(opcionesResponsables());
            $('#devolucionMotivo').html(opciones(catalogos.motivos_devolucion));
            iniciarSelect2();
            configurarRangos();
            cargarTablaInventario();
        }).fail(function (xhr) {
            Swal.fire('No fue posible iniciar el módulo', mensajeError(xhr), 'error');
        });
    }

    function cargarTablaInventario() {
        if (tablaInventario) {
            tablaInventario.ajax.reload();
            return;
        }
        tablaInventario = $('#tablaInventario').DataTable({
            processing: true,
            responsive: true,
            autoWidth: false,
            order: [[0, 'asc']],
            ajax: {
                url: api + '?op=listar',
                dataSrc: function (respuesta) { return respuesta.data || []; },
                data: function (datos) {
                    const adquisicion = rango('#filtroAdquisicion');
                    const mantenimiento = rango('#filtroMantenimiento');
                    datos.estado_id = $('#filtroEstado').val();
                    datos.tipo_id = $('#filtroTipo').val();
                    datos.ubicacion_id = $('#filtroUbicacion').val();
                    datos.responsable_id = $('#filtroResponsable').val();
                    datos.marca = $('#filtroMarca').val();
                    datos.serial = $('#filtroSerial').val();
                    datos.codigo_siesa = $('#filtroSiesa').val();
                    datos.codigo_activo_fijo = $('#filtroActivo').val();
                    datos.adquisicion_desde = adquisicion[0];
                    datos.adquisicion_hasta = adquisicion[1];
                    datos.mantenimiento_desde = mantenimiento[0];
                    datos.mantenimiento_hasta = mantenimiento[1];
                },
                error: function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); }
            },
            columns: [
                { data: 'nombre' }, { data: 'tipo' }, { data: 'marca' }, { data: 'modelo' },
                { data: 'serial' }, { data: 'codigo_siesa', defaultContent: '' },
                { data: 'codigo_activo_fijo', defaultContent: '' }, { data: 'estado' },
                { data: 'ubicacion' }, { data: 'responsable' },
                { data: 'fecha_mantenimiento', defaultContent: '' },
                {
                    data: null, orderable: false, searchable: false, render: function (dato, tipo, fila) {
                        if (tipo !== 'display') return fila.equipo_id;
                        let botones = '<div class="btn-group btn-group-sm">'
                            + '<button class="btn btn-info btn-detalle" data-id="' + fila.equipo_id + '" title="Detalle"><i class="fas fa-eye"></i></button>'
                            + '<button class="btn btn-warning btn-editar" data-id="' + fila.equipo_id + '" title="Editar"><i class="fas fa-edit"></i></button>'
                            + '<button class="btn btn-secondary btn-componente" data-id="' + fila.equipo_id + '" title="Componente"><i class="fas fa-keyboard"></i></button>'
                            + '<button class="btn btn-outline-warning btn-mantenimiento" data-id="' + fila.equipo_id + '" title="Mantenimiento"><i class="fas fa-tools"></i></button>'
                            + '<button class="btn btn-dark btn-traza" data-id="' + fila.equipo_id + '" data-text="' + escapar(fila.nombre + ' · ' + fila.serial) + '" title="Trazabilidad"><i class="fas fa-history"></i></button>';
                        if (fila.ultima_asignacion_id) {
                            botones += '<a class="btn btn-outline-danger" target="_blank" href="../PDF/ActaEquipoCOF16.php?tipo=ENTREGA&id='
                                + encodeURIComponent(fila.ultima_asignacion_id) + '" title="Acta de entrega"><i class="fas fa-file-export"></i></a>';
                        }
                        if (fila.ultima_devolucion_id && fila.ultima_asignacion_id) {
                            botones += '<a class="btn btn-outline-danger" target="_blank" href="../PDF/ActaEquipoCOF16.php?tipo=DEVOLUCION&id='
                                + encodeURIComponent(fila.ultima_asignacion_id) + '" title="Acta de devolución"><i class="fas fa-file-import"></i></a>';
                        }
                        if (fila.asignacion_activa) {
                            botones += '<button class="btn btn-primary btn-devolver" data-id="' + fila.equipo_id + '" data-text="' + escapar(fila.nombre + ' · ' + fila.serial) + '" title="Devolver"><i class="fas fa-undo"></i></button>';
                        } else if (fila.estado_codigo === 'DISPONIBLE') {
                            botones += '<button class="btn btn-success btn-asignar" data-id="' + fila.equipo_id + '" data-text="' + escapar(fila.nombre + ' · ' + fila.serial) + '" title="Asignar"><i class="fas fa-user-plus"></i></button>';
                        }
                        return botones + '</div>';
                    }
                }
            ],
            language: {
                processing: 'Procesando...', search: 'Buscar:', lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_', infoEmpty: 'Sin registros',
                zeroRecords: 'No se encontraron resultados',
                paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
            }
        });
    }

    function mostrarDetalle(id) {
        $.getJSON(api + '?op=detalle&id=' + encodeURIComponent(id)).done(function (respuesta) {
            const e = respuesta.data;
            let html = '<div class="row">'
                + '<div class="col-md-3"><strong>Nombre</strong><p>' + escapar(e.nombre) + '</p></div>'
                + '<div class="col-md-3"><strong>Tipo</strong><p>' + escapar(e.tipo) + '</p></div>'
                + '<div class="col-md-3"><strong>Estado</strong><p>' + escapar(e.estado) + '</p></div>'
                + '<div class="col-md-3"><strong>Custodio</strong><p>' + escapar(e.custodio) + '</p></div>'
                + '<div class="col-md-3"><strong>Marca / modelo</strong><p>' + escapar(e.marca) + ' / ' + escapar(e.modelo) + '</p></div>'
                + '<div class="col-md-3"><strong>Serial</strong><p>' + escapar(e.serial) + '</p></div>'
                + '<div class="col-md-3"><strong>SIESA</strong><p>' + escapar(e.codigo_siesa) + '</p></div>'
                + '<div class="col-md-3"><strong>Activo fijo</strong><p>' + escapar(e.codigo_activo_fijo) + '</p></div>'
                + '<div class="col-md-12"><strong>Especificaciones</strong><p>Disco: ' + escapar(e.disco_duro) + ' · RAM: ' + escapar(e.ram) + ' · CPU: ' + escapar(e.procesador) + ' · SO: ' + escapar(e.sistema_operativo) + '</p></div>'
                + '</div><h5>Componentes</h5><div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Serial</th><th>Estado</th><th>Observación</th></tr></thead><tbody>';
            (e.componentes || []).forEach(function (c) {
                html += '<tr><td>' + escapar(c.tipo) + '</td><td>' + escapar(c.marca) + '</td><td>'
                    + escapar(c.modelo) + '</td><td>' + escapar(c.serial) + '</td><td>'
                    + escapar(c.estado) + '</td><td>' + escapar(c.observacion) + '</td></tr>';
            });
            html += '</tbody></table></div><h5>Actas</h5><div class="list-group">';
            (e.actas || []).forEach(function (acta) {
                html += '<a class="list-group-item list-group-item-action" target="_blank" href="../PDF/ActaEquipoCOF16.php?tipo='
                    + encodeURIComponent(acta.tipo) + '&id=' + encodeURIComponent(acta.asignacion_id)
                    + '"><i class="fas fa-file-pdf text-danger mr-2"></i>' + escapar(acta.numero)
                    + ' · ' + escapar(acta.fecha_generacion) + '</a>';
            });
            html += (e.actas || []).length ? '</div>' : '<span class="text-muted">No hay actas generadas.</span></div>';
            $('#detalleEquipoContenido').html(html);
            $('#modalDetalleEquipo').modal('show');
        }).fail(function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); });
    }

    function seleccionarTab(tab, selector, id, texto) {
        $('#inventarioTabs a[href="' + tab + '"]').tab('show');
        const opcion = new Option(texto, id, true, true);
        $(selector).empty().append(opcion).trigger('change');
    }

    function pintarResumenAsignacion() {
        const empleado = $('#asignacionEmpleado').data('empleado');
        const jefe = $('#asignacionJefeEmpleado').data('empleado');
        const equipo = $('#asignacionEquipo').data('equipo');
        if (!empleado && !jefe && !equipo) {
            $('#resumenAsignacion').addClass('d-none').empty();
            return;
        }
        let html = '';
        if (empleado) html += '<strong>Empleado:</strong> ' + escapar(empleado.nombre) + ' · '
            + escapar(empleado.cargo) + ' · ' + escapar(empleado.area) + '<br>';
        if (jefe) html += '<strong>Jefe inmediato:</strong> ' + escapar(jefe.nombre) + ' · '
            + escapar(jefe.cargo) + '<br>';
        if (equipo) html += '<strong>Equipo:</strong> ' + escapar(equipo.nombre) + ' · '
            + escapar(equipo.marca) + ' ' + escapar(equipo.modelo) + ' · Serial ' + escapar(equipo.serial);
        $('#resumenAsignacion').html(html).removeClass('d-none');
    }

    function cargarEquipoAsignacion(id) {
        if (!id) {
            $('#asignacionComponentes').html('<span class="text-muted">Seleccione un equipo.</span>');
            $('#asignacionEquipo').removeData('equipo');
            pintarResumenAsignacion();
            return;
        }
        $.getJSON(api + '?op=detalle&id=' + encodeURIComponent(id)).done(function (respuesta) {
            const equipo = respuesta.data;
            $('#asignacionEquipo').data('equipo', equipo);
            pintarResumenAsignacion();
            let html = '<div class="custom-control custom-checkbox mb-2">'
                + '<input type="checkbox" class="custom-control-input" id="componentePrincipal" checked disabled>'
                + '<label class="custom-control-label" for="componentePrincipal">Equipo principal · '
                + escapar(equipo.marca) + ' ' + escapar(equipo.modelo) + ' · ' + escapar(equipo.serial) + '</label></div>';
            (equipo.componentes || []).forEach(function (c) {
                html += '<div class="custom-control custom-checkbox mb-2">'
                    + '<input type="checkbox" class="custom-control-input componente-entrega" id="entregaComponente'
                    + c.componente_id + '" value="' + c.componente_id + '">'
                    + '<label class="custom-control-label" for="entregaComponente' + c.componente_id + '">'
                    + escapar(c.tipo) + ' · ' + escapar(c.marca) + ' ' + escapar(c.modelo)
                    + ' · Serial ' + escapar(c.serial) + '</label></div>';
            });
            $('#asignacionComponentes').html(html);
        }).fail(function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); });
    }

    function filaSoftware() {
        return '<div class="row software-fila mb-2">'
            + '<div class="col-md-3"><input class="form-control software-nombre" maxlength="150" placeholder="Nombre"></div>'
            + '<div class="col-md-2"><input class="form-control software-version" maxlength="80" placeholder="Versión"></div>'
            + '<div class="col-md-3"><input class="form-control software-licencia" maxlength="180" placeholder="Licencia"></div>'
            + '<div class="col-md-3"><input class="form-control software-observacion" maxlength="1000" placeholder="Observación"></div>'
            + '<div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-quitar-software"><i class="fas fa-times"></i></button></div>'
            + '</div>';
    }

    function softwareFormulario() {
        const items = [];
        $('.software-fila').each(function () {
            if ($.trim($(this).find('.software-nombre').val())) {
                items.push({
                    nombre: $(this).find('.software-nombre').val(),
                    version: $(this).find('.software-version').val(),
                    licencia: $(this).find('.software-licencia').val(),
                    observacion: $(this).find('.software-observacion').val()
                });
            }
        });
        return items;
    }

    function cargarAsignacionDevolucion() {
        const equipoId = $('#devolucionEquipo').val();
        if (!equipoId) {
            Swal.fire('Equipo requerido', 'Seleccione primero un equipo con asignación activa.', 'warning');
            return;
        }
        $.getJSON(api + '?op=cargarAsignacion&equipo_id=' + encodeURIComponent(equipoId)).done(function (respuesta) {
            const a = respuesta.data;
            $('#devolucionAsignacionId').val(a.asignacion_id);
            $('#resumenDevolucion').html(
                '<strong>Equipo:</strong> ' + escapar(a.equipo_nombre) + ' · ' + escapar(a.equipo_serial)
                + '<br><strong>Colaborador:</strong> ' + escapar(a.empleado_nombre) + ' · ' + escapar(a.empleado_documento)
                + '<br><strong>Entrega:</strong> ' + escapar(a.fecha_entrega)
                + '<br><strong>Diagnóstico:</strong> ' + escapar(a.diagnostico_entrega)
            );
            let filas = '';
            (a.componentes || []).forEach(function (c) {
                filas += '<tr class="elemento-devolucion" data-id="' + c.asignacion_componente_id + '">'
                    + '<td>' + escapar(c.tipo) + '<br><small>' + escapar(c.marca) + ' ' + escapar(c.modelo) + '</small></td>'
                    + '<td class="serial-original">' + escapar(c.serial_original) + '</td>'
                    + '<td><select class="form-control form-control-sm estado-recepcion">' + opciones(catalogos.estados_recepcion) + '</select></td>'
                    + '<td><select class="form-control form-control-sm estado-serial">' + opciones(catalogos.estados_serial) + '</select></td>'
                    + '<td><input class="form-control form-control-sm serial-recibido" maxlength="120"></td>'
                    + '<td><textarea class="form-control form-control-sm observacion-elemento" maxlength="2000"></textarea></td></tr>';
            });
            $('#tablaElementosDevolucion tbody').html(filas);
            pintarRevisionFisica();
            $('#formDevolucion').removeClass('d-none');
        }).fail(function (xhr) {
            $('#formDevolucion').addClass('d-none');
            Swal.fire('No fue posible cargar', mensajeError(xhr), 'error');
        });
    }

    function pintarRevisionFisica() {
        const campos = {
            estado_fisico: 'Estado físico', encendido: 'Encendido', funcionamiento: 'Funcionamiento',
            pantalla: 'Pantalla', bateria: 'Batería', teclado: 'Teclado', mouse: 'Mouse',
            cargador: 'Cargador', puertos: 'Puertos', conectividad: 'Conectividad', limpieza: 'Limpieza'
        };
        let html = '';
        Object.keys(campos).forEach(function (campo) {
            html += '<div class="form-group col-md-3"><label>' + campos[campo] + '</label>'
                + '<select class="form-control revision-estado" data-campo="' + campo + '" required>'
                + '<option value="">Seleccione</option><option value="BUENO">Bueno</option>'
                + '<option value="NOVEDAD">Con novedad</option><option value="NO_APLICA">No aplica</option>'
                + '</select></div>';
        });
        $('#revisionFisica').html(html);
    }

    function elementosDevolucion() {
        const items = [];
        $('.elemento-devolucion').each(function () {
            items.push({
                asignacion_componente_id: $(this).data('id'),
                estado_recepcion_id: $(this).find('.estado-recepcion').val(),
                estado_verificacion_serial_id: $(this).find('.estado-serial').val(),
                serial_recibido: $(this).find('.serial-recibido').val(),
                observacion: $(this).find('.observacion-elemento').val()
            });
        });
        return items;
    }

    function revisionDevolucion() {
        const revision = {};
        $('.revision-estado').each(function () { revision[$(this).data('campo')] = $(this).val(); });
        revision.danos_visibles = $('#revisionDanos').val();
        revision.elementos_faltantes = $('#revisionFaltantes').val();
        revision.observacion = $('#revisionObservacion').val();
        return revision;
    }

    function consultarTrazabilidad() {
        const equipoId = $('#trazabilidadEquipo').val();
        if (!equipoId) {
            Swal.fire('Equipo requerido', 'Seleccione un equipo antes de consultar.', 'warning');
            return;
        }
        $.getJSON(api + '?op=trazabilidad&equipo_id=' + encodeURIComponent(equipoId)).done(function (respuesta) {
            const items = respuesta.data || [];
            let html = '';
            items.forEach(function (m) {
                html += '<div><i class="fas fa-history bg-info"></i><div class="timeline-item">'
                    + '<span class="time"><i class="far fa-clock"></i> ' + escapar(m.fecha_creacion) + '</span>'
                    + '<h3 class="timeline-header"><strong>' + escapar(m.tipo_movimiento) + '</strong> · ' + escapar(m.usuario) + '</h3>'
                    + '<div class="timeline-body">' + escapar(m.descripcion)
                    + (m.responsable_anterior ? '<br><small>Anterior: ' + escapar(m.responsable_anterior) + '</small>' : '')
                    + (m.responsable_nuevo ? '<br><small>Nuevo: ' + escapar(m.responsable_nuevo) + '</small>' : '')
                    + '</div></div></div>';
            });
            $('#lineaTrazabilidad').html(html || '<div class="callout callout-info">El equipo no tiene movimientos registrados.</div>');
        }).fail(function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); });
    }

    function filaRepuesto() {
        return '<div class="row repuesto-fila mb-2">'
            + '<div class="col-md-3"><input class="form-control repuesto-descripcion" maxlength="180" placeholder="Descripción"></div>'
            + '<div class="col-md-2"><input class="form-control repuesto-referencia" maxlength="120" placeholder="Referencia"></div>'
            + '<div class="col-md-2"><input class="form-control repuesto-serial" maxlength="120" placeholder="Serial"></div>'
            + '<div class="col-md-1"><input type="number" min="0.01" step="0.01" value="1" class="form-control repuesto-cantidad"></div>'
            + '<div class="col-md-3"><input class="form-control repuesto-observacion" maxlength="2000" placeholder="Observación"></div>'
            + '<div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-quitar-repuesto"><i class="fas fa-times"></i></button></div>'
            + '</div>';
    }

    function repuestosFormulario() {
        const items = [];
        $('.repuesto-fila').each(function () {
            if ($.trim($(this).find('.repuesto-descripcion').val())) {
                items.push({
                    descripcion: $(this).find('.repuesto-descripcion').val(),
                    referencia: $(this).find('.repuesto-referencia').val(),
                    serial: $(this).find('.repuesto-serial').val(),
                    cantidad: $(this).find('.repuesto-cantidad').val(),
                    observacion: $(this).find('.repuesto-observacion').val()
                });
            }
        });
        return items;
    }

    function cargarHistorialMantenimiento(equipoId) {
        return $.getJSON(api + '?op=listarMantenimientos&equipo_id=' + encodeURIComponent(equipoId))
            .done(function (respuesta) {
                let html = '<table class="table table-sm table-bordered"><thead><tr><th>Fecha</th><th>Tipo</th><th>Responsable</th><th>Diagnóstico</th><th>Actividad</th><th>Estado</th><th>Próxima</th></tr></thead><tbody>';
                (respuesta.data || []).forEach(function (m) {
                    html += '<tr><td>' + escapar(m.fecha) + '</td><td>' + escapar(m.tipo) + '</td><td>'
                        + escapar(m.responsable) + '</td><td>' + escapar(m.diagnostico) + '</td><td>'
                        + escapar(m.actividad_realizada) + '</td><td>' + escapar(m.estado_resultante)
                        + '</td><td>' + escapar(m.proxima_fecha) + '</td></tr>';
                });
                $('#historialMantenimiento').html(html + '</tbody></table>');
            }).fail(function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); });
    }

    function abrirMantenimiento(equipoId) {
        $('#formMantenimiento')[0].reset();
        $('#mantenimientoEquipoId').val(equipoId);
        $('#listaRepuestos').empty();
        $('#historialMantenimiento').html('<span class="text-muted">Cargando historial...</span>');
        $('#modalMantenimiento').modal('show');
        cargarHistorialMantenimiento(equipoId);
    }

    function cargarPendientes() {
        if (tablaPendientes) {
            tablaPendientes.ajax.reload();
            return;
        }
        tablaPendientes = $('#tablaPendientes').DataTable({
            responsive: true,
            autoWidth: false,
            ajax: {
                url: api + '?op=pendientes',
                dataSrc: function (respuesta) { return respuesta.data || []; },
                error: function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); }
            },
            columns: [
                { data: 'nombre' }, { data: 'serial' }, { data: 'tipo_pendiente' },
                { data: 'estado' }, { data: 'ubicacion' }, { data: 'responsable' },
                { data: 'detalle', defaultContent: '' }
            ],
            language: {
                search: 'Buscar:', lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_', infoEmpty: 'Sin registros',
                zeroRecords: 'No se encontraron resultados',
                paginate: { next: 'Siguiente', previous: 'Anterior' }
            }
        });
    }

    $('#btnFiltrarInventario').on('click', cargarTablaInventario);
    $(document).on('click', '.btn-detalle', function () { mostrarDetalle($(this).data('id')); });
    $(document).on('click', '.btn-editar', function () {
        window.location.href = 'registrar_equipo.php?id=' + encodeURIComponent($(this).data('id'));
    });
    $(document).on('click', '.btn-componente', function () {
        $('#formComponente')[0].reset();
        $('#componenteEquipoId').val($(this).data('id'));
        $('#modalComponente').modal('show');
    });
    $(document).on('click', '.btn-mantenimiento', function () { abrirMantenimiento($(this).data('id')); });
    $(document).on('click', '.btn-asignar', function () {
        seleccionarTab('#tabAsignacion', '#asignacionEquipo', $(this).data('id'), $(this).data('text'));
        cargarEquipoAsignacion($(this).data('id'));
    });
    $(document).on('click', '.btn-devolver', function () {
        seleccionarTab('#tabDevolucion', '#devolucionEquipo', $(this).data('id'), $(this).data('text'));
    });
    $(document).on('click', '.btn-traza', function () {
        seleccionarTab('#tabTrazabilidad', '#trazabilidadEquipo', $(this).data('id'), $(this).data('text'));
    });
    $('#asignacionEquipo').on('change', function () { cargarEquipoAsignacion($(this).val()); });

    $('#formComponente').on('submit', function (evento) {
        evento.preventDefault();
        const datos = $(this).serializeArray();
        datos.push({ name: 'csrf_token', value: csrf });
        $.ajax({ url: api + '?op=agregarComponente', method: 'POST', data: $.param(datos), dataType: 'json' })
            .done(function (respuesta) {
                $('#modalComponente').modal('hide');
                Swal.fire('Registrado', respuesta.message, 'success');
            }).fail(function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); });
    });

    $('#btnAgregarRepuesto').on('click', function () { $('#listaRepuestos').append(filaRepuesto()); });
    $(document).on('click', '.btn-quitar-repuesto', function () { $(this).closest('.repuesto-fila').remove(); });
    $('#formMantenimiento').on('submit', function (evento) {
        evento.preventDefault();
        const datos = $(this).serializeArray();
        datos.push({ name: 'csrf_token', value: csrf });
        datos.push({ name: 'repuestos_json', value: JSON.stringify(repuestosFormulario()) });
        $.ajax({ url: api + '?op=crearMantenimiento', method: 'POST', data: $.param(datos), dataType: 'json' })
            .done(function (respuesta) {
                Swal.fire('Registrado', respuesta.message, 'success');
                cargarHistorialMantenimiento($('#mantenimientoEquipoId').val());
                cargarTablaInventario();
                $('#formMantenimiento')[0].reset();
            }).fail(function (xhr) { Swal.fire('Error', mensajeError(xhr), 'error'); });
    });

    $('#btnAgregarSoftware').on('click', function () { $('#listaSoftware').append(filaSoftware()); });
    $(document).on('click', '.btn-quitar-software', function () { $(this).closest('.software-fila').remove(); });

    $('#formAsignacion').on('submit', function (evento) {
        evento.preventDefault();
        if (!$('#asignacionDocumento').val() || !$('#asignacionJefeDocumento').val()
            || !$('#asignacionEquipo').val()) {
            Swal.fire('Datos incompletos', 'Seleccione un equipo, un empleado y un jefe inmediato activos.', 'warning');
            return;
        }
        if ($('.check-entrega:checked').length !== $('.check-entrega').length) {
            Swal.fire('Confirmaciones pendientes', 'Complete todas las verificaciones de entrega.', 'warning');
            return;
        }
        const componentes = $('.componente-entrega:checked').map(function () { return Number(this.value); }).get();
        const datos = $(this).serializeArray();
        datos.push({ name: 'csrf_token', value: csrf });
        datos.push({ name: 'componentes_ids', value: JSON.stringify(componentes) });
        datos.push({ name: 'software_json', value: JSON.stringify(softwareFormulario()) });
        Swal.fire({
            title: '¿Confirmar asignación?', text: 'Se registrará la entrega y se generará el acta CO-F-16.',
            icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, asignar', cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;
            const boton = $('#btnGuardarAsignacion').prop('disabled', true);
            $.ajax({ url: api + '?op=crearAsignacion', method: 'POST', data: $.param(datos), dataType: 'json' })
                .done(function (respuesta) {
                    generarYMostrarActa(respuesta.data.acta_url).then(function () {
                        return Swal.fire('Asignado', respuesta.message, 'success');
                    }).then(function () { window.location.reload(); }).catch(function (error) {
                        Swal.fire('Asignación registrada', error.message + ' Quedó visible en la bandeja de pendientes.', 'warning');
                    });
                }).fail(function (xhr) { Swal.fire('No fue posible asignar', mensajeError(xhr), 'error'); })
                .always(function () { boton.prop('disabled', false); });
        });
    });

    $('#btnCargarAsignacion').on('click', cargarAsignacionDevolucion);

    $('#formDevolucion').on('submit', function (evento) {
        evento.preventDefault();
        const elementos = elementosDevolucion();
        const revision = revisionDevolucion();
        if (elementos.some(function (e) { return !e.estado_recepcion_id || !e.estado_verificacion_serial_id; })) {
            Swal.fire('Verificación pendiente', 'Complete el estado y serial de cada elemento.', 'warning');
            return;
        }
        if (Object.keys(revision).some(function (k) {
            return k !== 'danos_visibles' && k !== 'elementos_faltantes' && k !== 'observacion' && !revision[k];
        })) {
            Swal.fire('Revisión pendiente', 'Complete toda la revisión física y funcional.', 'warning');
            return;
        }
        if (!$('#confirmacionGeneral').is(':checked')) {
            Swal.fire('Confirmación requerida', 'Confirme la recepción física de los elementos.', 'warning');
            return;
        }
        const datos = $(this).serializeArray();
        datos.push({ name: 'csrf_token', value: csrf });
        datos.push({ name: 'elementos_json', value: JSON.stringify(elementos) });
        datos.push({ name: 'revision_json', value: JSON.stringify(revision) });
        Swal.fire({
            title: '¿Cerrar devolución?', text: 'La asignación se cerrará y se actualizarán estado, ubicación y custodia.',
            icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, registrar', cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;
            const boton = $('#btnGuardarDevolucion').prop('disabled', true);
            $.ajax({ url: api + '?op=crearDevolucion', method: 'POST', data: $.param(datos), dataType: 'json' })
                .done(function (respuesta) {
                    generarYMostrarActa(respuesta.data.acta_url).then(function () {
                        return Swal.fire(
                            respuesta.data.completa ? 'Devolución completa' : 'Devolución con pendientes',
                            respuesta.message, respuesta.data.completa ? 'success' : 'warning'
                        );
                    }).then(function () { window.location.reload(); }).catch(function (error) {
                        Swal.fire('Devolución registrada', error.message + ' Quedó visible en la bandeja de pendientes.', 'warning');
                    });
                }).fail(function (xhr) { Swal.fire('No fue posible devolver', mensajeError(xhr), 'error'); })
                .always(function () { boton.prop('disabled', false); });
        });
    });

    $('#btnConsultarTrazabilidad').on('click', consultarTrazabilidad);
    $('a[href="#tabPendientes"]').on('shown.bs.tab', cargarPendientes);

    cargarInicial();
})(jQuery);

var rowCount = 0;
var umOpciones = ['UND', 'KG', 'LT', 'MT', 'GL', 'CJ', 'PZA'];

function init() {
    listarActividades();
}

function buildUmSelect(selected) {
    return '<select class="form-control input-sm">' +
        umOpciones.map(function (u) {
            return '<option' + (u === selected ? ' selected' : '') + '>' + u + '</option>';
        }).join('') +
        '</select>';
}

var getURLParameter = function (sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&');
    var sParameterName;
    for (var i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

function renumerarFilas() {
    $('#tbodyActividades tr').each(function(i, tr) {
        $(tr).find('td:first').text(i + 1);
    });
}

function agregarFila(data, editable) {
    rowCount++;
    editable = editable || false;
    var d = data || { id: '', actividad: '', detalle: '', repuesto: '', um: 'UND', cantidad: 1 };

    var tr = $('<tr>').attr('data-row-id', d.id || '');

    if (editable) {
        /* ── modo edición ── */
        tr.html(
            '<td class="text-center">' + rowCount + '</td>' +
            '<td><input type="text" class="form-control input-sm" value="' + (d.actividad || '') + '"></td>' +
            '<td><input type="text" class="form-control input-sm" value="' + (d.detalle || '') + '"></td>' +
            '<td><input type="text" class="form-control input-sm" value="' + (d.repuesto || '') + '"></td>' +
            '<td>' + buildUmSelect(d.um) + '</td>' +
            '<td><input type="number" class="form-control input-sm text-right" min="0" step="0.01" value="' + (d.cantidad || 1) + '"></td>' +
            '<td class="text-center">' +
            '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar"><i class="fa fa-times"></i></button>' +
            '</td>'
        );
    } else {
        /* ── modo lectura ── */
        tr.html(
            '<td class="text-center">' + rowCount + '</td>' +
            '<td>' + (d.actividad || '') + '</td>' +
            '<td>' + (d.detalle || '') + '</td>' +
            '<td>' + (d.repuesto || '') + '</td>' +
            '<td>' + (d.um || '') + '</td>' +
            '<td class="text-right">' + (d.cantidad || 0) + '</td>' +
            '<td class="text-center">' +
            '<button class="btn btn-sm btn-warning btn-editar" title="Editar"><i class="fa fa-pencil-alt"></i></button> ' +
            '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar"><i class="fa fa-times"></i></button>' +
            '</td>'
        );
    }

    $('#tbodyActividades').append(tr);
    renumerarFilas();
}



function recolectarDatos() {
    var filas = [];
    $('#tbodyActividades tr').each(function () {
        var inputs  = $(this).find('input, select');
        if (!inputs.length) return;   // fila en modo lectura — omitir

        var id = $(this).data('row-id') || null;

        filas.push({
            id:        id,             // <-- null = INSERT, número = UPDATE
            actividad: $(inputs[0]).val().trim(),
            detalle:   $(inputs[1]).val().trim(),
            repuesto:  $(inputs[2]).val().trim(),
            um:        $(inputs[3]).val(),
            cantidad:  parseFloat($(inputs[4]).val()) || 0
        });
    });
    return filas;
}

/* ── Agregar fila ── */
$(document).on('click', '#btnAgregarFila', function () {
    agregarFila(null, true);
});

/* ── Eliminar fila ── */
$(document).on('click', '.btn-eliminar', function () {
    var tr = $(this).closest('tr');
    var id = tr.data('row-id');

    Swal.fire({
        title: '¿Eliminar fila?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        if (id) {
            $.ajax({
                url: '../../controller/Tickets.php?op=eliminarLote',
                type: 'POST',
                dataType: 'json',
                data: {id: id },
                success: function (res) {
                    if (res.success) {
                        tr.remove();
                        renumerarFilas();
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error de conexión' });
                }
            });
        } else {
            tr.remove();
            renumerarFilas();
        }
    });
});

var id_orden_trabajo = getURLParameter('id');

/* ── Guardar todo ── */
$(document).on('click', '#btnGuardarTodo', function () {

    var filas = recolectarDatos();
    if (!filas.length) {
        Swal.fire({ icon: 'warning', title: 'Sin filas', text: 'Agrega al menos una fila.' });
        return;
    }

    $.ajax({
        url: '../../controller/Tickets.php?op=guardarLote',
        type: 'POST',
        dataType: 'json',
        data: {
            filas: JSON.stringify(filas),
            id_orden_trabajo: id_orden_trabajo
        },
        beforeSend: function () {
            $('#btnGuardarTodo').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        },
        success: function (res) {
            if (res.success) {
                if (res.ids) {
                    $('#tbodyActividades tr').each(function (i) {
                        $(this).attr('data-row-id', res.ids[i] || '');
                    });
                }
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'Registros guardados correctamente.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(function () {
                    listarActividades();  // <-- refresca la tabla
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.mensaje || 'Error al guardar.'
                });
            }
        },
        error: function () { alert('Error de conexión.'); },
        complete: function () {
            $('#btnGuardarTodo').prop('disabled', false).html('<i class="fa fa-save"></i> Guardar todo');
        }
    });
});

/* ── Función listar ── */
function listarActividades() {
    $.ajax({
        url: '../../controller/Tickets.php?op=listar_lote',
        type: 'POST',
        dataType: 'json',
        data: { id_orden_trabajo: id_orden_trabajo },
        success: function (res) {
            console.log(res);
            $('#tbodyActividades').empty();
            rowCount = 0;
            if (res.success && res.data.length > 0) {
                $.each(res.data, function (i, row) {
                    agregarFila({
                        id: row.id_detalle_actv_otm,
                        actividad: row.actividad_programada,
                        detalle: row.detalle_trabajo,
                        repuesto: row.repuesto,
                        um: row.um,
                        cantidad: row.cantidad
                    });
                }, false);
            }
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error al cargar los registros.' });
        }
    });
}

$(document).on('click', '.btn-editar', function () {
    var tr = $(this).closest('tr');
    var id = tr.data('row-id');
    var tds = tr.find('td');

    var actividad = $(tds[1]).text();
    var detalle = $(tds[2]).text();
    var repuesto = $(tds[3]).text();
    var um = $(tds[4]).text();
    var cantidad = $(tds[5]).text();

    $(tds[1]).html('<input type="text" class="form-control input-sm" value="' + actividad + '">');
    $(tds[2]).html('<input type="text" class="form-control input-sm" value="' + detalle + '">');
    $(tds[3]).html('<input type="text" class="form-control input-sm" value="' + repuesto + '">');
    $(tds[4]).html(buildUmSelect(um.trim()));
    $(tds[5]).html('<input type="number" class="form-control input-sm text-right" min="0" step="0.01" value="' + cantidad + '">');
    $(tds[6]).html(
        '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar"><i class="fas fa-trash-alt"></i></button>' +
        '<button class="btn btn-sm btn-dark btn-cancelar" title="Cancelar"><i class="fas fa-minus-circle"></i></button>'
    );

    tr.attr('data-row-id', id);
});

/* ── Cancelar edición — volver a modo lectura ── */
$(document).on('click', '.btn-cancelar', function () {
    var tr = $(this).closest('tr');
    var id = tr.data('row-id');

    /* recarga la fila desde BD */
    $.ajax({
        url: '../../controller/Tickets.php?op=listarUno',
        type: 'POST',
        dataType: 'json',
        data: { id: id },
        success: function (res) {
            if (res.success) {
                var row = res.data;
                tr.find('td').eq(1).html(row.actividad_programada);
                tr.find('td').eq(2).html(row.detalle_trabajo);
                tr.find('td').eq(3).html(row.repuesto);
                tr.find('td').eq(4).html(row.um);
                tr.find('td').eq(5).html(row.cantidad);
                tr.find('td').eq(6).html(
                    '<button class="btn btn-sm btn-warning btn-editar" title="Editar"><i class="fa fa-pencil-alt"></i></button> ' +
                    '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar"><i class="fa fa-times"></i></button>'
                );
            }
        }
    });
});

$(document).on('click', '#btnVolver', function () {
    window.history.back();
});

init();
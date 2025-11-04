let totalPaginas = 0;
let paginaActual = 1;
const tamPag = 100;

$('#filtro-fechas').daterangepicker({
    locale: {
        format: 'YYYY-MM-DD',  // Formato de las fechas
        applyLabel: 'Aplicar',
        cancelLabel: 'Cancelar',
        customRangeLabel: 'Rango personalizado',
        daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
    },
    startDate: '2017-01-01',  // Fecha de inicio predeterminada
    endDate: moment(),  // Fecha de fin predeterminada (hoy)
    // Rango de fechas predefinidos
    ranges: {
        'Hoy': [moment(), moment()],
        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
        'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
        'Este mes': [moment().startOf('month'), moment().endOf('month')],
        'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    // Configuración visual
    showCustomRangeLabel: true,  // Mostrar el texto 'Rango personalizado'
    alwaysShowCalendars: true,  // Mostrar ambos calendarios (inicio y fin)
    autoApply: true,  // Aplicar automáticamente al seleccionar las fechas
    buttonClasses: ['btn', 'btn-sm', 'btn-primary'],  // Personaliza el estilo de los botones
    opens: 'center',  // Centrar el popup del rango de fechas
    drops: 'down',  // Mostrar el calendario hacia abajo desde el campo de texto

    // Personalizar las etiquetas de los botones
    applyButtonClasses: 'btn btn-success',
    cancelButtonClasses: 'btn btn-danger'
});


$(document).ready(function() {
    inicializarEventos();
});

function inicializarEventos() {
    $('#btn-filtrar-fechas').click(function() {
        paginaActual = 1;
        cargarProveedoresAPI();
    });

    $('#pageSelect').change(function() {
        paginaActual = $(this).val();
        cargarProveedoresAPI();
    });
}

function cargarProveedoresAPI() {
    const fechas = $('#filtro-fechas').val().split(' - ');
    const fechainicio = fechas[0];
    const fechafin = fechas[1];

    // Validar fechas
    if (!fechainicio || !fechafin) {
        console.error("Fechas no válidas");
        return;
    }

    $('#loading').show();
    
    $.ajax({
        url: '../../controller/Proveedores.php?op=consultaProveedorSiesa', // URL corregida
        type: 'GET',
        dataType: 'json',
        data: {
            fechainicio: fechainicio,
            fechafin: fechafin,
            pagina: paginaActual,
            tamPag: tamPag
        },
        success: function(response) {
            console.log("Respuesta completa:", response);
            
            if (response && response.aaData) {
                totalPaginas = response.totalPaginas || 1;
                llenarSelectPaginas();
                mostrarDatosEnTabla(response.aaData);
            } else {
                console.error("Estructura de respuesta inválida:", response);
                $('#proveedores_data_siesa').html('<tr><td colspan="2">No se recibieron datos válidos</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud:", {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            $('#proveedores_data_siesa').html('<tr><td colspan="2">Error al cargar los datos. Ver consola para detalles.</td></tr>');
        },
        complete: function() {
            $('#loading').hide();
        }
    });
}

function llenarSelectPaginas() {
    const select = $('#pageSelect');
    select.empty();
    
    for (let i = 1; i <= totalPaginas; i++) {
        select.append($('<option>', {
            value: i,
            text: 'Página ' + i
        }));
    }
    select.val(paginaActual);
}

function mostrarDatosEnTabla(datos) {
    // Destruir tabla existente si hay una
    if ($.fn.DataTable.isDataTable('#proveedores_data_siesa')) {
        $('#proveedores_data_siesa').DataTable().destroy();
    }
    
    // Limpiar la tabla
    $('#proveedores_data_siesa').empty();
    
    // Verificar si hay datos
    if (datos.length === 0) {
        $('#proveedores_data_siesa').html('<tr><td colspan="2">No hay registros para mostrar</td></tr>');
        return;
    }
    
    // Crear la tabla con DataTables
    $('#proveedores_data_siesa').DataTable({
        data: datos,
        columns: [
            { title: 'NIT', data: 0 },
            { title: 'RAZÓN SOCIAL', data: 1 }
        ],
        order: [[1, 'asc']],
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "No hay datos disponibles",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        destroy: true
    });
}
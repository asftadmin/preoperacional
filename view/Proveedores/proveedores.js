let tabla;

function init() {

};

$(document).on('click', '#btn-filtrar', function () {
    cargarProveedorAPI();
});



function cargarProveedorAPI() {
    const proveedor = $('#filtro-nombre').val();  // Obtener el valor del filtro de nombre

    $('#proveedores_data_siesa').DataTable({
        destroy: true,  // Eliminar la tabla existente antes de crear una nueva
        processing: true,
        serverSide: false,  // Deshabilitar la paginación en el servidor (en caso de que lo necesites)
        ajax: {
            url: '../../controller/Proveedores.php?op=consultaProveedorSiesa',
            type: 'GET',
            data: {
                proveedor: proveedor  // Pasar el filtro al backend
            },
            dataSrc: function(json) {
                console.log('Respuesta de la API:', json);  // Verificar la respuesta en la consola
                return json.aaData || [];  // Retornar los datos, asegúrate de que 'aaData' esté presente en la respuesta
            }
        },
        order: [[1, 'asc']],  // Ordenar por la segunda columna
        iDisplayLength: 10,   // Número de registros por página
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });
}







init();
$(document).ready(function () {

    // Inicializa la bandeja de formatos.
    inicializarTablaTrazabilidad();

    // Abre el formulario para crear una nueva trazabilidad.
    $("#btn_nuevo_formato").on("click", function () {

        window.location.href = "trazabilidad.php";

    });

});


/**
 * Inicializa la tabla principal de trazabilidad.
 *
 * La consulta AJAX se habilitará cuando
 * construyamos el controller y el modelo.
 */
function inicializarTablaTrazabilidad() {

    $("#tabla_trazabilidad").DataTable({

        responsive: true,
        autoWidth: false,

        order: [
            [0, "desc"]
        ],

        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron registros",
            emptyTable: "No existen formatos registrados",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            }
        }

    });

}
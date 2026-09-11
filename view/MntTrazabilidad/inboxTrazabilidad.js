let tablaTrazabilidad;


/**
 * Inicialización de la bandeja.
 */
$(document).ready(function () {

    inicializarTablaTrazabilidad();


    // Nuevo formato.
    $("#btn_nuevo_formato").on("click", function () {

        window.location.href = "trazabilidad.php";
    });


    // Abrir formato.
    $("#tabla_trazabilidad").on(
        "click",
        ".btn-abrir-trazabilidad",
        function () {

            const id = $(this).data("id");

            window.location.href =
                "trazabilidad.php?id=" + id;
        }
    );

});


/**
 * Inicializa la tabla de trazabilidades.
 */
function inicializarTablaTrazabilidad() {

    tablaTrazabilidad =
        $("#tabla_trazabilidad").DataTable({

            processing: true,
            responsive: true,
            autoWidth: false,

            ajax: {
                url: "../../controller/TrazabilidadObra.php?op=listar",
                type: "POST",
                dataType: "json",

                dataSrc: function (response) {

                    if (!response.success) {

                        Swal.fire({
                            icon: "warning",
                            title: "No fue posible cargar",
                            text: response.message
                                || "No se pudieron consultar los formatos."
                        });

                        return [];
                    }

                    return response.data || [];
                },

                error: function (xhr) {

                    let mensaje =
                        "Se presentó un error al consultar los formatos.";

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        mensaje =
                            xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: mensaje
                    });
                }
            },


            columns: [

                {
                    data: "fecha_trazabilidad",
                    render: function (data) {

                        return formatearFecha(
                            data
                        );
                    }
                },

                {
                    data: "tipo_mezcla_trazabilidad"
                },

                {
                    data: "tipo_actividad_trazabilidad",
                    render: function (data) {

                        return formatearActividad(
                            data
                        );
                    }
                },

                {
                    data: null,
                    render: function (data) {

                        let obra = "";

                        if (data.obras_codigo) {
                            obra +=
                                data.obras_codigo;
                        }

                        if (data.obras_nom) {

                            if (obra !== "") {
                                obra += " - ";
                            }

                            obra +=
                                data.obras_nom;
                        }

                        return obra;
                    }
                },

                {
                    data: "estado_trazabilidad",
                    className: "text-center",
                    render: function (data) {

                        return formatearEstado(
                            data
                        );
                    }
                },

                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center",

                    render: function (data) {

                        return generarBotonAccion(
                            data
                        );
                    }
                }
            ],


            order: [
                [0, "desc"]
            ],


            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu:
                    "Mostrar _MENU_ registros",
                info:
                    "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty:
                    "Mostrando 0 a 0 de 0 registros",
                infoFiltered:
                    "(filtrado de _MAX_ registros)",
                loadingRecords:
                    "Cargando...",
                zeroRecords:
                    "No se encontraron registros",
                emptyTable:
                    "No existen formatos registrados",

                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            }
        });
}


/**
 * Genera el botón según el estado.
 */
function generarBotonAccion(data) {

    const id =
        data.id_trazabilidad;

    const estado =
        parseInt(
            data.estado_trazabilidad,
            10
        );


    // Borrador.
    if (estado === 1) {

        return `
            <button
                type="button"
                class="btn btn-primary btn-sm btn-abrir-trazabilidad"
                data-id="${id}"
                title="Continuar diligenciando"
            >
                <i class="fas fa-edit mr-1"></i>
                Continuar
            </button>
        `;
    }


    // Los demás estados serán solo consulta.
    return `
        <button
            type="button"
            class="btn btn-info btn-sm btn-abrir-trazabilidad"
            data-id="${id}"
            title="Consultar formato"
        >
            <i class="fas fa-eye mr-1"></i>
            Ver
        </button>
    `;
}


/**
 * Muestra el estado de forma amigable.
 */
function formatearEstado(estado) {

    estado =
        parseInt(
            estado,
            10
        );


    switch (estado) {

        case 1:

            return `
                <span class="badge badge-secondary">
                    Borrador
                </span>
            `;


        case 2:

            return `
                <span class="badge badge-warning">
                    Pendiente aprobación
                </span>
            `;


        case 3:

            return `
                <span class="badge badge-success">
                    Aprobado
                </span>
            `;


        case 4:

            return `
                <span class="badge badge-danger">
                    Rechazado
                </span>
            `;


        default:

            return `
                <span class="badge badge-light">
                    Sin estado
                </span>
            `;
    }
}


/**
 * Cambia el código de actividad por un texto legible.
 */
function formatearActividad(actividad) {

    switch (actividad) {

        case "CONTINUA":
            return "Aplicación continua";

        case "BACHEO":
            return "Bacheo";

        case "PARCHEO":
            return "Parcheo";

        default:
            return actividad || "";
    }
}


/**
 * Convierte YYYY-MM-DD a DD-MM-YYYY.
 */
function formatearFecha(fecha) {

    if (!fecha) {
        return "";
    }

    const partes =
        fecha.split("-");

    if (partes.length !== 3) {
        return fecha;
    }

    return partes[2]
        + "-"
        + partes[1]
        + "-"
        + partes[0];
}
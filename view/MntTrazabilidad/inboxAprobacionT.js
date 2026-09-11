let tablaAprobacionTrazabilidad;


/**
 * Inicialización de la bandeja
 * de aprobación.
 */
$(document).ready(function () {

    inicializarTablaAprobacion();


    /*
     * Revisar formato.
     */
    $("#tabla_aprobacion_trazabilidad").on(
        "click",
        ".btn-revisar-trazabilidad",
        function () {

            const id =
                $(this).data("id");

            window.location.href =
                "aprobarTrazabilidad.php?id="
                + id;
        }
    );

});


/* ============================================================
 * DATATABLE
 * ============================================================ */

/**
 * Inicializa la tabla de formatos
 * pendientes de aprobación.
 */
function inicializarTablaAprobacion() {

    tablaAprobacionTrazabilidad =
        $("#tabla_aprobacion_trazabilidad")
            .DataTable({

                processing: true,
                responsive: true,
                autoWidth: false,

                ajax: {

                    url:
                        "../../controller/TrazabilidadObra.php"
                        + "?op=listar_bandeja_aprobacion",

                    type: "POST",

                    dataType: "json",


                    dataSrc: function (response) {

                        if (!response.success) {

                            Swal.fire({
                                icon: "warning",
                                title: "No fue posible cargar",
                                text:
                                    response.message
                                    || "No se pudieron consultar los formatos pendientes."
                            });

                            return [];
                        }


                        return response.data || [];
                    },


                    error: function (xhr) {

                        let mensaje =
                            "Se presentó un error al consultar los formatos pendientes.";


                        if (
                            xhr.responseJSON
                            && xhr.responseJSON.message
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

                    /*
                     * Fecha del formato.
                     */
                    {
                        data: "fecha_trazabilidad",

                        render: function (data) {

                            return formatearFecha(
                                data
                            );
                        }
                    },


                    /*
                     * Usuario que elaboró.
                     */
                    {
                        data: null,

                        render: function (data) {

                            return obtenerNombreUsuario(
                                data
                            );
                        }
                    },


                    /*
                     * Tipo de mezcla.
                     */
                    {
                        data:
                            "tipo_mezcla_trazabilidad"
                    },


                    /*
                     * Actividad.
                     */
                    {
                        data:
                            "tipo_actividad_trazabilidad",

                        render: function (data) {

                            return formatearActividad(
                                data
                            );
                        }
                    },


                    /*
                     * Obra.
                     */
                    {
                        data: null,

                        render: function (data) {

                            return obtenerNombreObra(
                                data
                            );
                        }
                    },


                    /*
                     * Fecha de envío.
                     */
                    {
                        data:
                            "fecha_envio_trazabilidad",

                        render: function (data) {

                            return formatearFechaHora(
                                data
                            );
                        }
                    },

                    {
                        data:
                            "estado_trazabilidad",

                        className:
                            "text-center",

                        render: function (data) {

                            return formatearEstadoAprobacion(
                                data
                            );
                        }
                    },


                    /*
                     * Acción.
                     */
                    {
                        data: null,

                        orderable: false,

                        searchable: false,

                        className:
                            "text-center",

                        render: function (data) {

                            return generarAccionesAprobacion(
                                data
                            );
                        }
                    }
                ],


                order: [
                    [5, "asc"]
                ],


                language: {

                    processing:
                        "Procesando...",

                    search:
                        "Buscar:",

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
                        "No existen formatos pendientes de aprobación",

                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    }
                }
            });
}


/* ============================================================
 * FORMATO DE DATOS
 * ============================================================ */

/**
 * Retorna el nombre completo
 * del usuario elaborador.
 */
function obtenerNombreUsuario(data) {

    const nombre =
        data.user_nombre || "";

    const apellidos =
        data.user_apellidos || "";


    return (
        nombre
        + " "
        + apellidos
    ).trim();
}


/**
 * Construye el nombre de la obra.
 */
function obtenerNombreObra(data) {

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


/**
 * Cambia el código de actividad
 * por una descripción legible.
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
 * Convierte YYYY-MM-DD
 * a DD-MM-YYYY.
 */
function formatearFecha(fecha) {

    if (!fecha) {
        return "";
    }


    const fechaLimpia =
        fecha.substring(0, 10);

    const partes =
        fechaLimpia.split("-");


    if (partes.length !== 3) {
        return fecha;
    }


    return partes[2]
        + "-"
        + partes[1]
        + "-"
        + partes[0];
}


/**
 * Formatea fecha y hora
 * del envío.
 */
function formatearFechaHora(fecha) {

    if (!fecha) {
        return "";
    }


    const objetoFecha =
        new Date(fecha);


    if (
        isNaN(
            objetoFecha.getTime()
        )
    ) {
        return fecha;
    }


    return objetoFecha
        .toLocaleString(
            "es-CO",
            {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            }
        );
}

/**
 * Muestra el estado mediante un badge.
 */
function formatearEstadoAprobacion(
    estado
) {

    estado =
        parseInt(
            estado,
            10
        );


    if (estado === 2) {

        return `
            <span class="badge badge-warning">
                Pendiente aprobación
            </span>
        `;
    }


    if (estado === 3) {

        return `
            <span class="badge badge-success">
                Aprobado
            </span>
        `;
    }


    if (estado === 4) {

        return `
            <span class="badge badge-danger">
                Rechazado
            </span>
        `;
    }


    return `
        <span class="badge badge-secondary">
            Sin estado
        </span>
    `;
}

/**
 * Genera las acciones disponibles
 * según el estado del formato.
 */
function generarAccionesAprobacion(data) {

    const id =
        data.id_trazabilidad;

    const estado =
        parseInt(
            data.estado_trazabilidad,
            10
        );


    /*
     * Pendiente de aprobación.
     */
    if (estado === 2) {

        return `
            <button
                type="button"
                class="btn btn-primary btn-sm btn-revisar-trazabilidad"
                data-id="${id}"
                title="Revisar formato"
            >
                <i class="fas fa-search mr-1"></i>
                Revisar
            </button>
        `;
    }


    /*
     * Aprobado.
     */
    if (estado === 3) {

        return `
            <button
                type="button"
                class="btn btn-info btn-sm btn-ver-trazabilidad"
                data-id="${id}"
                title="Ver formato"
            >
                <i class="fas fa-eye mr-1"></i>
                Ver
            </button>

            <button
                type="button"
                class="btn btn-danger btn-sm btn-pdf-trazabilidad"
                data-id="${id}"
                title="Generar PDF"
            >
                <i class="fas fa-file-pdf mr-1"></i>
                PDF
            </button>
        `;
    }


    /*
     * Rechazado.
     */
    if (estado === 4) {

        return `
            <button
                type="button"
                class="btn btn-info btn-sm btn-ver-trazabilidad"
                data-id="${id}"
                title="Ver formato"
            >
                <i class="fas fa-eye mr-1"></i>
                Ver
            </button>
        `;
    }


    return "";
}
let plantillaDetalleAprobacion = null;
let plantillaLlegadaAprobacion = null;


/**
 * Inicialización de la revisión.
 */
$(document).ready(function () {

    plantillaDetalleAprobacion =
        $("#tbody_detalle_aplicacion .fila-detalle:first")
            .clone(false, false);

    plantillaLlegadaAprobacion =
        $("#tbody_llegada_mezcla .fila-llegada:first")
            .clone(false, false);


    const trazabilidadId =
        obtenerParametroUrl("id");


    if (!trazabilidadId) {

        Swal.fire({
            icon: "warning",
            title: "Formato no válido",
            text: "No se encontró el formato que desea revisar."
        }).then(function () {

            window.location.href =
                "inboxAprobacionT.php";
        });

        return;
    }


    cargarAprobacion(
        trazabilidadId
    );


    /*
     * Volver.
     */
    $("#btn_volver").on(
        "click",
        function () {

            window.location.href =
                "inboxAprobacionT.php";
        }
    );


    /*
     * Aprobar.
     */
    $("#btn_aprobar").on(
        "click",
        function () {

            aprobarTrazabilidad(
                trazabilidadId
            );
        }
    );


    /*
     * Rechazar.
     */
    $("#btn_rechazar").on(
        "click",
        function () {

            rechazarTrazabilidad(
                trazabilidadId
            );
        }
    );

});

/**
 * Carga el formato pendiente de aprobación.
 */
function cargarAprobacion(
    trazabilidadId
) {

    $.ajax({
        url:
            "../../controller/TrazabilidadObra.php"
            + "?op=cargar_aprobacion",

        type: "POST",

        dataType: "json",

        data: {
            trazabilidad_id:
                trazabilidadId
        },


        success: function (response) {

            if (!response.success) {

                Swal.fire({
                    icon: "warning",
                    title: "Formato no disponible",
                    text:
                        response.message
                        || "El formato ya no se encuentra pendiente de aprobación."
                }).then(function () {

                    window.location.href =
                        "inboxAprobacionTrazabilidad.php";
                });

                return;
            }


            const data =
                response.data;


            cargarEncabezadoAprobacion(
                data.encabezado
            );


            cargarDetalleAprobacion(
                data.detalle || []
            );


            cargarLlegadasAprobacion(
                data.llegada || []
            );


            /*
             * Las columnas se configuran DESPUÉS
             * de crear las filas del detalle.
             */
            configurarColumnasAprobacion(
                data.encabezado.tipo_actividad_trazabilidad
            );


            bloquearFormularioAprobacion();
        },


        error: function (xhr) {

            mostrarErrorAprobacion(
                xhr,
                "No fue posible cargar el formato."
            );
        }
    });
}

/**
 * Carga la información general.
 */

/**
 * Carga la información general.
 */
function cargarEncabezadoAprobacion(
    data
) {

    $("#tipo_mezcla")
        .val(
            data.tipo_mezcla_trazabilidad
        );


    $("#fecha_trazabilidad")
        .val(
            formatearFechaFormulario(
                data.fecha_trazabilidad
            )
        );


    $(
        "input[name='tipo_actividad']"
        + "[value='"
        + data.tipo_actividad_trazabilidad
        + "']"
    ).prop(
        "checked",
        true
    );


    $("#observaciones").val(
        data.observaciones_trazabilidad
        || ""
    );


    /*
     * Elaboró.
     */
    const elaboradoPor =
        (
            (data.user_nombre || "")
            + " "
            + (data.user_apellidos || "")
        ).trim();


    $("#elaborado_por")
        .val(
            elaboradoPor
        );


    /*
     * Obra.
     */
    let textoObra =
        data.obras_nom || "";


    if (data.obras_codigo) {

        textoObra =
            data.obras_codigo
            + " - "
            + textoObra;
    }


    const option =
        new Option(
            textoObra,
            data.obra_trazabilidad,
            true,
            true
        );


    $("#obra_id")
        .empty()
        .append(option);



}

/**
 * Carga el detalle de aplicación.
 */
function cargarDetalleAprobacion(
    datos
) {

    const tbody =
        $("#tbody_detalle_aplicacion");


    tbody.empty();


    datos.forEach(
        function (item) {

            const fila =
                plantillaDetalleAprobacion
                    .clone(false, false);


            limpiarFilaAprobacion(
                fila
            );


            tbody.append(
                fila
            );


            cargarVehiculoAprobacion(
                fila.find(
                    ".select2-volqueta"
                ),
                item.vehiculo_traz_det,
                item.vehi_placa
            );


            const campos = {

                pr_inicial:
                    item.pr_inicial_traz_det,

                pr_final:
                    item.pr_final_traz_det,

                numero_caja:
                    item.numero_caja_traz_det,

                longitud:
                    item.longitud_traz_det,

                ancho:
                    item.ancho_traz_det,

                espesor_demolido:
                    item.espesor_demolido_traz_det,

                espesor_excavacion:
                    item.espesor_excavacion_traz_det,

                espesor_base:
                    item.espesor_base_traz_det,

                espesor_mezcla:
                    item.espesor_mezcla_traz_det,

                volumen_demolido:
                    item.volumen_demolido_traz_det,

                volumen_excavado:
                    item.volumen_excavado_traz_det,

                volumen_base:
                    item.volumen_base_traz_det,

                capas_imprimacion:
                    item.capas_imprimacion_traz_det,

                imprimacion:
                    item.imprimacion_traz_det,

                temperatura_aplicacion:
                    item.temperatura_aplicacion_traz_det,

                volumen_mezcla:
                    item.volumen_mezcla_traz_det
            };


            $.each(
                campos,
                function (
                    campo,
                    valor
                ) {

                    fila.find(
                        "[data-campo='"
                        + campo
                        + "']"
                    ).val(
                        valor !== null
                            ? valor
                            : ""
                    );
                }
            );
        }
    );
}

/**
 * Carga los registros de llegada.
 */
function cargarLlegadasAprobacion(
    datos
) {

    const tbody =
        $("#tbody_llegada_mezcla");


    tbody.empty();


    datos.forEach(
        function (item) {

            const fila =
                plantillaLlegadaAprobacion
                    .clone(false, false);


            limpiarFilaAprobacion(
                fila
            );


            tbody.append(
                fila
            );


            cargarVehiculoAprobacion(
                fila.find(
                    ".select2-volqueta-llegada"
                ),
                item.vehiculo_traz_lleg,
                item.vehi_placa
            );


            fila.find(
                "[data-campo='temperatura_llegada']"
            ).val(
                item.temperatura_llegada_traz_lleg
                ?? ""
            );


            fila.find(
                "[data-campo='volumen_llegada']"
            ).val(
                item.volumen_llegada_traz_lleg
                ?? ""
            );


            fila.find(
                "[data-campo='volumen_aplicado']"
            ).val(
                item.volumen_aplicado_traz_lleg
                ?? ""
            );


            fila.find(
                "[data-campo='factor_compactacion']"
            ).val(
                item.factor_compactacion_traz_lleg
                ?? ""
            );
        }
    );
}

/**
 * Carga la volqueta registrada.
 */
function cargarVehiculoAprobacion(
    select,
    vehiId,
    placa
) {

    if (!vehiId) {
        return;
    }


    select.empty();


    const option =
        new Option(
            placa || vehiId,
            vehiId,
            true,
            true
        );


    select.append(
        option
    );
}

/**
 * Deja el formulario únicamente
 * para consulta.
 */
function bloquearFormularioAprobacion() {

    $("#form_trazabilidad")
        .find("input, select, textarea")
        .prop("disabled", true);


    /*
     * Ocultar opciones de edición.
     */
    $("#btn_agregar_detalle").hide();

    $("#btn_agregar_llegada").hide();

    $(".btn-eliminar-detalle").hide();

    $(".btn-eliminar-llegada").hide();


    /*
     * Ocultar columna Acción.
     */
    $("#tabla_detalle_aplicacion")
        .find("th:last-child, td:last-child")
        .hide();

    $("#tabla_llegada_mezcla")
        .find("th:last-child, td:last-child")
        .hide();


    /*
     * Select2 en modo consulta.
     */
    $("#obra_id").select2({
        theme: "bootstrap4",
        width: "100%"
    });

    $(".select2-volqueta").select2({
        theme: "bootstrap4",
        width: "100%"
    });

    $(".select2-volqueta-llegada").select2({
        theme: "bootstrap4",
        width: "100%"
    });


    $("#obra_id")
        .prop("disabled", true)
        .trigger("change");

    $(".select2-volqueta")
        .prop("disabled", true)
        .trigger("change");

    $(".select2-volqueta-llegada")
        .prop("disabled", true)
        .trigger("change");


    /*
     * Botones del residente.
     */
    $("#btn_volver")
        .prop("disabled", false);

    $("#btn_aprobar")
        .prop("disabled", false);

    $("#btn_rechazar")
        .prop("disabled", false);
}

/**
 * Solicita confirmación para aprobar.
 */
function aprobarTrazabilidad(
    trazabilidadId
) {

    Swal.fire({

        title:
            "¿Aprobar trazabilidad?",

        text:
            "Confirme que la información registrada fue revisada.",

        icon:
            "question",

        input:
            "textarea",

        inputLabel:
            "Observaciones",

        inputPlaceholder:
            "Observaciones de la aprobación (opcional)",

        showCancelButton:
            true,

        confirmButtonText:
            "Sí, aprobar",

        cancelButtonText:
            "Cancelar",

        confirmButtonColor:
            "#28a745"

    }).then(
        function (result) {

            if (!result.isConfirmed) {
                return;
            }


            procesarAprobacion(
                trazabilidadId,
                result.value || ""
            );
        }
    );
}

/**
 * Envía la aprobación al servidor.
 */
function procesarAprobacion(
    trazabilidadId,
    observaciones
) {

    bloquearBotonesDecision();


    $.ajax({

        url:
            "../../controller/TrazabilidadObra.php"
            + "?op=aprobar_trazabilidad",

        type:
            "POST",

        dataType:
            "json",

        data: {

            trazabilidad_id:
                trazabilidadId,

            observaciones:
                observaciones
        },


        success: function (response) {

            if (!response.success) {

                Swal.fire({
                    icon: "warning",
                    title: "No fue posible aprobar",
                    text: response.message
                });

                habilitarBotonesDecision();

                return;
            }


            Swal.fire({
                icon: "success",
                title: "Trazabilidad aprobada",
                text:
                    response.message
                    || "El formato fue aprobado correctamente.",
                confirmButtonText: "Aceptar",
                allowOutsideClick: false
            }).then(function () {

                window.location.href =
                    "inboxAprobacionT.php";
            });
        },


        error: function (xhr) {

            mostrarErrorAprobacion(
                xhr,
                "Se presentó un error al aprobar el formato."
            );

            habilitarBotonesDecision();
        }
    });
}

/**
 * Solicita el motivo del rechazo.
 */
function rechazarTrazabilidad(
    trazabilidadId
) {

    Swal.fire({

        title:
            "Rechazar trazabilidad",

        text:
            "Indique el motivo por el cual el formato no puede ser aprobado.",

        icon:
            "warning",

        input:
            "textarea",

        inputLabel:
            "Motivo del rechazo",

        inputPlaceholder:
            "Escriba el motivo del rechazo...",

        inputAttributes: {
            maxlength: 1000
        },

        showCancelButton:
            true,

        confirmButtonText:
            "Rechazar",

        cancelButtonText:
            "Cancelar",

        confirmButtonColor:
            "#dc3545",


        inputValidator: function (
            value
        ) {

            if (
                !value
                || !value.trim()
            ) {

                return "Debe indicar el motivo del rechazo.";
            }
        }

    }).then(
        function (result) {

            if (!result.isConfirmed) {
                return;
            }


            procesarRechazo(
                trazabilidadId,
                result.value.trim()
            );
        }
    );
}

/**
 * Envía el rechazo al servidor.
 */
function procesarRechazo(
    trazabilidadId,
    observaciones
) {

    bloquearBotonesDecision();


    $.ajax({

        url:
            "../../controller/TrazabilidadObra.php"
            + "?op=rechazar_trazabilidad",

        type:
            "POST",

        dataType:
            "json",

        data: {

            trazabilidad_id:
                trazabilidadId,

            observaciones:
                observaciones
        },


        success: function (response) {

            if (!response.success) {

                Swal.fire({
                    icon: "warning",
                    title: "No fue posible rechazar",
                    text: response.message
                });

                habilitarBotonesDecision();

                return;
            }


            Swal.fire({
                icon: "success",
                title: "Trazabilidad rechazada",
                text:
                    response.message
                    || "El formato fue rechazado.",
                confirmButtonText: "Aceptar",
                allowOutsideClick: false
            }).then(function () {

                window.location.href =
                    "inboxAprobacionT.php";
            });
        },


        error: function (xhr) {

            mostrarErrorAprobacion(
                xhr,
                "Se presentó un error al rechazar el formato."
            );

            habilitarBotonesDecision();
        }
    });
}

function bloquearBotonesDecision() {

    $("#btn_aprobar")
        .prop("disabled", true);

    $("#btn_rechazar")
        .prop("disabled", true);
}


function habilitarBotonesDecision() {

    $("#btn_aprobar")
        .prop("disabled", false);

    $("#btn_rechazar")
        .prop("disabled", false);
}


/**
 * Elimina rastros de Select2
 * de las filas clonadas.
 */
function limpiarFilaAprobacion(
    fila
) {

    fila.find(
        ".select2-container"
    ).remove();


    fila.find("select")
        .removeClass(
            "select2-hidden-accessible"
        )
        .removeAttr(
            "data-select2-id"
        )
        .removeAttr(
            "aria-hidden"
        )
        .removeAttr(
            "tabindex"
        );


    fila.find("input")
        .val("");
}


/**
 * Obtiene parámetro de URL.
 */
function obtenerParametroUrl(
    parametro
) {

    const params =
        new URLSearchParams(
            window.location.search
        );


    return params.get(
        parametro
    );
}


/**
 * Convierte YYYY-MM-DD
 * en DD-MM-YYYY.
 */
function formatearFechaFormulario(
    fecha
) {

    if (!fecha) {
        return "";
    }


    const partes =
        fecha.substring(
            0,
            10
        ).split("-");


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
 * Muestra errores retornados
 * por el controller.
 */
function mostrarErrorAprobacion(
    xhr,
    mensajePredeterminado
) {

    let mensaje =
        mensajePredeterminado;


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
        text: mensaje,
        confirmButtonText: "Aceptar"
    });
}

/**
 * Muestra las columnas correspondientes
 * al tipo de actividad.
 */
function configurarColumnasAprobacion(
    tipoActividad
) {

    let casillasVisibles = [];


    if (tipoActividad === "CONTINUA") {

        casillasVisibles = [
            1,
            2,
            3,
            5,
            6,
            10,
            14,
            15,
            16
        ];
    }


    if (tipoActividad === "BACHEO") {

        casillasVisibles = [
            1,
            2,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            15,
            16
        ];
    }


    if (tipoActividad === "PARCHEO") {

        casillasVisibles = [
            1,
            2,
            4,
            5,
            6,
            10,
            14,
            15,
            16
        ];
    }


    $("#tabla_detalle_aplicacion [data-casilla]")
        .hide();


    casillasVisibles.forEach(
        function (casilla) {

            $(
                "#tabla_detalle_aplicacion "
                + "[data-casilla='"
                + casilla
                + "']"
            ).show();
        }
    );
}
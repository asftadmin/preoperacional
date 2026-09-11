let plantillaDetalle = null;
let plantillaLlegada = null;
let estadoTrazabilidad = 1;

/**
 * Inicialización del módulo.
 */
$(document).ready(function () {

    // Guardar plantillas limpias antes de inicializar Select2.
    plantillaDetalle = $("#tbody_detalle_aplicacion .fila-detalle:first")
        .clone(false, false);

    plantillaLlegada = $("#tbody_llegada_mezcla .fila-llegada:first")
        .clone(false, false);


    // Inicializaciones generales.
    inicializarSelect2();
    inicializarFecha();
    inicializarObras();
    inicializarVolquetas();
    inicializarVolquetasLlegada();

    configurarColumnasDetalle("");

    /*
     * Si viene un ID por URL significa que se está
     * continuando un borrador existente.
     */
    const trazabilidadId = obtenerParametroUrl("id");

    if (trazabilidadId) {

        $("#trazabilidad_id").val(trazabilidadId);

        cargarBorrador(trazabilidadId);

    } else {

        configurarModoFormulario();
    }


    /**
     * Cambio de tipo de actividad.
     */
    $("input[name='tipo_actividad']").on(
        "change",
        function () {

            configurarColumnasDetalle(
                $(this).val()
            );
        }
    );


    /**
     * Agregar fila al detalle.
     */
    $("#btn_agregar_detalle").on(
        "click",
        function () {
            agregarFilaDetalle();
        }
    );


    /**
     * Eliminar fila del detalle.
     */
    $("#tbody_detalle_aplicacion").on(
        "click",
        ".btn-eliminar-detalle",
        function () {
            eliminarFilaDetalle($(this));
        }
    );


    /**
     * Recalcular valores del detalle.
     */
    $("#tbody_detalle_aplicacion").on(
        "input",
        ".calcular-detalle",
        function () {

            calcularFilaDetalle(
                $(this).closest("tr")
            );
        }
    );


    /**
     * Agregar fila de llegada.
     */
    $("#btn_agregar_llegada").on(
        "click",
        function () {
            agregarFilaLlegada();
        }
    );


    /**
     * Eliminar fila de llegada.
     */
    $("#tbody_llegada_mezcla").on(
        "click",
        ".btn-eliminar-llegada",
        function () {
            eliminarFilaLlegada($(this));
        }
    );


    /**
     * Calcular factor de compactación.
     */
    $("#tbody_llegada_mezcla").on(
        "input",
        ".calcular-fc",
        function () {

            calcularFactorCompactacion(
                $(this).closest("tr")
            );
        }
    );


    /**
     * Cancelar / volver a bandeja.
     */
    $("#btn_cancelar").on(
        "click",
        function () {
            cancelarFormulario();
        }
    );


    /**
     * Guardar borrador.
     */
    $("#btn_guardar_borrador").on(
        "click",
        function () {

            if (!validarBorrador()) {
                return;
            }

            guardarBorrador();
        }
    );

    /**
 * Enviar formato a aprobación.
 */
    $("#btn_enviar_aprobacion").on(
        "click",
        function () {

            if (!validarEnvioAprobacion()) {
                return;
            }

            Swal.fire({
                title: "¿Enviar formato?",
                text: "El formato será enviado para aprobación y ya no podrá continuar editándolo.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sí, enviar",
                cancelButtonText: "Cancelar"
            }).then(function (result) {

                if (result.isConfirmed) {
                    enviarAprobacion();
                }
            });
        }
    );
});


/* ============================================================
 * SELECT2 GENERAL
 * ============================================================ */

/**
 * Inicializa Select2 que no utilizan AJAX.
 */
function inicializarSelect2() {

    $(".select2")
        .not(".select2-obra")
        .not(".select2-volqueta")
        .not(".select2-volqueta-llegada")
        .select2({
            theme: "bootstrap4",
            width: "100%"
        });
}


/* ============================================================
 * OBRAS
 * ============================================================ */

/**
 * Inicializa el Select2 de obras.
 */
function inicializarObras() {

    $("#obra_id").select2({
        theme: "bootstrap4",
        width: "100%",
        placeholder: "Seleccione una obra...",
        allowClear: true,
        minimumInputLength: 0,

        ajax: {
            url: "../../controller/TrazabilidadObra.php?op=listar_obras",
            type: "POST",
            dataType: "json",
            delay: 250,

            data: function (params) {
                return {
                    buscar: params.term || ""
                };
            },

            processResults: function (response) {

                console.log(
                    "Respuesta obras:",
                    response
                );

                return {
                    results: response.data
                };
            }
        }
    });
}


/* ============================================================
 * VEHÍCULOS - DETALLE
 * ============================================================ */

/**
 * Inicializa los Select2 de vehículos del detalle.
 */
function inicializarVolquetas() {

    $(".select2-volqueta").each(
        function () {

            inicializarSelect2Volqueta(
                $(this)
            );
        }
    );
}


/**
 * Configura un Select2 de vehículo.
 */
function inicializarSelect2Volqueta(select) {

    if (
        select.hasClass(
            "select2-hidden-accessible"
        )
    ) {
        return;
    }

    select.select2({
        theme: "bootstrap4",
        width: "100%",
        placeholder: "Seleccione...",
        allowClear: true,

        ajax: {
            url: "../../controller/TrazabilidadObra.php?op=listar_vehiculos",
            type: "POST",
            dataType: "json",
            delay: 250,

            data: function (params) {

                return {
                    buscar: params.term || ""
                };
            },

            processResults: function (response) {

                return {
                    results: response.data || []
                };
            },

            cache: true
        }
    });
}


/* ============================================================
 * VEHÍCULOS - LLEGADA
 * ============================================================ */

/**
 * Inicializa los vehículos de llegada.
 */
function inicializarVolquetasLlegada() {

    $(".select2-volqueta-llegada").each(
        function () {

            inicializarSelect2VolquetaLlegada(
                $(this)
            );
        }
    );
}


/**
 * Configura Select2 de vehículo para llegada.
 */
function inicializarSelect2VolquetaLlegada(
    select
) {

    if (
        select.hasClass(
            "select2-hidden-accessible"
        )
    ) {
        return;
    }

    select.select2({
        theme: "bootstrap4",
        width: "100%",
        placeholder: "Seleccione...",
        allowClear: true,

        ajax: {
            url: "../../controller/TrazabilidadObra.php?op=listar_vehiculos",
            type: "POST",
            dataType: "json",
            delay: 250,

            data: function (params) {

                return {
                    buscar: params.term || ""
                };
            },

            processResults: function (response) {

                return {
                    results: response.data || []
                };
            },

            cache: true
        }
    });
}


/* ============================================================
 * FECHA
 * ============================================================ */

/**
 * Inicializa la fecha del formato.
 */
function inicializarFecha() {

    $("#fecha_trazabilidad").daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,

        locale: {
            format: "DD-MM-YYYY",
            separator: " - ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Hasta",
            customRangeLabel: "Personalizado",
            weekLabel: "S",
            daysOfWeek: [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            monthNames: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            firstDay: 1
        }
    });


    $("#fecha_trazabilidad").on(
        "apply.daterangepicker",
        function (ev, picker) {

            $(this).val(
                picker.startDate.format(
                    "DD-MM-YYYY"
                )
            );
        }
    );


    $("#fecha_trazabilidad").on(
        "cancel.daterangepicker",
        function () {

            $(this).val("");
        }
    );
}


/* ============================================================
 * VISIBILIDAD DE COLUMNAS
 * ============================================================ */

/**
 * Configura las columnas según el tipo de actividad.
 */
function configurarColumnasDetalle(
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


    limpiarCamposOcultos(
        casillasVisibles
    );
}


/**
 * Limpia valores de columnas que no aplican
 * para el tipo de actividad seleccionado.
 */
function limpiarCamposOcultos(
    casillasVisibles
) {

    $("#tbody_detalle_aplicacion")
        .find(".fila-detalle")
        .each(function () {

            const fila = $(this);

            fila.find(
                "td[data-casilla]"
            ).each(function () {

                const celda = $(this);

                const casilla = parseInt(
                    celda.attr("data-casilla"),
                    10
                );


                if (
                    casillasVisibles.indexOf(
                        casilla
                    ) === -1
                ) {

                    celda.find("input")
                        .val("");

                    celda.find("select")
                        .val(null)
                        .trigger("change");
                }
            });
        });
}


/* ============================================================
 * FILAS DETALLE
 * ============================================================ */

/**
 * Agrega una nueva fila al detalle.
 */
function agregarFilaDetalle() {

    const fila = plantillaDetalle
        .clone(false, false);


    limpiarFilaClonada(fila);


    $("#tbody_detalle_aplicacion")
        .append(fila);


    reindexarFilasDetalle();


    inicializarSelect2Volqueta(
        fila.find(".select2-volqueta")
    );


    const tipoActividad =
        $("input[name='tipo_actividad']:checked")
            .val() || "";


    configurarColumnasDetalle(
        tipoActividad
    );
}


/**
 * Elimina una fila del detalle.
 */
function eliminarFilaDetalle(boton) {

    const filas =
        $("#tbody_detalle_aplicacion .fila-detalle");


    const fila =
        boton.closest("tr");


    if (filas.length === 1) {

        limpiarFilaDetalle(
            fila
        );

        return;
    }


    const select =
        fila.find(".select2-volqueta");


    if (
        select.hasClass(
            "select2-hidden-accessible"
        )
    ) {
        select.select2("destroy");
    }


    fila.remove();


    reindexarFilasDetalle();
}


/**
 * Limpia una fila del detalle.
 */
function limpiarFilaDetalle(fila) {

    fila.find("input")
        .val("");


    fila.find("select")
        .val(null)
        .trigger("change");
}


/**
 * Reindexa los nombres enviados al controller.
 */
function reindexarFilasDetalle() {

    $("#tbody_detalle_aplicacion .fila-detalle")
        .each(function (indice) {

            const fila = $(this);

            fila.attr(
                "data-fila",
                indice
            );


            fila.find("[data-campo]")
                .each(function () {

                    const campo =
                        $(this).data("campo");

                    $(this).attr(
                        "name",
                        "detalle["
                        + indice
                        + "]["
                        + campo
                        + "]"
                    );
                });
        });
}


/* ============================================================
 * CÁLCULOS DEL DETALLE
 * ============================================================ */

/**
 * Realiza los cálculos automáticos de una fila.
 */
function calcularFilaDetalle(fila) {

    const longitud =
        obtenerNumero(
            fila.find(
                "[data-campo='longitud']"
            ).val()
        );


    const ancho =
        obtenerNumero(
            fila.find(
                "[data-campo='ancho']"
            ).val()
        );


    const espesorDemolido =
        obtenerNumero(
            fila.find(
                "[data-campo='espesor_demolido']"
            ).val()
        );


    const espesorExcavacion =
        obtenerNumero(
            fila.find(
                "[data-campo='espesor_excavacion']"
            ).val()
        );


    const espesorBase =
        obtenerNumero(
            fila.find(
                "[data-campo='espesor_base']"
            ).val()
        );


    const capasImprimacion =
        obtenerNumero(
            fila.find(
                "[data-campo='capas_imprimacion']"
            ).val()
        );


    const volumenDemolido =
        longitud
        * ancho
        * espesorDemolido;


    const volumenExcavado =
        longitud
        * ancho
        * espesorExcavacion;


    const volumenBase =
        longitud
        * ancho
        * espesorBase;


    const imprimacion =
        longitud
        * ancho
        * capasImprimacion;


    fila.find(
        "[data-campo='volumen_demolido']"
    ).val(
        formatearNumero(
            volumenDemolido
        )
    );


    fila.find(
        "[data-campo='volumen_excavado']"
    ).val(
        formatearNumero(
            volumenExcavado
        )
    );


    fila.find(
        "[data-campo='volumen_base']"
    ).val(
        formatearNumero(
            volumenBase
        )
    );


    fila.find(
        "[data-campo='imprimacion']"
    ).val(
        formatearNumero(
            imprimacion
        )
    );
}


/* ============================================================
 * FILAS DE LLEGADA
 * ============================================================ */

/**
 * Agrega una fila al control de llegada.
 */
function agregarFilaLlegada() {

    const fila =
        plantillaLlegada
            .clone(false, false);


    limpiarFilaClonada(fila);


    $("#tbody_llegada_mezcla")
        .append(fila);


    reindexarFilasLlegada();


    inicializarSelect2VolquetaLlegada(
        fila.find(
            ".select2-volqueta-llegada"
        )
    );
}


/**
 * Elimina una fila de llegada.
 */
function eliminarFilaLlegada(boton) {

    const filas =
        $("#tbody_llegada_mezcla .fila-llegada");


    const fila =
        boton.closest("tr");


    if (filas.length === 1) {

        limpiarFilaLlegada(
            fila
        );

        return;
    }


    const select =
        fila.find(
            ".select2-volqueta-llegada"
        );


    if (
        select.hasClass(
            "select2-hidden-accessible"
        )
    ) {
        select.select2("destroy");
    }


    fila.remove();


    reindexarFilasLlegada();
}


/**
 * Limpia una fila de llegada.
 */
function limpiarFilaLlegada(fila) {

    fila.find("input")
        .val("");


    fila.find("select")
        .val(null)
        .trigger("change");
}


/**
 * Reindexa las filas de llegada.
 */
function reindexarFilasLlegada() {

    $("#tbody_llegada_mezcla .fila-llegada")
        .each(function (indice) {

            const fila = $(this);

            fila.attr(
                "data-fila",
                indice
            );


            fila.find("[data-campo]")
                .each(function () {

                    const campo =
                        $(this).data("campo");

                    $(this).attr(
                        "name",
                        "llegada["
                        + indice
                        + "]["
                        + campo
                        + "]"
                    );
                });
        });
}


/**
 * Calcula el factor de compactación.
 */
function calcularFactorCompactacion(
    fila
) {

    const volumenLlegada =
        obtenerNumero(
            fila.find(
                "[data-campo='volumen_llegada']"
            ).val()
        );


    const volumenAplicado =
        obtenerNumero(
            fila.find(
                "[data-campo='volumen_aplicado']"
            ).val()
        );


    if (
        volumenLlegada <= 0
        || volumenAplicado <= 0
    ) {

        fila.find(
            "[data-campo='factor_compactacion']"
        ).val("");

        return;
    }


    const factor =
        volumenLlegada
        / volumenAplicado;


    fila.find(
        "[data-campo='factor_compactacion']"
    ).val(
        factor.toFixed(3)
    );
}


/* ============================================================
 * GUARDAR BORRADOR
 * ============================================================ */

/**
 * Guarda o actualiza el borrador.
 */
function guardarBorrador() {

    const boton =
        $("#btn_guardar_borrador");


    boton.prop(
        "disabled",
        true
    );


    $.ajax({
        url: "../../controller/TrazabilidadObra.php?op=guardar_borrador",
        type: "POST",
        dataType: "json",
        data: $("#form_trazabilidad").serialize(),

        success: function (response) {

            if (!response.success) {

                mostrarValidacion(
                    "No fue posible guardar",
                    response.message
                    || "No se pudo guardar el borrador."
                );

                return;
            }


            /*
             * Después del primer INSERT conservamos
             * el ID para que los siguientes guardados
             * realicen UPDATE.
             */
            $("#trazabilidad_id").val(
                response.trazabilidad_id
            );


            /*
             * Actualizamos la URL sin recargar la página.
             */
            window.history.replaceState(
                {},
                "",
                "trazabilidad.php?id="
                + response.trazabilidad_id
            );


            configurarModoFormulario();


            Swal.fire({
                icon: "success",
                title: "Borrador guardado",
                text: "La información fue guardada correctamente.",
                confirmButtonText: "Aceptar"
            });
        },

        error: function (xhr) {

            let mensaje =
                "Se presentó un error al guardar el borrador.";


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
        },

        complete: function () {

            boton.prop(
                "disabled",
                false
            );
        }
    });
}


/* ============================================================
 * CARGAR BORRADOR
 * ============================================================ */

/**
 * Carga un borrador existente.
 */
function cargarBorrador(
    trazabilidadId
) {

    $.ajax({
        url: "../../controller/TrazabilidadObra.php?op=cargar_borrador",
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
                    text: response.message
                }).then(function () {

                    window.location.href =
                        "inboxTrazabilidad.php";
                });

                return;
            }


            const data =
                response.data;


            cargarEncabezado(
                data.encabezado
            );

            cargarDetalle(
                data.detalle || []
            );

            cargarLlegadas(
                data.llegada || []
            );

            configurarModoFormulario();

            configurarEstadoFormulario(
                data.encabezado.estado_trazabilidad
            );
        },

        error: function (xhr) {

            let mensaje =
                "No fue posible cargar el formato.";


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
            }).then(function () {

                window.location.href =
                    "inboxTrazabilidad.php";
            });
        }
    });
}


/**
 * Carga la información general.
 */
function cargarEncabezado(data) {

    $("#tipo_mezcla")
        .val(
            data.tipo_mezcla_trazabilidad
        )
        .trigger("change");


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
    )
        .prop("checked", true)
        .trigger("change");


    $("#observaciones").val(
        data.observaciones_trazabilidad
        || ""
    );


    /*
     * Cargar obra seleccionada en Select2 AJAX.
     */
    if (data.obra_trazabilidad) {

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
            .append(option)
            .trigger("change");
    }
}


/**
 * Carga las filas del detalle.
 */
function cargarDetalle(datos) {

    const tbody =
        $("#tbody_detalle_aplicacion");


    tbody.empty();


    if (datos.length === 0) {

        agregarFilaDetalle();

        return;
    }


    datos.forEach(
        function (item) {

            const fila =
                plantillaDetalle
                    .clone(false, false);


            limpiarFilaClonada(
                fila
            );


            tbody.append(fila);


            cargarVehiculoSelect(
                fila.find(
                    ".select2-volqueta"
                ),
                item.vehiculo_traz_det,
                item.vehi_placa
            );


            cargarCampo(
                fila,
                "pr_inicial",
                item.pr_inicial_traz_det
            );

            cargarCampo(
                fila,
                "pr_final",
                item.pr_final_traz_det
            );

            cargarCampo(
                fila,
                "numero_caja",
                item.numero_caja_traz_det
            );

            cargarCampo(
                fila,
                "longitud",
                item.longitud_traz_det
            );

            cargarCampo(
                fila,
                "ancho",
                item.ancho_traz_det
            );

            cargarCampo(
                fila,
                "espesor_demolido",
                item.espesor_demolido_traz_det
            );

            cargarCampo(
                fila,
                "espesor_excavacion",
                item.espesor_excavacion_traz_det
            );

            cargarCampo(
                fila,
                "espesor_base",
                item.espesor_base_traz_det
            );

            cargarCampo(
                fila,
                "espesor_mezcla",
                item.espesor_mezcla_traz_det
            );

            cargarCampo(
                fila,
                "volumen_demolido",
                item.volumen_demolido_traz_det
            );

            cargarCampo(
                fila,
                "volumen_excavado",
                item.volumen_excavado_traz_det
            );

            cargarCampo(
                fila,
                "volumen_base",
                item.volumen_base_traz_det
            );

            cargarCampo(
                fila,
                "capas_imprimacion",
                item.capas_imprimacion_traz_det
            );

            cargarCampo(
                fila,
                "imprimacion",
                item.imprimacion_traz_det
            );

            cargarCampo(
                fila,
                "temperatura_aplicacion",
                item.temperatura_aplicacion_traz_det
            );

            cargarCampo(
                fila,
                "volumen_mezcla",
                item.volumen_mezcla_traz_det
            );


            inicializarSelect2Volqueta(
                fila.find(
                    ".select2-volqueta"
                )
            );
        }
    );


    reindexarFilasDetalle();


    const actividad =
        $("input[name='tipo_actividad']:checked")
            .val() || "";


    configurarColumnasDetalle(
        actividad
    );
}


/**
 * Carga las filas del control de llegada.
 */
function cargarLlegadas(datos) {

    const tbody =
        $("#tbody_llegada_mezcla");


    tbody.empty();


    if (datos.length === 0) {

        agregarFilaLlegada();

        return;
    }


    datos.forEach(
        function (item) {

            const fila =
                plantillaLlegada
                    .clone(false, false);


            limpiarFilaClonada(
                fila
            );


            tbody.append(fila);


            cargarVehiculoSelect(
                fila.find(
                    ".select2-volqueta-llegada"
                ),
                item.vehiculo_traz_lleg,
                item.vehi_placa
            );


            cargarCampo(
                fila,
                "temperatura_llegada",
                item.temperatura_llegada_traz_lleg
            );

            cargarCampo(
                fila,
                "volumen_llegada",
                item.volumen_llegada_traz_lleg
            );

            cargarCampo(
                fila,
                "volumen_aplicado",
                item.volumen_aplicado_traz_lleg
            );

            cargarCampo(
                fila,
                "factor_compactacion",
                item.factor_compactacion_traz_lleg
            );


            inicializarSelect2VolquetaLlegada(
                fila.find(
                    ".select2-volqueta-llegada"
                )
            );
        }
    );


    reindexarFilasLlegada();
}


/* ============================================================
 * VALIDACIONES
 * ============================================================ */

/**
 * Valida los datos mínimos del borrador.
 */
function validarBorrador() {

    const tipoMezcla =
        $("#tipo_mezcla").val();

    const fecha =
        $("#fecha_trazabilidad").val();

    const tipoActividad =
        $("input[name='tipo_actividad']:checked")
            .val();

    const obra =
        $("#obra_id").val();


    if (!tipoMezcla) {

        mostrarValidacion(
            "Tipo de mezcla",
            "Seleccione el tipo de mezcla."
        );

        return false;
    }


    if (!fecha) {

        mostrarValidacion(
            "Fecha",
            "Seleccione la fecha del formato."
        );

        return false;
    }


    if (!tipoActividad) {

        mostrarValidacion(
            "Tipo de actividad",
            "Seleccione el tipo de actividad."
        );

        return false;
    }


    if (!obra) {

        mostrarValidacion(
            "Obra",
            "Seleccione la obra."
        );

        return false;
    }


    return true;
}


/**
 * Valida el formato antes de enviarlo.
 */
function validarEnvioAprobacion() {

    if (!validarBorrador()) {
        return false;
    }


    if (!validarDetalleAplicacion()) {
        return false;
    }


    if (!validarLlegadaMezcla()) {
        return false;
    }


    return true;
}


/**
 * Valida únicamente las casillas visibles
 * del detalle.
 */
function validarDetalleAplicacion() {

    const filas =
        $("#tbody_detalle_aplicacion .fila-detalle");


    if (filas.length === 0) {

        mostrarValidacion(
            "Detalle de aplicación",
            "Debe registrar al menos un detalle."
        );

        return false;
    }


    let valido = true;


    filas.each(function (indice) {

        const fila = $(this);


        fila.find(
            "td[data-casilla]:visible"
        ).each(function () {

            const celda =
                $(this);

            const casilla =
                celda.data("casilla");


            celda.find(
                "input:not([readonly]), select"
            ).each(function () {

                const campo =
                    $(this);


                if (
                    campo.val() === null
                    || campo.val() === ""
                ) {

                    mostrarValidacion(
                        "Detalle incompleto",
                        "Revise la casilla ["
                        + casilla
                        + "] de la fila "
                        + (indice + 1)
                        + "."
                    );


                    enfocarCampo(
                        campo
                    );


                    valido = false;

                    return false;
                }
            });


            return valido;
        });


        return valido;
    });


    return valido;
}


/**
 * Valida el control de llegada.
 */
function validarLlegadaMezcla() {

    const filas =
        $("#tbody_llegada_mezcla .fila-llegada");


    if (filas.length === 0) {

        mostrarValidacion(
            "Control de llegada",
            "Debe registrar al menos una llegada de mezcla."
        );

        return false;
    }


    let valido = true;


    filas.each(function (indice) {

        const fila = $(this);


        const campos = [
            "vehi_id",
            "temperatura_llegada",
            "volumen_llegada",
            "volumen_aplicado"
        ];


        campos.forEach(
            function (campo) {

                if (!valido) {
                    return;
                }


                const input =
                    fila.find(
                        "[data-campo='"
                        + campo
                        + "']"
                    );


                if (
                    input.val() === null
                    || input.val() === ""
                ) {

                    mostrarValidacion(
                        "Control de llegada incompleto",
                        "Revise la fila "
                        + (indice + 1)
                        + "."
                    );


                    enfocarCampo(
                        input
                    );


                    valido = false;
                }
            }
        );


        return valido;
    });


    return valido;
}


/* ============================================================
 * CANCELAR / VOLVER
 * ============================================================ */

/**
 * Cancela un formulario nuevo o regresa
 * a la bandeja según el estado del formato.
 */
function cancelarFormulario() {

    const trazabilidadId =
        $("#trazabilidad_id").val();


    /*
     * Si el formato ya fue enviado,
     * aprobado o rechazado, simplemente
     * regresa a la bandeja.
     */
    if (
        trazabilidadId &&
        estadoTrazabilidad !== 1
    ) {

        window.location.href =
            "inboxTrazabilidad.php";

        return;
    }


    /*
     * Si es un borrador, advierte sobre
     * posibles cambios sin guardar.
     */
    if (trazabilidadId) {

        Swal.fire({
            title: "¿Volver a la bandeja?",
            text: "Los cambios que no haya guardado se perderán.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, volver",
            cancelButtonText: "Continuar editando"
        }).then(function (result) {

            if (result.isConfirmed) {

                window.location.href =
                    "inboxTrazabilidad.php";
            }
        });

        return;
    }


    /*
     * Formulario nuevo.
     */
    Swal.fire({
        title: "¿Cancelar diligenciamiento?",
        text: "La información ingresada se perderá.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "Continuar diligenciando"
    }).then(function (result) {

        if (result.isConfirmed) {

            limpiarFormulario();

            window.location.href =
                "inboxTrazabilidad.php";
        }
    });
}

/**
 * Guarda la información actual y posteriormente
 * envía el formato a aprobación.
 */
function enviarAprobacion() {

    const botonEnviar =
        $("#btn_enviar_aprobacion");

    const botonGuardar =
        $("#btn_guardar_borrador");


    botonEnviar.prop(
        "disabled",
        true
    );

    botonGuardar.prop(
        "disabled",
        true
    );


    /*
     * Primero se guarda toda la información actual.
     * Si es nuevo crea el borrador.
     * Si ya existe actualiza el borrador.
     */
    $.ajax({
        url: "../../controller/TrazabilidadObra.php?op=guardar_borrador",
        type: "POST",
        dataType: "json",
        data: $("#form_trazabilidad").serialize(),

        success: function (response) {

            if (!response.success) {

                Swal.fire({
                    icon: "warning",
                    title: "No fue posible enviar",
                    text: response.message
                        || "No se pudo guardar la información del formato."
                });

                habilitarBotonesEnvio();

                return;
            }


            /*
             * Guardamos el ID retornado por el backend.
             */
            $("#trazabilidad_id").val(
                response.trazabilidad_id
            );


            /*
             * Con el borrador actualizado,
             * procedemos a enviarlo a aprobación.
             */
            enviarFormatoAprobacion(
                response.trazabilidad_id
            );
        },

        error: function (xhr) {

            mostrarErrorEnvio(
                xhr,
                "No fue posible guardar la información antes de enviarla."
            );

            habilitarBotonesEnvio();
        }
    });
}

/**
 * Cambia el formato de BORRADOR
 * a PENDIENTE DE APROBACIÓN.
 */
function enviarFormatoAprobacion(
    trazabilidadId
) {

    $.ajax({
        url: "../../controller/TrazabilidadObra.php?op=enviar_aprobacion",
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
                    title: "No fue posible enviar",
                    text: response.message
                        || "El formato no pudo ser enviado a aprobación."
                });

                habilitarBotonesEnvio();

                return;
            }


            Swal.fire({
                icon: "success",
                title: "Formato enviado",
                text: "El formato fue enviado a aprobación correctamente.",
                confirmButtonText: "Aceptar",
                allowOutsideClick: false
            }).then(function () {

                window.location.href =
                    "inboxTrazabilidad.php";
            });
        },

        error: function (xhr) {

            mostrarErrorEnvio(
                xhr,
                "Se presentó un error al enviar el formato a aprobación."
            );

            habilitarBotonesEnvio();
        }
    });
}



/**
 * Habilita nuevamente los botones
 * cuando el proceso de envío falla.
 */
function habilitarBotonesEnvio() {

    $("#btn_enviar_aprobacion")
        .prop("disabled", false);

    $("#btn_guardar_borrador")
        .prop("disabled", false);
}


/**
 * Muestra los errores retornados
 * durante el proceso de envío.
 */
function mostrarErrorEnvio(
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
 * Configura el formulario según el estado
 * de la trazabilidad.
 */
function configurarEstadoFormulario(estado) {

    estado = parseInt(
        estado,
        10
    );

    estadoTrazabilidad = estado;

    /*
     * Estado 1 = BORRADOR.
     * El formulario continúa editable.
     */
    if (estado === 1) {

        $("#form_trazabilidad")
            .find("input, select, textarea")
            .prop("disabled", false);

        /*
         * Los campos calculados continúan
         * siendo solo lectura.
         */
        $("#form_trazabilidad")
            .find("input[readonly]")
            .prop("readonly", true);

        $("#btn_agregar_detalle").show();
        $("#btn_agregar_llegada").show();

        $(".btn-eliminar-detalle").show();
        $(".btn-eliminar-llegada").show();

        $("#btn_guardar_borrador").show();
        $("#btn_enviar_aprobacion").show();

        return;
    }


    /*
     * Estado diferente de BORRADOR.
     * El usuario solo puede consultar.
     */
    $("#form_trazabilidad")
        .find("input, select, textarea")
        .prop("disabled", true);


    $("#btn_agregar_detalle").hide();
    $("#btn_agregar_llegada").hide();

    $(".btn-eliminar-detalle").hide();
    $(".btn-eliminar-llegada").hide();

    $("#btn_guardar_borrador").hide();
    $("#btn_enviar_aprobacion").hide();


    /*
     * El botón para regresar a la bandeja
     * debe permanecer disponible.
     */
    $("#btn_cancelar")
        .prop("disabled", false);
}

/**
 * Cambia el nombre del botón según el modo.
 */
function configurarModoFormulario() {

    const trazabilidadId =
        $("#trazabilidad_id").val();


    if (trazabilidadId) {

        $("#btn_cancelar").html(
            '<i class="fas fa-arrow-left mr-1"></i>'
            + " Volver a bandeja"
        );

        return;
    }


    $("#btn_cancelar").html(
        '<i class="fas fa-times mr-1"></i>'
        + " Cancelar"
    );
}


/**
 * Limpia el formulario.
 */
function limpiarFormulario() {

    $("#form_trazabilidad")[0]
        .reset();


    $("#trazabilidad_id")
        .val("");


    $("#obra_id")
        .val(null)
        .trigger("change");


    $("#tipo_mezcla")
        .val(null)
        .trigger("change");


    $("#tbody_detalle_aplicacion")
        .empty()
        .append(
            plantillaDetalle.clone(false, false)
        );


    $("#tbody_llegada_mezcla")
        .empty()
        .append(
            plantillaLlegada.clone(false, false)
        );


    limpiarFilaClonada(
        $("#tbody_detalle_aplicacion .fila-detalle:first")
    );


    limpiarFilaClonada(
        $("#tbody_llegada_mezcla .fila-llegada:first")
    );


    reindexarFilasDetalle();
    reindexarFilasLlegada();

    inicializarVolquetas();
    inicializarVolquetasLlegada();

    configurarColumnasDetalle("");
}


/* ============================================================
 * FUNCIONES AUXILIARES
 * ============================================================ */

/**
 * Limpia una fila clonada y elimina rastros
 * generados por Select2.
 */
function limpiarFilaClonada(fila) {

    fila.find(".select2-container")
        .remove();


    fila.find("select")
        .each(function () {

            $(this)
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
                )
                .val("");
        });


    fila.find("option")
        .removeAttr(
            "data-select2-id"
        );


    fila.find("input")
        .val("");
}


/**
 * Carga un valor usando data-campo.
 */
function cargarCampo(
    fila,
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


/**
 * Carga un vehículo seleccionado dentro
 * de un Select2 AJAX.
 */
function cargarVehiculoSelect(
    select,
    vehiId,
    placa
) {

    if (!vehiId) {
        return;
    }


    const option =
        new Option(
            placa || vehiId,
            vehiId,
            true,
            true
        );


    select
        .append(option)
        .trigger("change");
}


/**
 * Obtiene un parámetro de la URL.
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
 * Convierte YYYY-MM-DD a DD-MM-YYYY.
 */
function formatearFechaFormulario(
    fecha
) {

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


/**
 * Convierte un valor a número.
 */
function obtenerNumero(valor) {

    const numero =
        parseFloat(valor);


    return isNaN(numero)
        ? 0
        : numero;
}


/**
 * Formatea valores calculados.
 */
function formatearNumero(valor) {

    if (
        !valor
        || valor === 0
    ) {
        return "";
    }


    return valor.toFixed(3);
}


/**
 * Enfoca el campo que tiene una validación.
 */
function enfocarCampo(campo) {

    if (campo.is("select")) {

        campo.select2(
            "open"
        );

        return;
    }


    campo.focus();
}


/**
 * Muestra validaciones mediante SweetAlert2.
 */
function mostrarValidacion(
    titulo,
    mensaje
) {

    Swal.fire({
        icon: "warning",
        title: titulo,
        text: mensaje,
        confirmButtonText: "Aceptar"
    });
}

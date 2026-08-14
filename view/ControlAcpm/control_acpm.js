let saldoSiesa = 0;
let galonesPendientesSiesa = 0;
let totalGalonesDespachados = 0;

let saldoSiesaCargado = false;
let resumenDespachosCargado = false;

let tablaPendientesSiesa;

function init() {
    consultarInventarioAcpmSiesa();

    consultarResumenControlAcpm();

    consultarDespachosObraHoy();

    cargarPendientesSiesa();
}

$(document).on(
    'click',
    '#btn_actualizar_siesa',
    function () {

        consultarInventarioAcpmSiesa();

    }
);

/*
|--------------------------------------------------------------------------
| CONSULTAR INVENTARIO ACPM SIESA
|--------------------------------------------------------------------------
*/
function consultarInventarioAcpmSiesa() {

    $.ajax({

        url: "../../controller/ControlAcpm.php?op=consultarInventarioAcpmSiesa",

        type: "GET",

        dataType: "json",

        beforeSend: function () {

            $("#btn_actualizar_siesa")
                .prop("disabled", true)
                .html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    'Consultando...'
                );
        },

        success: function (data) {

            if (data.status === "success") {

                /*
                 * SALDO REAL RETORNADO POR SIESA
                 */
                saldoSiesa =
                    parseFloat(
                        data.f400_cant_existencia_1
                    ) || 0;


                /*
                 * UNIDAD RETORNADA POR SIESA
                 */
                let unidadSiesa =
                    data.f120_id_unidad_inventario
                    || "GL";


                saldoSiesaCargado = true;


                /*
                 * FORMATEAR SALDO
                 */
                let saldoFormateado =
                    saldoSiesa.toLocaleString(
                        "es-CO",
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    )
                    + " "
                    + unidadSiesa;


                /*
                 * KPI PRINCIPAL
                 */
                $("#kpi_saldo_siesa")
                    .text(saldoFormateado);


                /*
                 * BLOQUE DISPONIBILIDAD
                 */
                $("#resumen_saldo_siesa")
                    .text(saldoFormateado);


                /*
                 * HORA DE LA CONSULTA
                 */
                $("#ultima_actualizacion_siesa")
                    .text(
                        moment().format(
                            "DD/MM/YYYY HH:mm:ss"
                        )
                    );


                /*
                 * RECALCULAR DISPONIBILIDAD
                 */
                calcularDisponibilidadOperativa();

            } else {

                saldoSiesaCargado = false;

                Swal.fire({
                    icon: "warning",
                    title: "Inventario SIESA",
                    text: data.message
                });
            }
        },

        error: function (xhr) {

            saldoSiesaCargado = false;

            console.log(xhr.responseText);

            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No fue posible consultar el inventario de ACPM en SIESA."
            });
        },

        complete: function () {

            $("#btn_actualizar_siesa")
                .prop("disabled", false)
                .html(
                    '<i class="fas fa-sync-alt mr-1"></i>' +
                    'Actualizar SIESA'
                );
        }

    });
}

/* function consultarInventarioAcpmSiesa() {

    $.ajax({
        url: '../../controller/ControlAcpm.php?op=consultarInventarioAcpmSiesa',
        type: 'GET',
        dataType: 'json',

        beforeSend: function () {

            $('#btn_actualizar_siesa')
                .prop('disabled', true)
                .html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    'Consultando...'
                );
        },

        success: function (data) {

            if (data.status === 'success') {

                let existencia = parseFloat(
                    data.f400_cant_existencia_1
                ) || 0;

                let unidad =
                    data.f120_id_unidad_inventario || 'GL';

                let existenciaFormateada =
                    existencia.toLocaleString(
                        'es-CO',
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 4
                        }
                    ) + ' ' + unidad;

                $('#kpi_saldo_siesa')
                    .text(existenciaFormateada);

                $('#resumen_saldo_siesa')
                    .text(existenciaFormateada);

                $('#ultima_actualizacion_siesa')
                    .text(
                        moment().format(
                            'DD/MM/YYYY HH:mm:ss'
                        )
                    );

            } else {

                Swal.fire({
                    icon: 'warning',
                    title: 'SIESA',
                    text: data.message
                });
            }
        },

        error: function () {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No fue posible consultar el inventario de ACPM en SIESA.'
            });
        },

        complete: function () {

            $('#btn_actualizar_siesa')
                .prop('disabled', false)
                .html(
                    '<i class="fas fa-sync-alt mr-1"></i>' +
                    'Actualizar SIESA'
                );
        }
    });
} */

/*
|--------------------------------------------------------------------------
| RESUMEN CONTROL ACPM DEL DIA
|--------------------------------------------------------------------------
*/
function consultarResumenControlAcpm() {

    $.ajax({

        url: "../../controller/ControlAcpm.php?op=resumenControlAcpmHoy",

        type: "GET",

        dataType: "json",

        success: function (data) {

            if (data.status !== "success") {

                Swal.fire({
                    icon: "warning",
                    title: "Control ACPM",
                    text: data.message
                });

                return;
            }


            /*
             * DATOS GENERALES
             */
            let totalDespachos =
                parseInt(data.total_despachos) || 0;

            let totalGalones =
                parseFloat(data.total_galones) || 0;

            totalGalonesDespachados =
                parseFloat(data.total_galones) || 0;

            let totalPendientes =
                parseInt(
                    data.total_pendientes_siesa
                ) || 0;

            galonesPendientesSiesa =
                parseFloat(
                    data.galones_pendientes_siesa
                ) || 0;

            let totalRegistrados =
                parseInt(
                    data.total_registrados_siesa
                ) || 0;

            let galonesRegistrados =
                parseFloat(
                    data.galones_registrados_siesa
                ) || 0;


            resumenDespachosCargado = true;


            /*
             * KPI DESPACHADO HOY
             */
            $("#kpi_despachado_hoy").text(
                formatoGalones(totalGalones)
            );

            $("#kpi_numero_despachos_hoy")
                .text(totalDespachos);


            /*
             * KPI PENDIENTE SIESA
             */
            $("#kpi_pendiente_siesa").text(
                formatoGalones(
                    galonesPendientesSiesa
                )
            );

            $("#kpi_numero_pendientes_siesa")
                .text(totalPendientes);

            $("#resumen_despachado_hoy")
                .text(
                    "- " +
                    formatoGalones(
                        totalGalonesDespachados
                    )
                );

            /*
             * BLOQUE DISPONIBILIDAD
             */
            /*             $("#resumen_pendiente_siesa")
                            .text(
                                "- " +
                                formatoGalones(
                                    galonesPendientesSiesa
                                )
                            ); */


            /*
             * CONCILIACION
             */
            $("#conciliacion_total_despachado")
                .text(
                    formatoNumero(totalGalones)
                );

            $("#conciliacion_registrado_siesa")
                .text(
                    formatoNumero(
                        galonesRegistrados
                    )
                );

            $("#conciliacion_pendiente_siesa")
                .text(
                    formatoNumero(
                        galonesPendientesSiesa
                    )
                );


            /*
             * PORCENTAJE CONCILIADO
             */
            let porcentajeConciliado = 0;

            if (totalGalones > 0) {

                porcentajeConciliado =
                    (
                        galonesRegistrados
                        /
                        totalGalones
                    ) * 100;
            }


            $("#porcentaje_conciliado_siesa")
                .text(
                    porcentajeConciliado
                        .toFixed(1)
                        .replace(".", ",")
                    + "%"
                );


            $("#barra_conciliacion_siesa")
                .css(
                    "width",
                    porcentajeConciliado + "%"
                )
                .attr(
                    "aria-valuenow",
                    porcentajeConciliado
                );


            /*
             * BADGE PENDIENTES
             */
            $("#badge_pendientes_siesa")
                .text(
                    totalPendientes
                    + " pendientes - "
                    + formatoGalones(
                        galonesPendientesSiesa
                    )
                );


            /*
             * DISPONIBILIDAD OPERATIVA
             */
            calcularDisponibilidadOperativa();
        },

        error: function (xhr) {

            console.log(xhr.responseText);

            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No fue posible consultar el resumen de despachos."
            });
        }

    });
}


/*
|--------------------------------------------------------------------------
| DISPONIBILIDAD OPERATIVA
|--------------------------------------------------------------------------
|
| Saldo SIESA
| -
| Galones despachados que todavía no tienen documento SIESA
|
*/
function calcularDisponibilidadOperativa() {

    /*
     * Esperamos a tener las dos consultas.
     */
    if (
        !saldoSiesaCargado ||
        !resumenDespachosCargado
    ) {
        return;
    }


    let disponibleOperativo =
        saldoSiesa - totalGalonesDespachados;


    $("#kpi_disponibilidad_operativa")
        .text(
            formatoGalones(
                disponibleOperativo
            )
        );


    $("#resumen_disponibilidad_operativa")
        .text(
            formatoGalones(
                disponibleOperativo
            )
        );
}


/*
|--------------------------------------------------------------------------
| DESPACHOS AGRUPADOS POR OBRA
|--------------------------------------------------------------------------
*/
function consultarDespachosObraHoy() {

    $.ajax({

        url: "../../controller/ControlAcpm.php?op=despachosObraHoy",

        type: "GET",

        dataType: "json",

        success: function (data) {

            if (data.status !== "success") {
                return;
            }


            let html = "";

            let totalGeneral = 0;

            let mayorCantidad = 0;


            /*
             * PRIMER RECORRIDO:
             * obtener total y valor máximo.
             */
            $.each(
                data.data,
                function (index, row) {

                    let galones =
                        parseFloat(
                            row.total_galones
                        ) || 0;

                    totalGeneral += galones;

                    if (
                        galones > mayorCantidad
                    ) {
                        mayorCantidad = galones;
                    }
                }
            );


            /*
             * SEGUNDO RECORRIDO:
             * construir barras.
             */
            $.each(
                data.data,
                function (index, row) {

                    let galones =
                        parseFloat(
                            row.total_galones
                        ) || 0;

                    let totalDespachos =
                        parseInt(
                            row.total_despachos
                        ) || 0;


                    let porcentajeBarra = 0;

                    if (mayorCantidad > 0) {

                        porcentajeBarra =
                            (
                                galones
                                /
                                mayorCantidad
                            ) * 100;
                    }


                    html += `
                        <div class="mb-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <strong>
                                        ${row.obras_nom}
                                    </strong>

                                    <small class="text-muted ml-2">
                                        ${totalDespachos}
                                        despacho${totalDespachos === 1 ? "" : "s"}
                                    </small>

                                </div>

                                <strong>
                                    ${formatoGalones(galones)}
                                </strong>

                            </div>

                            <div class="progress mt-1">

                                <div
                                    class="progress-bar bg-primary"
                                    role="progressbar"
                                    style="width: ${porcentajeBarra}%"
                                    aria-valuenow="${porcentajeBarra}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>

                            </div>

                        </div>
                    `;
                }
            );


            /*
             * SIN DESPACHOS
             */
            if (data.data.length === 0) {

                html = `
                    <div class="text-center text-muted py-4">

                        <i class="fas fa-gas-pump fa-2x mb-2"></i>

                        <p class="mb-0">
                            No se han registrado despachos hoy.
                        </p>

                    </div>
                `;
            }


            $("#contenedor_despachos_obra")
                .html(html);


            $("#total_despachos_obra")
                .text(
                    formatoGalones(
                        totalGeneral
                    )
                );
        },

        error: function (xhr) {

            console.log(xhr.responseText);

        }

    });
}


/*
|--------------------------------------------------------------------------
| DATATABLE PENDIENTES SIESA
|--------------------------------------------------------------------------
|
| Mantiene el patrón que actualmente utiliza el módulo AcpmDesp.
|
*/
function cargarPendientesSiesa() {

    tablaPendientesSiesa =
        $("#pendientes_siesa_data")
            .dataTable({

                aProcessing: true,

                aServerSide: true,

                searching: true,

                lengthChange: false,

                responsive: true,

                autoWidth: false,

                bInfo: true,

                iDisplayLength: 7,

                bDestroy: true,

                ajax: {

                    url:
                        "../../controller/ControlAcpm.php?op=listarPendientesSiesa",

                    type: "POST",

                    dataType: "json",

                    error: function (e) {

                        console.log(
                            e.responseText
                        );
                    }
                },

                order: [
                    [0, "asc"]
                ],

                language: {

                    sProcessing:
                        "Procesando...",

                    sLengthMenu:
                        "Mostrar _MENU_ registros",

                    sZeroRecords:
                        "No se encontraron resultados",

                    sEmptyTable:
                        "No hay despachos pendientes de registrar en SIESA",

                    sInfo:
                        "Mostrando un total de _TOTAL_ registros",

                    sInfoEmpty:
                        "Mostrando un total de 0 registros",

                    sInfoFiltered:
                        "(filtrado de un total de _MAX_ registros)",

                    sSearch:
                        "Buscar:",

                    sLoadingRecords:
                        "Cargando...",

                    oPaginate: {

                        sFirst:
                            "Primero",

                        sLast:
                            "Último",

                        sNext:
                            "Siguiente",

                        sPrevious:
                            "Anterior"
                    }
                }

            })
            .DataTable();
}

/*
|--------------------------------------------------------------------------
| GUARDAR DOCUMENTO INTERNO SIESA
|--------------------------------------------------------------------------
*/
$(document).on(
    "click",
    "#btn_guardar_documento_siesa",
    function () {

        let desp_id =
            $("#despacho_id_siesa").val();

        let desp_documento_siesa =
            $("#documento_interno_siesa")
                .val()
                .trim();


        /*
        |--------------------------------------------------------------------------
        | VALIDACIONES
        |--------------------------------------------------------------------------
        */
        if (
            desp_id === "" ||
            desp_id === null
        ) {

            Swal.fire({
                icon: "warning",
                title: "Validación",
                text: "No se pudo identificar el despacho."
            });

            return;
        }


        if (desp_documento_siesa === "") {

            Swal.fire({
                icon: "warning",
                title: "Documento SIESA",
                text: "Ingrese el documento interno registrado en SIESA."
            });

            $("#documento_interno_siesa")
                .focus();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CONFIRMACION
        |--------------------------------------------------------------------------
        */
        Swal.fire({

            icon: "question",

            title: "Confirmar documento",

            html:
                "¿Desea asociar el documento SIESA " +
                "<strong>" +
                desp_documento_siesa +
                "</strong> al despacho <strong>#" +
                desp_id +
                "</strong>?",

            showCancelButton: true,

            confirmButtonText:
                "Sí, guardar",

            cancelButtonText:
                "Cancelar"

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */
            $.ajax({

                url:
                    "../../controller/ControlAcpm.php?op=guardarDocumentoSiesa",

                type:
                    "POST",

                dataType:
                    "json",

                data: {

                    desp_id:
                        desp_id,

                    desp_documento_siesa:
                        desp_documento_siesa

                },

                beforeSend: function () {

                    $("#btn_guardar_documento_siesa")
                        .prop(
                            "disabled",
                            true
                        )
                        .html(
                            '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                            'Guardando...'
                        );
                },

                success: function (data) {

                    if (
                        data.status ===
                        "success"
                    ) {

                        /*
                         * CERRAR MODAL
                         */
                        $("#modal_documento_siesa")
                            .modal("hide");


                        /*
                         * LIMPIAR CAMPOS
                         */
                        $("#despacho_id_siesa")
                            .val("");

                        $("#despacho_resumen_siesa")
                            .val("");

                        $("#documento_interno_siesa")
                            .val("");


                        /*
                         * MENSAJE
                         */
                        Swal.fire({
                            icon: "success",
                            title: "Correcto",
                            text: data.message,
                            timer: 1800,
                            showConfirmButton: false
                        });


                        /*
                        |--------------------------------------------------------------------------
                        | RECARGAR INFORMACION DEL DASHBOARD
                        |--------------------------------------------------------------------------
                        */

                        /*
                         * El despacho debe desaparecer
                         * de la tabla de pendientes.
                         */
                        if (tablaPendientesSiesa) {

                            tablaPendientesSiesa
                                .ajax
                                .reload(
                                    null,
                                    false
                                );
                        }


                        /*
                         * Recalcular:
                         *
                         * - Despachado hoy
                         * - Pendiente SIESA
                         * - Con documento
                         * - % conciliado
                         */
                        consultarResumenControlAcpm();


                        /*
                         * Volver a consultar SIESA porque
                         * el usuario indica que el movimiento
                         * ya fue registrado allí.
                         */
                        consultarInventarioAcpmSiesa();


                        /*
                         * Los despachos por obra no cambian,
                         * por lo que no es obligatorio
                         * recargarlos aquí.
                         */

                    } else {

                        Swal.fire({
                            icon: "warning",
                            title: "Documento SIESA",
                            text: data.message
                        });
                    }
                },

                error: function (xhr) {

                    console.log(
                        xhr.responseText
                    );

                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No fue posible registrar el documento SIESA."
                    });
                },

                complete: function () {

                    $("#btn_guardar_documento_siesa")
                        .prop(
                            "disabled",
                            false
                        )
                        .html(
                            '<i class="fas fa-save mr-1"></i>' +
                            'Guardar documento'
                        );
                }

            });

        });

    }
);

function registrarDocumentoSiesa(desp_id) {

    console.log(
        "Despacho seleccionado:",
        desp_id
    );

    $("#despacho_id_siesa")
        .val(desp_id);

    $("#despacho_resumen_siesa")
        .val(
            "Despacho #" + desp_id
        );

    $("#documento_interno_siesa")
        .val("");


    $("#modal_documento_siesa")
        .modal("show");

}

$(document).on(
    "click",
    "#btn_historial_acpm",
    verHistorialAcpm
);


function verHistorialAcpm() {

    window.location.href =
        "../HistorialAcpm/historialAcpm.php";

}

/*
|--------------------------------------------------------------------------
| FORMATO DE NUMEROS
|--------------------------------------------------------------------------
*/
function formatoNumero(valor) {

    valor =
        parseFloat(valor) || 0;

    return valor.toLocaleString(
        "es-CO",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );
}


function formatoGalones(valor) {

    return formatoNumero(valor)
        + " gal";
}

init();
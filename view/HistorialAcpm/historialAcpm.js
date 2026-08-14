let tablaHistorialAcpm;

let filtrosHistorialAcpm = {
    fecha_inicio: "",
    fecha_final: "",
    desp_obra: "",
    desp_vehi: "",
    estado_siesa: ""
};


function init() {
    inicializarRangoFechasHistorial();
    inicializarSelectHistorial();
    cargarHistorialAcpm();
}



/*
 * COMBO OBRAS
 */
$.post(
    "../../controller/Obras.php?op=comboObras",
    function (data, status) {

        $("#historial_obra").html(
            "<option value=''>Todas las obras</option>"
            + data
        );

        $("#historial_obra")
            .val("")
            .trigger("change");
    }
);


/*
 * COMBO VEHICULOS / EQUIPOS
 */
$.post(
    "../../controller/Vehiculo.php?op=comboVehiculoPreop",
    function (data, status) {

        $("#historial_equipo").html(
            "<option value=''>Todos los equipos</option>"
            + data
        );

        $("#historial_equipo")
            .val("")
            .trigger("change");
    }
);

function cargarHistorialAcpm() {

    tablaHistorialAcpm =
        $("#historial_acpm_data")
            .dataTable({

                aProcessing: true,

                aServerSide: false,

                dom: "Bfrtip",

                searching: true,

                lengthChange: false,

                colReorder: true,

                buttons: [
                    "copyHtml5",
                    "excelHtml5",
                    "csvHtml5",
                    "pdfHtml5"
                ],

                ajax: {

                    url:
                        "../../controller/ControlAcpm.php?op=listarHistorialAcpm",

                    type:
                        "POST",

                    dataType:
                        "json",

                    data:
                        function (d) {

                            d.fecha_inicio =
                                filtrosHistorialAcpm.fecha_inicio;

                            d.fecha_final =
                                filtrosHistorialAcpm.fecha_final;

                            d.desp_obra =
                                filtrosHistorialAcpm.desp_obra;

                            d.desp_vehi =
                                filtrosHistorialAcpm.desp_vehi;

                            d.estado_siesa =
                                filtrosHistorialAcpm.estado_siesa;
                        },

                    dataSrc:
                        function (json) {

                            $("#historial_total_galones_tabla")
                                .text(
                                    formatoNumeroHistorial(
                                        json.total_galones
                                    )
                                );

                            return json.aaData;
                        },

                    error:
                        function (xhr) {

                            console.log(
                                xhr.responseText
                            );

                            Swal.fire({
                                icon: "error",
                                title: "Historial ACPM",
                                text: "No fue posible consultar el historial."
                            });
                        }
                },


                /*
                 * FECHA DESCENDENTE
                 */
                order: [
                    [0, "desc"],
                    [1, "desc"]
                ],


                bDestroy:
                    true,

                responsive:
                    true,

                bInfo:
                    true,

                iDisplayLength:
                    10,

                autoWidth:
                    false,


                language: {

                    sProcessing:
                        "Procesando...",

                    sLengthMenu:
                        "Mostrar _MENU_ registros",

                    sZeroRecords:
                        "No se encontraron resultados",

                    sEmptyTable:
                        "No hay despachos registrados en el mes actual",

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

function consultarHistorialAcpm() {

    let rango = $("#rango_fechas_historial")
        .data("daterangepicker");

    if (!rango) {

        Swal.fire({
            icon: "warning",
            title: "Período requerido",
            text: "Debe seleccionar un período para realizar la consulta."
        });

        return;
    }


    filtrosHistorialAcpm = {

        fecha_inicio:
            rango.startDate.format("YYYY-MM-DD"),

        fecha_final:
            rango.endDate.format("YYYY-MM-DD"),

        desp_obra:
            $("#historial_obra").val() || "",

        desp_vehi:
            $("#historial_equipo").val() || "",

        estado_siesa:
            $("#historial_estado_siesa").val() || ""

    };


    /*
     * RECARGAR DATATABLE
     */
    if (
        tablaHistorialAcpm
        &&
        $.fn.DataTable.isDataTable(
            "#historial_acpm_data"
        )
    ) {

        tablaHistorialAcpm
            .ajax
            .reload();

    } else {

        cargarHistorialAcpm();
    }
}

$("#form_historial_acpm").on(
    "submit",
    function (e) {

        e.preventDefault();

        consultarHistorialAcpm();
    }
);

function inicializarSelectHistorial() {

    $("#historial_obra").select2({
        theme: "bootstrap4",
        width: "100%"
    });


    $("#historial_equipo").select2({
        theme: "bootstrap4",
        width: "100%"
    });


    $("#historial_estado_siesa").select2({
        theme: "bootstrap4",
        width: "100%"
    });
}

function inicializarRangoFechasHistorial() {

    moment.locale("es");

    $("#rango_fechas_historial").daterangepicker({

        startDate:
            moment().startOf("month"),

        endDate:
            moment(),

        /*
         * No consultar fechas futuras.
         */
        maxDate:
            moment(),

        autoUpdateInput:
            true,

        locale: {

            format:
                "DD/MM/YYYY",

            separator:
                " - ",

            applyLabel:
                "Aplicar",

            cancelLabel:
                "Cancelar",

            fromLabel:
                "Desde",

            toLabel:
                "Hasta",

            customRangeLabel:
                "Personalizado",

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

            firstDay:
                1
        }

    });
}

$("#btn_limpiar_filtros").on(
    "click",
    function () {

        limpiarFiltrosHistorialAcpm();
    }
);


$("#btn_volver_control_acpm").on(
    "click",
    function () {

        volverControlAcpm();
    }
);

function limpiarFiltrosHistorialAcpm() {

    let rango = $("#rango_fechas_historial")
        .data("daterangepicker");

    /*
     * RESTABLECER PERIODO:
     * PRIMER DIA DEL MES HASTA HOY
     */
    rango.setStartDate(
        moment().startOf("month")
    );

    rango.setEndDate(
        moment()
    );


    /*
     * LIMPIAR SELECT
     */
    $("#historial_obra")
        .val("")
        .trigger("change");

    $("#historial_equipo")
        .val("")
        .trigger("change");

    $("#historial_estado_siesa")
        .val("")
        .trigger("change");


    /*
     * RESTABLECER FILTROS
     */
    filtrosHistorialAcpm = {

        fecha_inicio:
            moment()
                .startOf("month")
                .format("YYYY-MM-DD"),

        fecha_final:
            moment()
                .format("YYYY-MM-DD"),

        desp_obra:
            "",

        desp_vehi:
            "",

        estado_siesa:
            ""

    };


    /*
     * RECARGAR DATATABLE
     */
    tablaHistorialAcpm
        .ajax
        .reload();
}

function volverControlAcpm() {

    window.location.href =
        "../ControlAcpm/control_acpm.php";
}

//***UTILIDADES */

function formatoNumeroHistorial(valor) {

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

init();
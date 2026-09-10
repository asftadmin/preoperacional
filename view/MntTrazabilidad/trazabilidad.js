$(document).ready(function () {
    // Inicializa los componentes generales.
    inicializarSelect2();
    inicializarFecha();

    // Inicializa los Select2 de la tabla de detalle.
    inicializarVolquetas();

    // Inicializa los Select2 del bloque de llegada.
    inicializarVolquetasLlegada();

    // Oculta las casillas hasta seleccionar una actividad.
    configurarColumnasDetalle('');

    // Controla las casillas visibles según el tipo de actividad.
    $("input[name='tipo_actividad']").on('change', function () {
        const tipoActividad = $(this).val();

        configurarColumnasDetalle(tipoActividad);
    });

    // Agrega filas al detalle de aplicación.
    $('#btn_agregar_detalle').on('click', function () {
        agregarFilaDetalle();
    });

    // Elimina filas del detalle de aplicación.
    $('#tbody_detalle_aplicacion').on('click', '.btn-eliminar-detalle', function () {
        eliminarFilaDetalle($(this));
    });

    // Calcula los valores del detalle.
    $('#tbody_detalle_aplicacion').on('input', '.calcular-detalle', function () {
        const fila = $(this).closest('tr');

        calcularFilaDetalle(fila);
    });

    // Agrega filas al control de llegada.
    $('#btn_agregar_llegada').on('click', function () {
        agregarFilaLlegada();
    });

    // Elimina filas del control de llegada.
    $('#tbody_llegada_mezcla').on('click', '.btn-eliminar-llegada', function () {
        eliminarFilaLlegada($(this));
    });

    // Calcula el factor de compactación.
    $('#tbody_llegada_mezcla').on('input', '.calcular-fc', function () {
        const fila = $(this).closest('tr');

        calcularFactorCompactacion(fila);
    });
});

/**
 * Inicializa los Select2 generales de la vista.
 */
function inicializarSelect2() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
    });
}

/**
 * Inicializa el campo de fecha utilizando Daterangepicker
 * en modo de selección de una sola fecha.
 */
function inicializarFecha() {
    $('#fecha_trazabilidad').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        showDropdowns: true,
        locale: {
            format: 'DD-MM-YYYY',
            applyLabel: 'Aceptar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Personalizado',
            weekLabel: 'S',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            monthNames: [
                'Enero',
                'Febrero',
                'Marzo',
                'Abril',
                'Mayo',
                'Junio',
                'Julio',
                'Agosto',
                'Septiembre',
                'Octubre',
                'Noviembre',
                'Diciembre',
            ],
            firstDay: 1,
        },
    });

    // Coloca la fecha seleccionada en el input.
    $('#fecha_trazabilidad').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
    });

    // Limpia el campo si el usuario cancela la selección.
    $('#fecha_trazabilidad').on('cancel.daterangepicker', function () {
        $(this).val('');
    });
}

/**
 * Inicializa todos los Select2 correspondientes a volquetas.
 * La información será obtenida posteriormente desde el controlador.
 */
function inicializarVolquetas() {
    $('.select2-volqueta').each(function () {
        inicializarSelect2Volqueta($(this));
    });
}

/**
 * Configura un Select2 individual para consultar las volquetas
 * mediante AJAX.
 *
 * @param {jQuery} select
 */
function inicializarSelect2Volqueta(select) {
    // Evita inicializar nuevamente un Select2 existente.
    if (select.hasClass('select2-hidden-accessible')) {
        return;
    }

    select.select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione...',
        allowClear: true,

        ajax: {
            url: '../../controller/trazabilidad.php?op=listar_volquetas',
            type: 'POST',
            dataType: 'json',
            delay: 250,

            data: function (params) {
                return {
                    buscar: params.term || '',
                };
            },

            processResults: function (response) {
                return {
                    results: response.data || [],
                };
            },

            cache: true,
        },
    });
}

/**
 * Define qué casillas de la tabla son visibles
 * según la guía de diligenciamiento del CT-F-21.
 *
 * @param {string} tipoActividad
 */
function configurarColumnasDetalle(tipoActividad) {
    let casillasVisibles = [];

    // Aplicación continua:
    // [1], [2], [3], [5], [6], [10], [14], [15], [16].
    if (tipoActividad === 'CONTINUA') {
        casillasVisibles = [1, 2, 3, 5, 6, 10, 14, 15, 16];
    }

    // Bacheo:
    // [1], [2], [4], [5], [6], [7], [8], [9],
    // [10], [11], [12], [13], [14], [15], [16].
    if (tipoActividad === 'BACHEO') {
        casillasVisibles = [1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16];
    }

    // Parcheo:
    // [1], [2], [4], [5], [6], [10], [14], [15], [16].
    if (tipoActividad === 'PARCHEO') {
        casillasVisibles = [1, 2, 4, 5, 6, 10, 14, 15, 16];
    }

    // Oculta inicialmente todas las casillas numeradas.
    $('#tabla_detalle_aplicacion [data-casilla]').hide();

    // Muestra únicamente las casillas correspondientes
    // al tipo de actividad seleccionado.
    casillasVisibles.forEach(function (casilla) {
        $("#tabla_detalle_aplicacion [data-casilla='" + casilla + "']").show();
    });

    // Limpia los campos que no pertenecen al tipo de actividad.
    limpiarCamposOcultos(casillasVisibles);
}

/**
 * Limpia los campos pertenecientes a columnas que actualmente
 * no aplican al tipo de actividad seleccionado.
 *
 * @param {Array} casillasVisibles
 */
function limpiarCamposOcultos(casillasVisibles) {
    $('#tbody_detalle_aplicacion tr').each(function () {
        const fila = $(this);

        fila.find('td[data-casilla]').each(function () {
            const celda = $(this);
            const casilla = parseInt(celda.attr('data-casilla'), 10);

            if (!casillasVisibles.includes(casilla)) {
                celda.find('input').val('');

                celda.find('select').val(null).trigger('change');
            }
        });
    });
}

/**
 * Agrega una nueva fila utilizando como base
 * la primera fila existente de la tabla.
 */
function agregarFilaDetalle() {
    const tbody = $('#tbody_detalle_aplicacion');
    const filaBase = tbody.find('tr.fila-detalle:first');

    if (filaBase.length === 0) {
        return;
    }

    // Clona la fila sin copiar eventos.
    const nuevaFila = filaBase.clone(false, false);

    // Elimina elementos visuales generados previamente por Select2.
    nuevaFila.find('.select2-container').remove();

    // Restablece los Select2 clonados.
    nuevaFila
        .find('.select2-hidden-accessible')
        .removeClass('select2-hidden-accessible')
        .removeAttr('data-select2-id')
        .removeAttr('aria-hidden')
        .removeAttr('tabindex');

    nuevaFila.find('option').removeAttr('data-select2-id');

    // Limpia los valores de todos los controles.
    nuevaFila.find('input').val('');
    nuevaFila.find('select').val('');

    // Agrega la nueva fila al tbody.
    tbody.append(nuevaFila);

    // Actualiza índices y nombres.
    reindexarFilasDetalle();

    // Inicializa nuevamente Select2 únicamente
    // sobre la nueva fila.
    nuevaFila.find('.select2-volqueta').each(function () {
        inicializarSelect2Volqueta($(this));
    });

    // Mantiene las columnas correctas según
    // la actividad actualmente seleccionada.
    const tipoActividad = $("input[name='tipo_actividad']:checked").val() || '';

    configurarColumnasDetalle(tipoActividad);
}

/**
 * Elimina la fila seleccionada.
 *
 * Siempre se conserva como mínimo una fila disponible.
 *
 * @param {jQuery} boton
 */
function eliminarFilaDetalle(boton) {
    const tbody = $('#tbody_detalle_aplicacion');
    const filas = tbody.find('tr.fila-detalle');

    // Si solamente existe una fila, se limpian sus campos
    // en lugar de eliminarla.
    if (filas.length === 1) {
        const fila = filas.first();

        fila.find('input').val('');

        fila.find('select').val(null).trigger('change');

        return;
    }

    // Destruye Select2 antes de eliminar la fila.
    const selectVolqueta = boton.closest('tr').find('.select2-volqueta');

    if (selectVolqueta.hasClass('select2-hidden-accessible')) {
        selectVolqueta.select2('destroy');
    }

    // Elimina la fila.
    boton.closest('tr').remove();

    // Reorganiza los índices del arreglo detalle.
    reindexarFilasDetalle();
}

/**
 * Reorganiza los índices de todas las filas para mantener:
 *
 * detalle[0][campo]
 * detalle[1][campo]
 * detalle[2][campo]
 */
function reindexarFilasDetalle() {
    $('#tbody_detalle_aplicacion tr.fila-detalle').each(function (indice) {
        const fila = $(this);

        fila.attr('data-fila', indice);

        fila.find('[data-campo]').each(function () {
            const campo = $(this).attr('data-campo');

            $(this).attr('name', 'detalle[' + indice + '][' + campo + ']');
        });
    });
}

/**
 * Realiza los cálculos automáticos de cada registro.
 *
 * [11] = Longitud x Ancho x Espesor demolido
 * [12] = Longitud x Ancho x Espesor excavación
 * [13] = Longitud x Ancho x Espesor base
 * [14] = Longitud x Ancho x Número de capas
 *
 * @param {jQuery} fila
 */
function calcularFilaDetalle(fila) {
    const longitud = obtenerNumero(fila.find("[data-campo='longitud']").val());

    const ancho = obtenerNumero(fila.find("[data-campo='ancho']").val());

    const espesorDemolido = obtenerNumero(fila.find("[data-campo='espesor_demolido']").val());

    const espesorExcavacion = obtenerNumero(fila.find("[data-campo='espesor_excavacion']").val());

    const espesorBase = obtenerNumero(fila.find("[data-campo='espesor_base']").val());

    const capasImprimacion = obtenerNumero(fila.find("[data-campo='capas_imprimacion']").val());

    // Calcula [11].
    const volumenDemolido = longitud * ancho * espesorDemolido;

    // Calcula [12].
    const volumenExcavado = longitud * ancho * espesorExcavacion;

    // Calcula [13].
    const volumenBase = longitud * ancho * espesorBase;

    // Calcula [14].
    const imprimacion = longitud * ancho * capasImprimacion;

    fila.find("[data-campo='volumen_demolido']").val(formatearNumero(volumenDemolido));

    fila.find("[data-campo='volumen_excavado']").val(formatearNumero(volumenExcavado));

    fila.find("[data-campo='volumen_base']").val(formatearNumero(volumenBase));

    fila.find("[data-campo='imprimacion']").val(formatearNumero(imprimacion));
}

/**
 * Convierte un valor recibido desde un input
 * en un número válido.
 *
 * @param {*} valor
 * @returns {number}
 */
function obtenerNumero(valor) {
    const numero = parseFloat(valor);

    return isNaN(numero) ? 0 : numero;
}

/**
 * Formatea los cálculos a tres cifras decimales.
 *
 * Cuando el resultado es cero deja el campo vacío
 * para no llenar visualmente la tabla con ceros.
 *
 * @param {number} valor
 * @returns {string}
 */
function formatearNumero(valor) {
    if (!valor || valor === 0) {
        return '';
    }

    return valor.toFixed(3);
}

/**
 * Inicializa los Select2 correspondientes
 * a las volquetas del bloque de llegada.
 */
function inicializarVolquetasLlegada() {
    $('.select2-volqueta-llegada').each(function () {
        inicializarSelect2VolquetaLlegada($(this));
    });
}

/**
 * Inicializa un Select2 individual para seleccionar
 * la volqueta asociada al registro de llegada.
 *
 * @param {jQuery} select
 */
function inicializarSelect2VolquetaLlegada(select) {
    // Evita inicializar nuevamente el mismo Select2.
    if (select.hasClass('select2-hidden-accessible')) {
        return;
    }

    select.select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione...',
        allowClear: true,

        ajax: {
            url: '../../controller/trazabilidad.php?op=listar_volquetas',
            type: 'POST',
            dataType: 'json',
            delay: 250,

            data: function (params) {
                return {
                    buscar: params.term || '',
                };
            },

            processResults: function (response) {
                return {
                    results: response.data || [],
                };
            },

            cache: true,
        },
    });
}

/**
 * Agrega una nueva fila al control
 * de llegada de mezcla.
 */
function agregarFilaLlegada() {
    const tbody = $('#tbody_llegada_mezcla');
    const filaBase = tbody.find('tr.fila-llegada:first');

    if (filaBase.length === 0) {
        return;
    }

    // Clona la primera fila sin copiar eventos.
    const nuevaFila = filaBase.clone(false, false);

    // Elimina los elementos visuales creados por Select2.
    nuevaFila.find('.select2-container').remove();

    // Restablece el select clonado.
    nuevaFila
        .find('.select2-hidden-accessible')
        .removeClass('select2-hidden-accessible')
        .removeAttr('data-select2-id')
        .removeAttr('aria-hidden')
        .removeAttr('tabindex');

    nuevaFila.find('option').removeAttr('data-select2-id');

    // Limpia los campos de la nueva fila.
    nuevaFila.find('input').val('');
    nuevaFila.find('select').val('');

    // Agrega la fila a la tabla.
    tbody.append(nuevaFila);

    // Reorganiza los índices del arreglo llegada.
    reindexarFilasLlegada();

    // Inicializa Select2 únicamente en la nueva fila.
    nuevaFila.find('.select2-volqueta-llegada').each(function () {
        inicializarSelect2VolquetaLlegada($(this));
    });
}

/**
 * Elimina un registro del control de llegada.
 *
 * Se conserva siempre al menos una fila.
 *
 * @param {jQuery} boton
 */
function eliminarFilaLlegada(boton) {

    const tbody = $("#tbody_llegada_mezcla");
    const filas = tbody.find("tr.fila-llegada");

    // Si existe una sola fila, se limpia en lugar de eliminarla.
    if (filas.length === 1) {

        const fila = filas.first();

        fila.find("input").val("");

        fila
            .find("select")
            .val(null)
            .trigger("change");

        return;
    }

    const fila = boton.closest("tr");

    // Destruye Select2 antes de eliminar la fila.
    const selectVolqueta = fila.find(
        ".select2-volqueta-llegada"
    );

    if (
        selectVolqueta.hasClass(
            "select2-hidden-accessible"
        )
    ) {
        selectVolqueta.select2("destroy");
    }

    // Elimina el registro.
    fila.remove();

    // Reorganiza los índices restantes.
    reindexarFilasLlegada();

}

/**
 * Reorganiza los índices de los registros
 * correspondientes al control de llegada.
 *
 * llegada[0][campo]
 * llegada[1][campo]
 * llegada[2][campo]
 */
function reindexarFilasLlegada() {

    $("#tbody_llegada_mezcla tr.fila-llegada")
        .each(function (indice) {

            const fila = $(this);

            fila.attr("data-fila", indice);

            fila
                .find("[data-campo]")
                .each(function () {

                    const campo = $(this)
                        .attr("data-campo");

                    $(this).attr(
                        "name",
                        "llegada[" +
                        indice +
                        "][" +
                        campo +
                        "]"
                    );

                });

        });

}

/**
 * Calcula el factor de compactación del registro.
 *
 * [19] = [17] / [18]
 *
 * FC = Volumen de llegada / Volumen aplicado
 *
 * @param {jQuery} fila
 */
function calcularFactorCompactacion(fila) {

    const volumenLlegada = obtenerNumero(
        fila
            .find("[data-campo='volumen_llegada']")
            .val()
    );

    const volumenAplicado = obtenerNumero(
        fila
            .find("[data-campo='volumen_aplicado']")
            .val()
    );

    const campoFactor = fila.find(
        "[data-campo='factor_compactacion']"
    );

    // Evita divisiones entre cero.
    if (
        volumenLlegada <= 0 ||
        volumenAplicado <= 0
    ) {
        campoFactor.val("");
        return;
    }

    const factorCompactacion =
        volumenLlegada / volumenAplicado;

    campoFactor.val(
        factorCompactacion.toFixed(3)
    );

}

// Cancela el diligenciamiento y limpia el formulario.
$("#btn_cancelar").on("click", function () {
    cancelarFormulario();
});

// Valida la información mínima requerida para guardar como borrador.
$("#btn_guardar_borrador").on("click", function () {

    if (!validarBorrador()) {
        return;
    }

    Swal.fire({
        icon: "info",
        title: "Borrador listo",
        text: "La información cumple con los datos mínimos para ser guardada.",
        confirmButtonText: "Aceptar"
    });

    // El AJAX de guardado se implementará posteriormente.
});

// Valida completamente el formato antes de enviarlo a aprobación.
$("#btn_enviar_aprobacion").on("click", function () {

    if (!validarEnvioAprobacion()) {
        return;
    }

    Swal.fire({
        title: "¿Enviar formato?",
        text: "El formato será enviado para aprobación.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, enviar",
        cancelButtonText: "Cancelar"
    }).then(function (result) {

        if (result.isConfirmed) {

            // El AJAX de envío se implementará posteriormente.
            Swal.fire({
                icon: "success",
                title: "Validación correcta",
                text: "El formato está listo para ser enviado.",
                confirmButtonText: "Aceptar"
            });

        }

    });

});

/**
 * Valida los datos mínimos necesarios
 * para guardar el formato como borrador.
 *
 * @returns {boolean}
 */
function validarBorrador() {

    const tipoMezcla = $("#tipo_mezcla").val();
    const fecha = $("#fecha_trazabilidad").val();
    const tipoActividad = $(
        "input[name='tipo_actividad']:checked"
    ).val();

    if (!tipoMezcla) {

        mostrarValidacion(
            "Tipo de mezcla",
            "Seleccione el tipo de mezcla."
        );

        $("#tipo_mezcla").select2("open");

        return false;
    }

    if (!fecha) {

        mostrarValidacion(
            "Fecha",
            "Seleccione la fecha del formato."
        );

        $("#fecha_trazabilidad").focus();

        return false;
    }

    if (!tipoActividad) {

        mostrarValidacion(
            "Tipo de actividad",
            "Seleccione Aplicación continua, Bacheo o Parcheo."
        );

        return false;
    }

    return true;
}

/**
 * Valida toda la información requerida antes
 * de enviar el formato para aprobación.
 *
 * @returns {boolean}
 */
function validarEnvioAprobacion() {

    // Primero valida los campos generales.
    if (!validarBorrador()) {
        return false;
    }

    const obra = $("#obra_id").val();

    if (!obra) {

        mostrarValidacion(
            "Ubicación / Obra",
            "Seleccione la obra donde se realizó la aplicación."
        );

        $("#obra_id").select2("open");

        return false;
    }

    // Valida cada registro del detalle de aplicación.
    if (!validarDetalleAplicacion()) {
        return false;
    }

    // Valida cada registro del control de llegada.
    if (!validarLlegadaMezcla()) {
        return false;
    }

    return true;
}

/**
 * Valida los registros de la tabla Detalle de aplicación.
 *
 * Solo se revisan los campos que pertenecen
 * al tipo de actividad actualmente seleccionado.
 *
 * @returns {boolean}
 */
function validarDetalleAplicacion() {

    const filas = $(
        "#tbody_detalle_aplicacion tr.fila-detalle"
    );

    if (filas.length === 0) {

        mostrarValidacion(
            "Detalle de aplicación",
            "Debe registrar por lo menos un detalle de aplicación."
        );

        return false;
    }

    let formularioValido = true;

    filas.each(function (indice) {

        if (!formularioValido) {
            return;
        }

        const fila = $(this);

        // Solo revisa controles ubicados en columnas visibles.
        fila.find("td[data-casilla]:visible").each(function () {

            if (!formularioValido) {
                return;
            }

            const celda = $(this);

            celda.find(
                "input:not([readonly]), select"
            ).each(function () {

                const campo = $(this);

                if (!campo.val()) {

                    const casilla = celda.data("casilla");

                    mostrarValidacion(
                        "Detalle de aplicación",
                        "Complete la casilla [" +
                        casilla +
                        "] del registro " +
                        (indice + 1) +
                        "."
                    );

                    enfocarCampo(campo);

                    formularioValido = false;
                    return false;
                }

            });

        });

    });

    return formularioValido;
}

/**
 * Valida los registros del control
 * de llegada de mezcla.
 *
 * @returns {boolean}
 */
function validarLlegadaMezcla() {

    const filas = $(
        "#tbody_llegada_mezcla tr.fila-llegada"
    );

    if (filas.length === 0) {

        mostrarValidacion(
            "Control de llegada",
            "Debe registrar por lo menos una llegada de mezcla."
        );

        return false;
    }

    let formularioValido = true;

    filas.each(function (indice) {

        if (!formularioValido) {
            return;
        }

        const fila = $(this);

        const placa = fila.find(
            "[data-campo='placa_volqueta']"
        );

        const temperatura = fila.find(
            "[data-campo='temperatura_llegada']"
        );

        const volumenLlegada = fila.find(
            "[data-campo='volumen_llegada']"
        );

        const volumenAplicado = fila.find(
            "[data-campo='volumen_aplicado']"
        );

        if (!placa.val()) {

            mostrarValidacion(
                "Control de llegada",
                "Seleccione la volqueta del registro " +
                (indice + 1) +
                "."
            );

            placa.select2("open");

            formularioValido = false;
            return false;
        }

        if (!temperatura.val()) {

            mostrarValidacion(
                "Control de llegada",
                "Ingrese la temperatura de llegada del registro " +
                (indice + 1) +
                "."
            );

            temperatura.focus();

            formularioValido = false;
            return false;
        }

        if (!volumenLlegada.val()) {

            mostrarValidacion(
                "Control de llegada",
                "Ingrese el volumen de llegada del registro " +
                (indice + 1) +
                "."
            );

            volumenLlegada.focus();

            formularioValido = false;
            return false;
        }

        if (!volumenAplicado.val()) {

            mostrarValidacion(
                "Control de llegada",
                "Ingrese el volumen aplicado del registro " +
                (indice + 1) +
                "."
            );

            volumenAplicado.focus();

            formularioValido = false;
            return false;
        }

    });

    return formularioValido;
}

/**
 * Muestra una alerta de validación utilizando SweetAlert2.
 *
 * @param {string} titulo
 * @param {string} mensaje
 */
function mostrarValidacion(titulo, mensaje) {

    Swal.fire({
        icon: "warning",
        title: titulo,
        text: mensaje,
        confirmButtonText: "Aceptar"
    });

}

/**
 * Lleva el foco al control que presenta
 * un error de validación.
 *
 * @param {jQuery} campo
 */
function enfocarCampo(campo) {

    if (campo.is("select")) {

        campo.select2("open");
        return;
    }

    campo.focus();

}

/**
 * Solicita confirmación antes de cancelar
 * el diligenciamiento del formato.
 */

function cancelarFormulario() {

    const trazabilidadId = $("#trazabilidad_id").val();

    // Si existe ID, el formulario corresponde a un borrador guardado.
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
                window.location.href = "inboxTrazabilidad.php";
            }

        });

        return;
    }

    // Si no existe ID, se trata de un formulario nuevo.
    Swal.fire({
        title: "¿Cancelar diligenciamiento?",
        text: "La información que no haya sido guardada se perderá.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "Continuar diligenciando"
    }).then(function (result) {

        if (result.isConfirmed) {

            limpiarFormulario();

            window.location.href = "inboxTrazabilidad.php";
        }

    });

}

/**
 * Restablece el formulario a su estado inicial.
 */
function limpiarFormulario() {

    const formulario = $("#form_trazabilidad")[0];

    if (formulario) {
        formulario.reset();
    }

    // Limpia los Select2 generales.
    $("#tipo_mezcla")
        .val(null)
        .trigger("change");

    $("#obra_id")
        .val(null)
        .trigger("change");

    // Conserva solamente la primera fila del detalle.
    $("#tbody_detalle_aplicacion tr.fila-detalle")
        .not(":first")
        .remove();

    // Limpia la primera fila del detalle.
    const primeraFilaDetalle = $(
        "#tbody_detalle_aplicacion tr.fila-detalle:first"
    );

    primeraFilaDetalle
        .find("input")
        .val("");

    primeraFilaDetalle
        .find("select")
        .val(null)
        .trigger("change");

    // Conserva solamente la primera fila de llegada.
    $("#tbody_llegada_mezcla tr.fila-llegada")
        .not(":first")
        .remove();

    // Limpia la primera fila de llegada.
    const primeraFilaLlegada = $(
        "#tbody_llegada_mezcla tr.fila-llegada:first"
    );

    primeraFilaLlegada
        .find("input")
        .val("");

    primeraFilaLlegada
        .find("select")
        .val(null)
        .trigger("change");

    // Limpia observaciones.
    $("#observaciones").val("");

    // Vuelve a ocultar las columnas hasta
    // seleccionar un tipo de actividad.
    configurarColumnasDetalle("");

    // Reorganiza nuevamente los índices.
    reindexarFilasDetalle();
    reindexarFilasLlegada();

}

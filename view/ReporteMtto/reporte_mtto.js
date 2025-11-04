function init() {

    $("#rpteMtto").off("submit").on("submit", function (e) {
        e.preventDefault();

        console.log("Formulario enviado, capturando datos...");
        //Capturo los datos del fomulario

        const datos = capturarDatos();

        console.log("Datos Capturados en el init", datos);

        guardarDatos(datos);

        setTimeout(() => {
            $("#btnGuardar").prop("disabled", false); // Reactivar después de 2 segundos
        }, 2000);

    });

}

$('.select2').select2()


$.post("../../controller/Obras.php?op=comboObras", function (data) {
    $('#nomb_obra').html(data);
});

$.post("../../controller/Usuario.php?op=comboUsuarioReportes", function (data) {
    $('#nomb_cond').html(data);
});

$.post("../../controller/ReporteMtto.php?op=comboTipoMtto", function (data) {
    $('#tipo_mtto').html(data);
});

$.post(
    "../../controller/Vehiculo.php?op=comboVehiculoPreop",
    function (data, status) {
        $("#nomb_vehi").html(data);
    }
);

$.post(
    "../../controller/ReporteMtto.php?op=numeroReporte",
    function (data, status) {
        //console.log("Respuesta del servidor:", data);
        $("#numb_reporte").val(data);
    }
);

$('.summernote').summernote({ height: 300 });



$(document).ready(function () {

    // Inicializar DataTables
    var table = $('#editableTable').DataTable({
        "paging": true,   // Habilita la paginación
        "pageLength": 5,  // 5 filas por página
        "lengthChange": false, // Oculta el selector de cantidad de filas
        "searching": false, // Desactiva la búsqueda (opcional)
        "info": false, // Oculta la información de paginación
        "ordering": false, // Desactiva la ordenación de columnas
        "language": { "paginate": { "next": "Siguiente", "previous": "Anterior" } }
    });

    // Agregar fila nueva insumo o repuesto
    $('#addRow').click(function () {

        var newRow = `<tr>
            <td><input type="text" name="insumo_nombre[]" class="form-control" placeholder="Nombre Repuesto"></td>
            <td><input type="text" name="insumo_refe[]" class="form-control" placeholder="Ref."></td>
            <td><input type="text" name="insumo_marca[]" class="form-control" placeholder="Marca Repuesto"></td>
            <td><input type="text" name="insumo_modulo[]" class="form-control" placeholder="Modelo Repuesto"></td>
            <td><input type="text" name="insumo_serial[]" class="form-control" placeholder="Serial"></td>
            <td class="cantidad"><input type="text" name="insumo_cantidad[]" class="form-control cantidad" placeholder="Cantidad"></td> <!-- Columna cantidad -->
            <td class="costo"><input type="text" name="insumo_costo[]" class="form-control costo" placeholder="Costo"></td> <!-- Columna costo -->
            <td><input type="text" name="insumo_orden_compra[]" class="form-control" placeholder="Orden Compra"></td>
            <td><input type="text" name="insumo_factura[]" class="form-control" placeholder="Factura Prov."></td>                       
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
        $('#editableTable tbody').append(newRow);
        calcularTotal(); // Calcular el total después de agregar una fila
    });

    // Eliminar fila
    $(document).on('click', '.btn-delete', function () {
        $(this).closest('tr').remove();
        calcularTotal(); // Recalcular el total después de eliminar una fila

    });

    // Calcular el total de los costos
    // Calcular el total de los costos
    function calcularTotal() {
        var total = 0;

        // Recorrer todas las filas de la tabla
        $('#editableTable tbody tr').each(function () {
            // Obtener los valores de cantidad y costo
            var cantidad = parseFloat($(this).find('input[name="insumo_cantidad[]"]').val()) || 0; // Si no es un número, asignar 0
            var costo = parseFloat($(this).find('input[name="insumo_costo[]"]').val()) || 0; // Si no es un número, asignar 0

            // Sumar la multiplicación de cantidad por costo
            total += cantidad * costo;
        });

        // Guardar el total sin formato en un campo oculto para enviar al backend
        $('#total_real').val(total.toFixed(2));

        // Formatear el total con separadores de miles
        var totalFormatted = total.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Actualizar el campo de texto con el total
        $('#total').val(totalFormatted); // Mostrar el total con 2 decimales y separadores de miles
    }

    // Detectar cambios en las celdas de cantidad o costo para recalcular el total
    $(document).on('input', 'input[name="insumo_cantidad[]"], input[name="insumo_costo[]"]', function () {
        calcularTotal(); // Recalcular el total cuando se modifique la cantidad o el costo
    });



});

$(document).ready(function () {
    // Inicializar DataTables
    var table = $('#editableTableProv').DataTable({
        "paging": true,   // Habilita la paginación
        "pageLength": 5,  // 5 filas por página
        "lengthChange": false, // Oculta el selector de cantidad de filas
        "searching": false, // Desactiva la búsqueda (opcional)
        "info": false, // Oculta la información de paginación
        "ordering": false, // Desactiva la ordenación de columnas
        "language": { "paginate": { "next": "Siguiente", "previous": "Anterior" } }
    });

    // Agregar fila nueva
    $('#addRow2').click(function () {

        var newRow = `<tr>
            <td ><input type="text" name="prov_nomb_externo[]" class="form-control" placeholder="Nombre proveedor"></td>
            <td ><input type="text" name="prov_orde_trab_ext[]" class="form-control" placeholder="Orden de Trabajo"></td>
            <td ><input type="text" name="prov_orde_comp_ext[]" class="form-control" placeholder="Orden de Compra"></td>
            <td ><input type="text" name="prov_fact_ext[]" class="form-control" placeholder="Factura proveedor"></td>                 
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
        $('#editableTableProv tbody').append(newRow);
    });

    // Eliminar fila
    $(document).on('click', '.btn-delete', function () {
        $(this).closest('tr').remove();

    });

});

function capturarDatos() {

    console.log("Capturando datos...");
    //Captura los datos del formulario pricipal
    const formData = new FormData(document.getElementById("rpteMtto"));

    //Captura tipoProveedor
    const tipoProveedor = document.querySelector('input[name="proveedor"]:checked').value;

    const deta_diag_inici = $('#summernote_inicial').summernote('code');

    const deta_desc_mtto = $('#summernote_descripcion').summernote('code');

    //Capturar los proveedores internos
    const proveedoresInternos = [];

    if (tipoProveedor === "interno") {
        proveedoresInternos.push({
            prov_nomb: document.querySelector('input[name="nombre_interno"]').value,
            prov_carg: document.querySelector('input[name="carg_interno"]').value,
            prov_orden: document.querySelector('input[name="orden_interno"]').value,
            tipo: "interno",
        });
    }

    //Capturar los proveedores externos
    const proveedoresExterno = [];

    if (tipoProveedor === "externo") {
        document.querySelectorAll("#editableTableProv tbody tr").forEach((fila) => {
            let inputNombre = fila.querySelector('input[name="prov_nomb_externo[]"]');
            let inputOrdenTrab = fila.querySelector('input[name="prov_orde_trab_ext[]"]');
            let inputOrdenComp = fila.querySelector('input[name="prov_orde_comp_ext[]"]');
            let inputFact = fila.querySelector('input[name="prov_fact_ext[]"]');
            proveedoresExterno.push({
                prov_nomb: inputNombre ? inputNombre.value : "",
                prov_orde_trab: inputOrdenTrab ? inputOrdenTrab.value : "",
                prov_ord_comp: inputOrdenComp ? inputOrdenComp.value : "",
                prov_fact: inputFact ? inputFact.value : "",
                tipo: "externo",
            });
        });

    }

    const insumos = [];

    document.querySelectorAll("#editableTable tbody tr").forEach((fila) => {

        if (!fila.querySelector('input')) {
            console.warn("Fila ignorada porque no contiene inputs:", fila);
            return;
        }

        let nombre = fila.querySelector('input[name="insumo_nombre[]"]');
        let referencia = fila.querySelector('input[name="insumo_refe[]"]');
        let marca = fila.querySelector('input[name="insumo_marca[]"]');
        let modelo = fila.querySelector('input[name="insumo_modulo[]"]');
        let serial = fila.querySelector('input[name="insumo_serial[]"]');
        let cantidad = fila.querySelector('input[name="insumo_cantidad[]"]');
        let costo = fila.querySelector('input[name="insumo_costo[]"]');
        let orden_compra = fila.querySelector('input[name="insumo_orden_compra[]"]');
        let factura = fila.querySelector('input[name="insumo_factura[]"]');

        // Verifica que los inputs existen antes de acceder a su valor
        if (nombre && referencia && marca && modelo && serial && cantidad && costo && orden_compra && factura) {
            insumos.push({
                insumo_nombre: nombre.value,
                insumo_referencia: referencia.value,
                insumo_marca: marca.value,
                insumo_modelo: modelo.value,
                insumo_serial: serial.value,
                insumo_cantidad: cantidad.value,
                insumo_costo: costo.value,
                insumo_orden_compra: orden_compra.value,
                insumo_factura: factura.value,
            });
        } else {
            console.error("Algunos inputs no fueron encontrados en la fila:", fila);
        }
    });

    //Convertir el FormData a un objeto
    const datosFormulario = {};
    formData.forEach((value, key) => {
        datosFormulario[key] = value;
    });

    const reporte ={
        numb_reporte: datosFormulario.numb_reporte || "",
        fecha_reporte: datosFormulario.fecha_reporte || "",
        nomb_vehi: datosFormulario.nomb_vehi || "",
        nomb_obra: datosFormulario.nomb_obra || "",
        nomb_cond: datosFormulario.nomb_cond || "",
        codig_equipo: datosFormulario.codig_equipo || "",
        hora_reporte: datosFormulario.hora_reporte || "",
        tipo_mtto: datosFormulario.tipo_mtto || "",
    };

    //Combina todos los datos en un solo objeto

    const datosCompletos = {
        reporte,
        detalle: {
            deta_diag_inici: deta_diag_inici,
            deta_desc_mtto: deta_desc_mtto,
            deta_esta_fina: datosFormulario.deta_esta_fina,
            deta_tipo_prov: datosFormulario.proveedor,
            deta_total_mtto: datosFormulario.total_real,
        },
        proveedores: [...proveedoresInternos, ...proveedoresExterno],
        insumos,

    }

    //console.log("Datos capturados:", datosCompletos);

    return datosCompletos;

}

function guardarDatos(datos) {
    console.log("Datos recibidos en guardarDatos:", datos);

    // Evita doble envío
    if ($("#btnGuardar").prop("disabled")) {
        console.log("La solicitud ya está en proceso...");
        return;
    }

    $("#btnGuardar").prop("disabled", true); // Deshabilita el botón mientras se envía

    $.ajax({
        url: "../../controller/ReporteMtto.php?op=guardar",
        type: "POST",
        data: JSON.stringify(datos),
        contentType: "application/json",
        processData: false,

        success: function (response) {
            console.log("Respuesta del servidor:", response);

            let data = typeof response === "string" ? JSON.parse(response) : response;

            if (response.status === "error") {
                swal({
                    title: "Error",
                    text: data.message,
                    icon: "error",
                    confirmButtonClass: "btn-danger",
                });
            } else {
                Swal.fire({
                    title: "Correcto",
                    text: "Reporte No. " +data.repo_numb+ " ha sido guardo exitosamente",
                    icon: "success",
                    confirmButtonText: "OK",
                }).then(() => {
                    window.location.href = window.location.href; // Recarga/redirige a la misma página
                });
                

            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
            swal({
                title: "Error",
                text: "Hubo un problema al enviar los datos. Por favor, inténtalo de nuevo.",
                icon: "error",
                confirmButtonClass: "btn-danger",
            });
        },
        complete: function () {
            $("#btnGuardar").prop("disabled", false); // Rehabilita el botón después de completar la solicitud
        }
    });
}

$(document).ready(function () {

    // Mostrar las secciones basadas en la selección de proveedor
    document.getElementById('interno').addEventListener('change', function () {
        if (this.checked) {
            document.getElementById('internoFields').style.display = 'block';
            document.getElementById('externoFields').style.display = 'none';
        }
    });

    document.getElementById('externo').addEventListener('change', function () {
        if (this.checked) {
            document.getElementById('externoFields').style.display = 'block';
            document.getElementById('internoFields').style.display = 'none';
        }
    });
    // Establecer el valor por defecto de "interno"
    document.getElementById('interno').checked = true;
    document.getElementById('internoFields').style.display = 'block';
});
$(document).ready(function () {
    document.getElementById('card2').style.display = 'none';
    document.getElementById('card3').style.display = 'none';
    document.getElementById('cardFooter').style.display = 'none';
});

//Boton siguiente 1 de la pagina encabezado
$(document).on("click", "#btnSiguiente1", function () {
    let camposInvalidos = $("#card1").find("input[required], select[required]").filter(function () {
        return !$(this).val() || $(this).val() === "";
    });
    // Quitar clases anteriores para que no se acumulen
    $("#card1").find("input, select").removeClass("is-invalid");
    $(".select2-selection").removeClass("is-invalid");

    if (camposInvalidos.length > 0) {
        camposInvalidos.each(function () {
            $(this).addClass("is-invalid");

            // Si es select2, aplicar clase al contenedor visual también
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).next('.select2-container').find('.select2-selection').addClass('is-invalid');
            }
        });

        Swal.fire({
            icon: "warning",
            title: "Campos incompletos",
            text: "Por favor, completa todos los campos requeridos (*) antes de continuar.",
        });

        return;
    }
    document.getElementById('card2').style.display = 'block';
    document.getElementById('card1').style.display = 'none';
});

$(document).on("click", "#btnSiguiente2", function () {

    let valido = true;

    // Recorre todos los summernote dentro de card2
    $("#card2 .summernote").each(function () {
        const contenido = $(this).summernote("code").trim();
        const editor = $(this).next(".note-editor");

        if (contenido === "" || contenido === "<p><br></p>") {
            valido = false;
            editor.addClass("is-invalid");
        } else {
            editor.removeClass("is-invalid");
        }
    });

    if (!valido) {
        Swal.fire({
            icon: "warning",
            title: "Campos requeridos",
            text: "Por favor completa todos los campos de texto con el (*) antes de continuar.",
        });
        return; // no avanzar
    }

    document.getElementById('card3').style.display = 'block';
    document.getElementById('cardFooter').style.display = 'block';
    document.getElementById('card2').style.display = 'none';
});

$(document).on("click", "#btnAnterior2", function () {
    document.getElementById('card2').style.display = 'none';
    document.getElementById('card1').style.display = 'block';
});

$(document).on("click", "#btnAnterior3", function () {
    document.getElementById('card3').style.display = 'none';
    document.getElementById('cardFooter').style.display = 'none';
    document.getElementById('card2').style.display = 'block';
});

$('#reservationdate').datetimepicker({
    format: 'YYYY-MM-DD'
});



init();
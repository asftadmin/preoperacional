function init() {
    $("#formulario_preop").on("submit", function (e) {
        if (!validarFormulario()||!validarKilometraje()) {
            e.preventDefault(); // Evitar el envío del formulario si no se ha seleccionado ninguna opción
        } else {
            guardar(e);
        }
    });

    $("#formulario_fallas").on("submit", function(e){
        guardarForm(e);
    });
}
$('.select2bs4').select2({
    theme: 'bootstrap4'
});
function guardarForm(e) {
    e.preventDefault();
    let formData = new FormData($("#formulario_fallas")[0]);
    formData.append('opcion', 'guardar_respuestas_form');
    swal({
        title: "Confirmar Datos",
        text: "¿Deseas guardar Cambios?",
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-primary",
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,   
    },
    function (isConfirm) {
        if (isConfirm) {
    $.ajax({
        url: "../../controller/FormularioFallas.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            var data = JSON.parse(datos);
            console.log(data.status);
            if (data.status.trim().toLowerCase() == "errores") {
                swal({
                    title: "Error",
                    text: data.message,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                });
                document.getElementById("formulario_fallas").reset();
            } else {
                swal({
                    title: "Correcto",
                    text: data.message,
                    type: "success",
                    confirmButtonClass: "btn-success",
       
                });
                document.getElementById("formulario_fallas").reset();
            }
        }
        });
    }
      
    });
}
function showPlate() {
    var carPlate = document.getElementById("vehi_placa").value;
    var carPlate2 = document.getElementById("vehi_id").value;
    document.getElementById("Placa").value = carPlate;
    document.getElementById("form_vehiculo").value = carPlate2;
}
 
function guardar(e) {
    e.preventDefault();
    let formData = new FormData($("#formulario_preop")[0]);
    formData.append('opcion', 'guardar_respuestas');

    swal({
        title: "Envío de Formulario",
        text: "¿Estás seguro de enviar el Formulario?",
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-primary",
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: "../../controller/Preoperacional.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,

                success: function (datos) {
                    var data = JSON.parse(datos);
                    console.log(data.status);
                    if (data.status.trim().toLowerCase() == "errores") {
                        swal({
                            title: "Error",
                            text: data.message,
                            type: "error",
                            confirmButtonClass: "btn-danger",
                        
                        });
                        document.getElementById("formulario_preop").reset();
                    } else {
                        swal({
                            title: "Correcto",
                            text: data.message,
                            type: "success",
                            confirmButtonClass: "btn-success",
               
                        });
                        document.getElementById("formulario_preop").reset();
                    }
                }
            });
        }
    });
}


$(document).ready(function () {
    
    // Cargar las preguntas mediante AJAX al cargar la página
    $.ajax({
        url: "../../controller/FormularioFallas.php",
        method: "POST",
        data: { opcion: 'listarPreguntas', action: 'cargar_preguntas' },
        success: function (data) {
            $('#form_fallas').html(data);
        }
    });
});

$.post("../../controller/Vehiculo.php?op=comboVehiculoPreop",function(data, status){
    $('#select_placa').html(data);
});

$.post("../../controller/Vehiculo.php?op=comboVehiculoPreop",function(data, status){
    $('#select_form').html(data);
});


//PREOPERACIONAL
$(document).ready(function() {
    // Cuando cambia la selección del vehículo
    $('#select_placa').on('change', function() {
        var tipo_id = $(this).find('option:selected').data('tipo-id');
        var vehi_placa = $(this).find('option:selected').data('vehi-placa');
        var vehi_id = $(this).find('option:selected').data('vehi-id');
        $('#tipo_id').val(tipo_id);
        $('#vehi_placa').val(vehi_placa);
        $('#vehi_id').val(vehi_id);
        cargarPreguntas(tipo_id);
    });
});

function cargarPreguntas(tipo_id){
   // Cargar las preguntas mediante AJAX al cargar la página
   $.ajax({
    url: "../../controller/Preoperacional.php",
    method: "POST",
    data: { opcion: 'listarPreguntas', action: 'cargar_preguntas', tipo_id: tipo_id },
    success: function (data) {
        $('#pregunta-container').html(data);
    }
}); 
}

function validarFormulario() {
    const preguntas = document.querySelectorAll(".opciones");

    for (const pregunta of preguntas) {
        const opcionesSeleccionadas = pregunta.querySelectorAll(".opcion-radio:checked");
        
        if (opcionesSeleccionadas.length === 0) {
            alert("Por favor, seleccione una opción (B, M o N/A) para cada pregunta.");
            return false; // Evitar el envío del formulario si falta una respuesta
        }
    }

    return true; // Permitir el envío del formulario si todas las preguntas tienen respuestas
}
function validarKilometraje() {
    const inicial = document.getElementById("pre_kilometraje_inicial").value;
    
    if (!isValidNumber(inicial)) {
        alert("Por favor, ingrese números válidos para Kilometraje Inicial ");
        return false; // Evitar el envío del formulario si los valores no son números válidos
    }
    if ((inicial.length >= 4  && inicial.length == 0)) {
        alert("Por favor, ingrese de minimo 4 números en Kilometraje Inicial ");
        return false; // Evitar el envío del formulario si no cumple con la condición
    }
    
    return true; // Permitir el envío del formulario si los valores son números válidos
}
// Función para validar si es un número válido
function isValidNumber(value) {
    return !isNaN(value) && isFinite(value);
}




init();

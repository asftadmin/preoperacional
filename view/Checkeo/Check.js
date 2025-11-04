function init() {
    $("#formulario_check").on("submit", function (e) {
        if (!validarFormulario()) {
            e.preventDefault(); // Evitar el envío del formulario si no se ha seleccionado ninguna opción
        } else {
            guardar(e);
        }
    });
}
$('.select2bs4').select2({
    theme: 'bootstrap4'
});


function guardar(e) {
    e.preventDefault();
    let formData = new FormData($("#formulario_check")[0]);
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
                        document.getElementById("formulario_check").reset();
                    } else {
                        swal({
                            title: "Correcto",
                            text: data.message,
                            type: "success",
                            confirmButtonClass: "btn-success",
               
                        });
                        document.getElementById("formulario_check").reset();
                    }
                }
            });
        }
    });
}

$.post("../../controller/Vehiculo.php?op=comboEquiposLab",function(data, status){
    $('#select_placa').html(data);
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
    data: { opcion: 'listarCheckeo', action: 'cargar_check', tipo_id: tipo_id },
    success: function (data) {
        $('#check-container').html(data);
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

init();

function init() {
    // Inicialización si es necesario
    $("#rpte_form").on("submit", function(e){
        guardar(e);
    });
}

$('.select2').select2()

//Adquirir datos para el detalle ticket
$(document).ready(function() {
    var ticket_id = getURLParameter('id');
    
    if (!ticket_id) {
        alert('No se encontró el ID del ticket en la URL');
        return;
    }

    $.ajax({
        url: "../../controller/Tickets.php?op=detalleTicket",
        method: "POST",
        data: {id: ticket_id},
        dataType: "json", // Esperamos una respuesta JSON
        success: function(data) {
            console.log("Respuesta completa:", data);
            
            if (data.status === 'success') {
                $('#readMessage').html(data.html);
            } else {
                alert(data.message || 'Error desconocido');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            alert("Error al cargar los detalles del ticket");
        }
    });

});

//Post para crear reporte de mantenimiento

$(document).ready(function() {
    $('#btnCrearReporte').on('click', function() {
        let solicitud = $('#ticket_id').val(); 
        $('#mdltitulo').html('No. Solicitud: '+ solicitud);
        $('#codi_vehi').val();      // Llenar el campo codi_vehi
        $('#codi_cond').val();      // Llenar el campo codi_cond
        $('#diag_rpte').val();      // Llenar el campo diag_rpte
        $('#modalRerporteMtto').modal('show');  // Abrir el modal
    });

});

function guardar(e){
     e.preventDefault();
     let num_reporte = $('#numb_reporte').val();
     let horas_prog = $('#hora_reporte').val();
     let fecha_asignacion = $('#fecha_reporte').val();
     let obra = $('#nomb_obra').val();
     let mantenimiento = $('#tipo_mtto').val();

     let vehiculo = $('#codi_vehi').val();
     let conductor = $('#codi_cond').val();
     let diagnostico_inicial = $('#diag_rpte').val();
     let ticket_id = $('#ticket_id').val();

     let data = {
        num_reporte: num_reporte,
        horas_prog: horas_prog,
        fecha_asignacion: fecha_asignacion,
        obra: obra,
        mantenimiento : mantenimiento,
        vehiculo : vehiculo,
        conductor : conductor,
        diagnostico_inicial : diagnostico_inicial,
        ticket_id : ticket_id
     };

     console.log(data);

     $.ajax({

        url: '../../controller/ReporteMtto.php?op=guardarReporte',
        type: 'POST',
        data: data,
        success: function(response) {
            console.log(response);
                const result = JSON.parse(response);  // Asumimos que la respuesta es JSON

                // Mostrar mensaje de éxito o error con Swal
                if (result.status === 'success') {
                    Swal.fire({
                        title: '¡Guardado Exitosamente!',
                        text: 'El reporte fue creado correctamente.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });

                    // Cerrar el modal
                    $('#modalRerporteMtto').modal('hide');
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: result.message,
                        icon: 'error',
                        confirmButtonText: 'Intentar de nuevo'
                    });
                }

        },
        error: function() {
            // En caso de error en la solicitud AJAX
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al crear el reporte.',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo'
            });
        },

    });

}

//Boton Regresar a la bandeja de Abiertos
$('#btnVolver').click(function() {
    // Verificar si viene de tickets.php
    if(document.referrer.indexOf('tickets.php') !== -1) {
        history.back(); // Regresa a la página anterior
    } else {
        window.location.href = 'tickets.php'; // Redirige por defecto
    }
});

// Función para obtener el parámetro de la URL
var getURLParameter = function(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&');
    var sParameterName;
    for (var i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

$('#reservationdate').datetimepicker({
    format: 'YYYY-MM-DD'
});

$.post(
    "../../controller/ReporteMtto.php?op=numeroReporte",
    function (data, status) {
        //console.log("Respuesta del servidor:", data);
        $("#numb_reporte").val(data);
    }
);

$.post("../../controller/Obras.php?op=comboObras", function (data) {
    $('#nomb_obra').html(data);
});

$.post("../../controller/ReporteMtto.php?op=comboTipoMtto", function (data) {
    $('#tipo_mtto').html(data);
});



init();

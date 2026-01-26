function init() {
    // Inicialización si es necesario
    $("#orden_form").on("submit", function(e){
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


$(document).ready(function() {
    $('#btnCrearOrden').on('click', function() {
        let solicitud = $('#ticket_id').val(); 
        $('#mdltitulo').html('No. Solicitud: '+ solicitud);
        $('#modalOrdenMtto').modal('show');  // Abrir el modal
    });

});

function guardar(e){
     e.preventDefault();
     let num_orden = $('#num_orden').val();
     let fecha_asignacion = $('#fecha_reporte').val();
     let mantenimiento = $('#tipo_mtto').val();
     let tecnico = $('#tecnico_orden').val();
     let actividad = $('#activ_orden').val();
     let prioridad = $('#prioridad_orden').val();
     let ticket_id = $('#ticket_id').val();

     let data = {
        num_orden: num_orden,
        fecha_asignacion: fecha_asignacion,
        mantenimiento : mantenimiento,
        tecnico : tecnico,
        actividad : actividad,
        prioridad : prioridad,
        ticket_id : ticket_id
     };

     console.log(data);

     $.ajax({

        url: '../../controller/OrdenTrabajo.php?op=guardarOrdenTrabajo',
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
                    $('#modalOrdenMtto').modal('hide');
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
    "../../controller/OrdenTrabajo.php?op=numeroOrden",
    function (data, status) {
        //console.log("Respuesta del servidor:", data);
        $("#num_orden").val(data);
    }
);

$.post("../../controller/Obras.php?op=comboObras", function (data) {
    $('#nomb_obra').html(data);
});

$.post("../../controller/ReporteMtto.php?op=comboTipoMtto", function (data) {
    $('#tipo_mtto').html(data);
});



init();

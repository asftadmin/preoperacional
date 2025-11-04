var tabla = null;
function init(){
    
}

document.getElementById('abiertos-btn').addEventListener('click', function() {
    cargarSolicitudes();  // Llama a la función que carga los tickets "Abiertos"
});

document.getElementById('revision-btn').addEventListener('click', function() {
    cargarTicketsRevision();  // Llama a la función que carga los tickets "En Revisión"
});

//funcion cargar tickets
function cargarSolicitudes() {
    if (tabla) {
        tabla.clear(); // Limpiar la tabla existente
    } else {
        tabla = $('#tableTickets').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "searching": false,
            "lengthChange": false,
            "colReorder": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 7,
            "autoWidth": false,
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            "ajax": {
                url: '../../controller/tickets.php?op=listarTicketsOpen',
                type: "POST",
                dataType: "json",
                data: {
                    parametro: 'valor'
                },
                success: function(response) {
                    if (response && response.aaData) {
                        tabla.clear().rows.add(response.aaData).draw(); // Añadir los datos a la tabla
                    }
                },
                error: function(e) {
                    console.log(e.responseText);
                }
            }
        });
    }
}

function cargarTicketsRevision(){

    if (tabla) {
        tabla.clear(); // Limpiar la tabla existente
    } else {
        tabla = $('#tableTktRev').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "searching": false,
            "lengthChange": false,
            "colReorder": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 10,
            "autoWidth": false,
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            },
            "ajax": {
                url: '../../controller/tickets.php?op=listarTicketsRevision',
                type: "POST",
                dataType: "json",
                data: {
                    parametro: 'valor'
                },
                success: function(response) {
                    if (response && response.aaData) {
                        tabla.clear().rows.add(response.aaData).draw(); // Añadir los datos a la tabla
                    }
                },
                error: function(e) {
                    console.log(e.responseText);
                }
            }
        });
    }


}

function verDetalleTicket(ticketID){

    $.ajax({
        url: '../../controller/tickets.php?op=detalleTicket',
        type: 'GET',
        data: {id : ticketID},
        success: function(response){
            const detalle = JSON.parse(response);

            if(detalle.status && detalle.status === 'error'){
                $('#readMessage').html(detalle.html);
                Swal.fire('Error', detalle.message, 'error');
            }else{

                $('#readMessage').html(detalle.html);
                
                // Opcional: Actualizar la URL sin recargar (HTML5 History API)
                history.pushState(null, null, `?id=${ticketID}`);

            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Hubo un error al obtener los detalles: ' + error, 'error');
        }
        
    }); 
}



function ver(ticketID) {
    console.log(ticketID);
    window.location.href = BASE_URL +'/view/tickets/detalle_ticket.php?id='+ticketID; //http://181.204.219.154:3396/preoperacional
}

// Manejar el botón "Volver" (si lo implementas)
$(document).on('click', '.btn-volver-tickets', function() {
    $('#readMessage').html(`
        <div class="text-center py-5">
            <i class="fas fa-ticket-alt fa-4x text-muted"></i>
            <h4 class="mt-3">Selecciona un ticket para ver su detalle</h4>
        </div>
    `);
    history.pushState(null, null, 'tickets.php');
});

init();
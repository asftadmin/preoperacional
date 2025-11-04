let tabla;

function init(){

    $("#crear_liquidacion_form").on("submit", function(e){
        guardaryeditar(e);
    });

}

/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnCrearLiquidacion", function(){
    //$('#act_id').val('');
    $('#mdltitulo').html('Crear Liquidacion');
    $('#crear_liquidacion_form')[0].reset();
    $('#modalLiquidacion').modal('show');

    $('#fechas_extremas').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY', // visible para el usuario
            separator: ' - ',
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            customRangeLabel: 'Rango personalizado',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            monthNames: [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ],
            firstDay: 1
        },
        startDate: moment().startOf('month'),
        endDate: moment(),
        opens: 'center',
        drops: 'auto',
        autoApply: false,
        showDropdowns: true,
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes pasado': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        }
    },
    function(start, end, label) {
        // Inputs ocultos con formato YYYY-MM-DD
        $('#fecha_inicio').val(start.format('YYYY-MM-DD'));
        $('#fecha_fin').val(end.format('YYYY-MM-DD'));
    });


});

$(document).ready(function(){

    tabla=$('#liquidacion_data_status').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ajax":{
            url: '../../controller/Liquidacion.php?op=listarLiquidaciones',
            type : "post",
            dataType : "json",	
            data: tabla,			    		
            error: function(e){
                console.log(e.responseText);	
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3,
                render: function(data) {
                    // Intentar convertir a número
                    let numero = parseFloat(
                        data.toString().replace(/[^\d.,]/g, '').replace('.', '').replace(',', '.')
                    );

                    if (isNaN(numero)) {
                        // Si no es un número válido, mostrar como $0 COP
                        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(0);
                    }

                    // Formatear correctamente
                    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(numero);
                }
            },
            { "data": 4 },
            { "data": 5 }
        ],
        
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }     
    }).DataTable(); 
});

function guardaryeditar(e) {
  e.preventDefault();
  let formData = new FormData($("#crear_liquidacion_form")[0]);

  $.ajax({
    url: "../../controller/Liquidacion.php?op=saveLiquidacion",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(datos) {
      console.log(datos);
      const data = JSON.parse(datos);

      // Reset y recarga
      $('#crear_liquidacion_form')[0].reset();
      $('#modalLiquidacion').modal('hide');
      $('#liquidacion_data_status').DataTable().ajax.reload();

      // Usa Swal.fire en lugar de swal
      if (data.status.trim().toLowerCase() === "error") {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message,
          confirmButtonText: 'Ok',
          customClass: {
            confirmButton: 'btn btn-danger'
          },
          buttonsStyling: false
        });
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Correcto',
          text: data.message,
          confirmButtonText: 'Ok',
          customClass: {
            confirmButton: 'btn btn-success'
          },
          buttonsStyling: false
        });
      }
    },
    error: function(xhr) {
      console.error(xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo procesar la solicitud.',
        confirmButtonText: 'Ok'
      });
    }
  });
}


function ver(codi_liqu) {
    console.log(codi_liqu);
    window.location.href = BASE_URL +'/view/liquidacion/detalle_liquidacion.php?id='+codi_liqu; //http://181.204.219.154:3396/preoperacional
}

function detalle(codi_liqu) {
    console.log(codi_liqu);
    window.location.href = BASE_URL +'/view/liquidacion/mostrar_liquidacion.php?id='+codi_liqu; //http://181.204.219.154:3396/preoperacional
}

function listarComision(codi_liqu){
    console.log(codi_liqu);
    window.location.href = BASE_URL +'/view/liquidacion/comisiones_liq.php?id='+codi_liqu; //http://181.204.219.154:3396/preoperacional
}

function anular(liqId) {
  Swal.fire({
    title: '¿Confirmas anular esta liquidación?',
    text:  'Esta acción no se puede deshacer.',
    icon:  'warning',
    showCancelButton: true,                // ← habilita el botón Cancelar
    confirmButtonText: 'Sí, anular',       // texto botón de confirmación
    cancelButtonText:  'Cancelar',         // texto botón de cancelación
    reverseButtons:   true,                // opcional: invierte posición de botones
    focusCancel:      true                 // opcional: foco inicial en “Cancelar”
  }).then((result) => {
    if (result.isConfirmed) {
      // El usuario pulsó “Sí, anular”
      $.ajax({
        url: '../../controller/Liquidacion.php?op=anularLiquidacion',
        type: 'POST',
        dataType: 'json',
        data: { id: liqId },
        success: function(resp) {
          if (resp.status === 'success') {
            Swal.fire(
              'Anulada',
              resp.message,
              'success'
            );
            $('#liquidacion_data_status')
              .DataTable()
              .ajax.reload(null, false);
          } else {
            Swal.fire('Error', resp.message, 'error');
          }
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
        }
      });
    }
    // else: usuario pulsó “Cancelar” o cerró el modal, no hacemos nada
  });
}


function liquidar(liqId) {
  Swal.fire({
    title: '¿Confirmas cerrar esta liquidación?',
    text:  'Al cerrar se calculará el total y no podrás editarla.',
    icon:  'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, cerrar',
    cancelButtonText: 'Cancelar',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) return;
    $.ajax({
      url: '../../controller/Liquidacion.php?op=liquidar',
      type: 'POST',
      dataType: 'json',
      data: { id: liqId },
      success: function(resp) {
        if (resp.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Liquidado',
            text: resp.message,
            timer: 1500,
            showConfirmButton: false
          });
          // recarga la tabla de liquidaciones
          $('#liquidacion_data_status').DataTable().ajax.reload(null, false);
        } else {
          Swal.fire('Error', resp.message, 'error');
        }
      },
      error: function(xhr) {
        console.error(xhr.responseText);
        Swal.fire('Error', 'Fallo al contactar al servidor.', 'error');
      }
    });
  });
}













init();

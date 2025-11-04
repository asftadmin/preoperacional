let tabla;
function init() {}
$('.select2').select2();

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function (a) {
      if (a == null || a == "") {
        return 0;
      }
      var ukDatea = a.split("/");
      return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function (a, b) {
      return a < b ? -1 : a > b ? 1 : 0;
    },
    "date-uk-desc": function (a, b) {
      return a < b ? 1 : a > b ? -1 : 0;
    },
  });
$(document).ready(function () {
   
    tabla = $("#rte_obra_data")
      .dataTable({
        aProcessing: true,
        aServerSide: true,
        dom: 'Bfrtip',
        searching: true,
        lengthChange: false,
        colReorder: true,
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
        ajax: {
          url: "../../controller/ReporteObra.php?op=consultar",
          type: "post",
          dataType: "json",
          data: tabla,
          error: function (e) {
            console.log(e.responseText);
          },
        },
        columnDefs: [
          { type: "date-uk", targets: 0 }, // Cambia 0 al índice de tu columna de fechas
        ],
        order: [[0, "desc"]], // Ordenar la columna de fechas en orden descendente
        bDestroy: true,
        responsive: true,
        bInfo: true,
        iDisplayLength: 7,
        autoWidth: false,
        language: {
          sProcessing: "Procesando...",
          sLengthMenu: "Mostrar _MENU_ registros",
          sZeroRecords: "No se encontraron resultados",
          sEmptyTable: "Ningún dato disponible en esta tabla",
          sInfo: "Mostrando un total de _TOTAL_ registros",
          sInfoEmpty: "Mostrando un total de 0 registros",
          sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
          sInfoPostFix: "",
          sSearch: "Buscar:",
          sUrl: "",
          sInfoThousands: ",",
          sLoadingRecords: "Cargando...",
          oPaginate: {
            sFirst: "Primero",
            sLast: "Último",
            sNext: "Siguiente",
            sPrevious: "Anterior",
          },
          oAria: {
            sSortAscending:
              ": Activar para ordenar la columna de manera ascendente",
            sSortDescending:
              ": Activar para ordenar la columna de manera descendente",
          },
        },
      })
      .DataTable();
  });

  $(function() {
    $('#rangoFechas').daterangepicker({
        autoUpdateInput: false,
        locale: {
          format: 'YYYY-MM-DD',
          cancelLabel: 'Limpiar',
          applyLabel: 'Aplicar',
          daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
          monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]
        }
    });

    $('#rangoFechas').on('apply.daterangepicker', function(ev, picker) {
        const fechaInicio = picker.startDate.format('YYYY-MM-DD');
        const fechaFinal = picker.endDate.format('YYYY-MM-DD');
      
        // Actualiza el valor del input con el rango seleccionado
        $(this).val(fechaInicio + ' - ' + fechaFinal);
      
        console.log("Fecha inicio:", fechaInicio);
        console.log("Fecha final:", fechaFinal);
      
        consultarReporte(fechaInicio, fechaFinal);
      });
    
      $('#rangoFechas').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val(''); // Limpia el input visualmente
      });

    function consultarReporte(fechaInicio, fechaFinal) {
      // Aquí puedes usar fetch/AJAX para enviar fechas al backend
      console.log("Consultando reporte desde", fechaInicio, "hasta", fechaFinal);
    }
  });
  $.post("../../controller/Usuario.php?op=comboUsuarioInspector", function (data, status) {
    $("#ro_id_inspector").html(data);
  });
  $.post("../../controller/Usuario.php?op=comboOperadores",function(data, status){
    $('#ro_id_operador').html(data);
});

$('#btnBuscar').click(function() {  
    var ro_id_inspector = $('#ro_id_inspector').val(); 
    var ro_id_operador = $('#ro_id_operador').val(); 

    var fecha_inicio = '';
    var fecha_final = '';

    var inputRango = $('#rangoFechas');
    var picker = inputRango.data('daterangepicker');

    // Si el input tiene valor, tomar las fechas del picker
    if (inputRango.val() !== '') {
        fecha_inicio = picker.startDate.format('YYYY-MM-DD');
        fecha_final = picker.endDate.format('YYYY-MM-DD');
    }

    console.log(ro_id_inspector, ro_id_operador, fecha_inicio, fecha_final);

    tabla = $('#rte_obra_data').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [ 'copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5' ],
        "ajax": {
            url: "../../controller/ReporteObra.php?op=filtro_ro",
            type: "POST",
            data: {
                ro_id_inspector: ro_id_inspector,
                ro_id_operador: ro_id_operador,
                fecha_inicio: fecha_inicio,
                fecha_final: fecha_final
            },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 0}
        ],
        "order": [[0, "desc"]],
        "bDestroy": true,
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
        }     
    }); 
});

  init();
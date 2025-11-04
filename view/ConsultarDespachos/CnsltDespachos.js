let tabla;
function init() {}
$(".select2bs4").select2({
  theme: "bootstrap4",
});
$('#desp_vehi').on('change', function () {
  var placa = $('#desp_vehi option:selected').text(); // obtiene el texto visible
  $('#placa').val(placa); // lo asigna al input
});


//SELECT PARA VEHICULOS
$.post("../../controller/Vehiculo.php?op=comboVehiculo",function(data, status){
  $('#desp_vehi').html(data);
});

//GRAFICO CONSUMO GENERAL DE VEHICULOS
$(document).ready(function(){
  
  document.getElementById("divgrafico").innerHTML = "";

$.post("../../controller/VerDespachos.php?op=graficoRendimiento",{ },function (data) {
  data = JSON.parse(data);

  new Morris.Bar({
      element: 'divgrafico',
      data: data,
      xkey: ['vehi_placa'],
      ykeys: ['galones'],
      barColors: ["#009BA9"],
      labels: ['Value']
  });
});
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
  jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function (a) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-uk-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
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
//GRAFICO CONSUMO DE VEHICULO POR ID
$('#btnBuscar').click(function() {
  var desp_vehi = $('#desp_vehi').val();
  document.getElementById("divgraficoDetalle").innerHTML = "";
$.post("../../controller/VerDespachos.php?op=graficoDetalle",{desp_vehi : desp_vehi },function (data) {
  data = JSON.parse(data);

  new Morris.Bar({
      element: 'divgraficoDetalle',
      data: data,
      xkey: ['desp_fech'],
      ykeys: ['desp_galones'],
      barColors: ["#009BA9"],
      labels: ['Value']
  });
});
});
//TABLA CONSUMO DE VEHICULO POR ID
$('#btnBuscar').click(function() {
  var desp_vehi = $('#desp_vehi').val();

  tabla = $('#pre_data').dataTable({
      "aProcessing": true,
      "aServerSide": true,
      "searching": false,
      lengthChange: false,
      colReorder: true,
      "ajax": {
          url: '../../controller/VerDespachos.php?op=detalle_tabla',
          type: "post",
          dataType: "json",
          data: { desp_vehi: desp_vehi }, 
          error: function(e){
              console.log(e.responseText);
          }
      },
      "columnDefs": [
          { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
      ],
      "order": [[0, "desc"]],
      "bDestroy": true,
      "responsive": true,
      "bInfo": true,
      "iDisplayLength": 5,
      "autoWidth": false,
      "language": {
          "sProcessing": "Procesando...",
          "sLengthMenu": "Mostrar _MENU_ registros",
          "sZeroRecords": "No se encontraron resultados",
          "sEmptyTable": "Ningún dato disponible en esta tabla",
          "sInfo": "Mostrando un total de _TOTAL_ registros",
          "sInfoEmpty": "Mostrando un total de 0 registros",
          "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
          "sInfoPostFix": "",
          "sSearch": "Buscar:",
          "sUrl": "",
          "sInfoThousands": ",",
          "sLoadingRecords": "Cargando...",
          "oPaginate": {
              "sFirst": "Primero",
              "sLast": "Último",
              "sNext": "Siguiente",
              "sPrevious": "Anterior"
          },
          "oAria": {
              "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
          }
      }
  }).DataTable(); 
});
$("#btnDetalle").click(function () {
  var url = BASE_URL +'/view/ConsultarDespachos/detalle.php';
  window.open(url);
});
init();

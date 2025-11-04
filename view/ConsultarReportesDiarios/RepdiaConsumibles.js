let tabla;
function init() {}

$.post(
  "../../controller/Vehiculo.php?op=comboVehiculoPreop",
  function (data, status) {
    $("#repdia_vehi").html(data);
  }
);
$(".select2bs4").select2({
  theme: "bootstrap4",
});
$.post("../../controller/Obras.php?op=comboObras", function (data) {
  $("#repdia_obras").html(data);
});

$("#btnbuscar").click(function () {
  var repdia_obras = $("#repdia_obras").val();
  console.log(repdia_obras);
  var repdia_vehi = $("#repdia_vehi").val();
  console.log(repdia_vehi);
  var fecha_inicio = $("#fecha_inicio").val();
  console.log(fecha_inicio);
  var fecha_final = $("#fecha_final").val();
  console.log(fecha_final);

  tabla = $("#consumibles_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/VerReporteDiario.php?op=ReporteConsumibles",
        type: "POST",
        data: {
          repdia_obras: repdia_obras,
          repdia_vehi: repdia_vehi,
          fecha_inicio: fecha_inicio,
          fecha_final: fecha_final,
        },
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },

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

function ver(vehi_id) {
  // Obtiene los valores
  var repdia_obras = document.getElementById("repdia_obras").value || "null";
  var fecha_inicio = document.getElementById("fecha_inicio").value || "null";
  var fecha_final = document.getElementById("fecha_final").value || "null";

  console.log(vehi_id);
  // Redirecciona con los parámetros
  window.location.href =
    BASE_URL +
    "/view/ConsultarReportesDiarios/DetalleConsumible.php?ID=" +
    vehi_id +
    "&repdia_obras=" +
    repdia_obras +
    "&fecha_inicio=" +
    fecha_inicio +
    "&fecha_final=" +
    fecha_final;
}

init();

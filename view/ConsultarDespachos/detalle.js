let tabla;
function init() {}
$(".select2bs4").select2({
  theme: "bootstrap4",
});

$("#RDmensuales_data").DataTable({
  paging: false, // Desactiva la paginación
  info: false, // Desactiva el texto de info de la tabla (e.g., "Showing 0 to 0 of 0 entries")
  scrollX: true, // Mantén el scroll horizontal activado
  searching: false, // Opcional, desactiva el cuadro de búsqueda si no lo necesitas
  lengthChange: false, // Quita el "Show X entries"
});

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

$("#btnLimpiar").click(function () {
  // Limpiar los input de tipo fecha
  $("#fecha_inicio").val("");
  $("#fecha_final").val("");

  // Limpiar los select (dejarlos en su opción por defecto)
  $("#desp_vehi").val("--Selecciona un Vehiculo--").trigger("change");
  $("#desp_cond").val("--Selecciona el Conductor--").trigger("change");
});

$.post(
  "../../controller/Vehiculo.php?op=comboVehiculoPreop",
  function (data, status) {
    $("#desp_vehi").html(data);
  }
);
$.post(
  "../../controller/Usuario.php?op=comboUsuarioCond",
  function (data, status) {
    $("#desp_cond").html(data);
  }
);

$("#btnbuscar").click(function () {
  var desp_vehi = $("#desp_vehi").val();
  var desp_cond = $("#desp_cond").val();
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_final = $("#fecha_final").val();

  tabla = $("#despachos_data")
  .dataTable({
    aProcessing: true,
    aServerSide: true,
    dom: "Bfrtip",
    searching: true,
    lengthChange: false,
    colReorder: true,
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/Despachos.php?op=listar",
        type: "POST",
        data: {
          desp_vehi: desp_vehi,
          desp_cond: desp_cond,
          fecha_inicio: fecha_inicio,
          fecha_final: fecha_final,
        },
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      columnDefs: [
        { type: "date-uk", targets: 0 }, // Cambia 0 al índice de tu columna de fechas
      ],
      order: [[0, "desc"]],
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



init();

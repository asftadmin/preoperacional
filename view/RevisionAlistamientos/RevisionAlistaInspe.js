let tabla;

function init() {}

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
  var userId = $("#user_id").val();
  console.log(userId);

  tabla = $("#inspe_data").dataTable({
    aProcessing: true,
    aServerSide: true,
    dom: "Bfrtip",
    searching: true,
    lengthChange: false,
    colReorder: true,
    ajax: {
      url: "../../controller/VerAlistamiento.php?op=listarAlistamientoxInspector",
      type: "post",
      dataType: "json",
      data: { user_id: userId },
      error: function (e) {
        console.log(e.responseText);
      },
    },
    columnDefs: [
      { type: "date-uk", targets: 1 }, // Cambia 0 al índice de tu columna de fechas
    ],
    order: [[0, "desc"]],
    bDestroy: true,
    responsive: true,
    bInfo: true,
    iDisplayLength: 5,
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
  });

  // Inicializar la segunda tabla
  $("#HM_Inspec").DataTable({
    aProcessing: true,
    aServerSide: true,
    dom: "Bfrtip",
    searching: true,
    lengthChange: false,
    colReorder: true,
    ajax: {
      url: "../../controller/VerAlistamiento.php?op=listarHM_Inspec",
      type: "post",
      dataType: "json",
      data: { user_id: userId },
      error: function (e) {
        console.log(e.responseText);
      },
    },
    columnDefs: [
      { type: "date-uk", targets: 1 }, // Cambia 0 al índice de tu columna de fechas
    ],
    order: [[1, "desc"]],
    bDestroy: true,
    responsive: true,
    bInfo: true,
    iDisplayLength: 5,
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
  });
});



init();

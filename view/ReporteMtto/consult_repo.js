let tabla;

function init() {}

$(document).ready(function () {
  tabla = $("#reportMtto")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      searching: true,
      lengthChange: false,
      colReorder: true,
      ajax: {
        url: "../../controller/ReporteMtto.php?op=listaRerporte",
        type: "post",
        dataType: "json",
        data: tabla,
        error: function (e) {
          console.log(e.responseText);
        },
      },
      order: [[1, "desc"]],

      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 9,
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
function pdf(repo_numb) {
  console.log(repo_numb);
  var url = BASE_URL + "/view/PDF/ReporteMtto.php?ID=" + repo_numb;
  window.open(url, "_blank");
}

function ver(repo_numb) {
  console.log(repo_numb);
  window.location.href =
    BASE_URL + "/view/ReporteMtto/detalle_mtto.php?ID=" + repo_numb; //http://181.204.219.154:3396/preoperacional
}

function anular(repo_codi) {
  swal(
    {
      title: "Anular Reporte Mantenimiento",
      text: "Estas seguro de anular el reporte?",
      type: "error",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: false,
    },

    function (isConfirm) {
      if (isConfirm) {
        $.post(
          "../../controller/ReporteMtto.php?op=anulado",
          { repo_codi: repo_codi },
          function (data) {}
        );
        /*Recargamos el data table*/
        $("#reportMtto").DataTable().ajax.reload();

        swal({
          title: "Anular Reporte Mantenimiento",
          text: "Registro Anulado",
          type: "success",
          confirmButtonClass: "btn btn-success",
        });
      } else {
        swal({
          title: "Anular Reporte Mantenimiento",
          text: "No se anulo el reporte",
          type: "error",
          confirmButtonClass: "btn btn-danger",
        });
      }
    }
  );
}
init();

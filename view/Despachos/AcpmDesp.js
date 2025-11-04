let tabla;

function init() {
  $("#despacho_form").on("submit", function (e) {
    guardar(e);
  });
}

$(document).ready(function() {
    // Escuchar cuando se muestra el modal
    $('#modaldespacho').on('shown.bs.modal', function () {
      // Inicializar Select2 y evitar problemas de z-index con dropdownParent
      $('#desp_cond').select2({
        dropdownParent: $('#modaldespacho'),
      });
      $('#desp_obra').select2({
        dropdownParent: $('#modaldespacho')
      });
    });
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
$.post("../../controller/Usuario.php?op=comboUsuarioCond",function(data){
    $('#desp_cond').html(data);
});

$(document).ready(function () {

  tabla = $("#acpm_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/Despachos.php?op=ListarACPM",
        type: "post",
        dataType: "json",
        data: tabla,
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
function guardar(e) {
  e.preventDefault();
  let formData = new FormData($("#despacho_form")[0]);

  swal(
      {
          title: "Confirmar Datos",
          text: "¿Deseas guardar Cambios?",
          type: "info",
          showCancelButton: true,
          confirmButtonClass: "btn-primary",
          confirmButtonText: "Sí",
          cancelButtonText: "No",
          closeOnConfirm: false,
      },
      function (isConfirm) {
          if (isConfirm) {
              $.ajax({
                  url: "../../controller/Despachos.php?op=guardaryeditarDespacho",
                  type: "POST",
                  data: formData,
                  contentType: false,
                  processData: false,
                  success: function (datos) {
                      var data = JSON.parse(datos);

                      $("#despacho_form")[0].reset();
                      $("#modaldespacho").modal("hide");
                      $("#acpm_data").DataTable().ajax.reload();

                      if (data.status.trim().toLowerCase() === "error") {
                          swal({
                              title: "Error",
                              text: data.message,
                              type: "error",
                              confirmButtonClass: "btn-danger",
                          });
                      } else {
                          swal({
                              title: "Correcto",
                              text: data.message,
                              type: "success",
                              confirmButtonClass: "btn-success",
                          }, function () {
                              // Redirige a impresion.php con el desp_id
                              window.open("impresion.php?desp_id=" + data.desp_id, "_blank");
                          });
                      }
                  },
                  error: function (xhr, status, error) {
                      swal({
                          title: "Error",
                          text: "Ocurrió un error al guardar los datos.",
                          type: "error",
                          confirmButtonClass: "btn-danger",
                      });
                  }
              });
          }
      }
  );
}


function Despacho(desp_id){
    $('#mdltitulo').html('ACPM');

    $.post("../../controller/Despachos.php?op=mostrarDespachos", { desp_id : desp_id}, function(data) { 
        data = JSON.parse(data);
        $('#desp_id').val(data.desp_id);
        $('#vehi_placa').val(data.vehi_placa);
        $('#desp_cond').val(data.desp_cond);
        $('#desp_obra').val(data.desp_obra);
        $('#desp_galones').val(data.desp_galones);
        $('#desp_recibo').val(data.desp_recibo);
        $('#desp_fech').val(data.desp_fech);
        $('#desp_hora').val(data.desp_hora);
        $('#desp_km_hr').val(data.desp_km_hr);
        $('#desp_observaciones').val(data.desp_observaciones);

    }); 
    $('#modaldespacho').modal('show');
}

init();

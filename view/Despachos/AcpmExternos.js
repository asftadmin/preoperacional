let tabla;

function init() {
  $("#dpex_form").on("submit", function (e) {
    guardaryeditar(e);
  });
}
$(document).on("click", "#btnnuevo_desp_ext", function () {
  $("#ae_id").val("");
  $("#mdltitulo").html("Nuevo Despacho Externo");
  $("#dpex_form")[0].reset();
  $("#modaldpex").modal("show");
});
$.post("../../controller/Usuario.php?op=comboUsuarioCond", function (data) {
  $("#ae_cond").html(data);
});
$.post("../../controller/Obras.php?op=comboObras", function (data) {
  $("#ae_obra").html(data);
});
$(document).ready(function () {
  // Escuchar cuando se muestra el modal
  $("#modaldpex").on("shown.bs.modal", function () {
    // Inicializar Select2 y evitar problemas de z-index con dropdownParent
    $("#ae_cond").select2({
      dropdownParent: $("#modaldpex"),
    });
    $("#ae_obra").select2({
      dropdownParent: $("#modaldpex"),
    });
  });
});

function guardaryeditar(e) {
  e.preventDefault();
  let formData = new FormData($("#dpex_form")[0]);

  /* URL DEL CONTROLADOR OP INSERT - ROL */
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
          url: "../../controller/DespachosExternos.php?op=guardaryeditarDespacho",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (datos) {
            var data = JSON.parse(datos);
            $("#dpex_form")[0].reset();
            $("#modaldpex").modal("hide");

            $("#dpex_data").DataTable().ajax.reload();

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
              });
            }
          },
        });
      }
    }
  );
}
$(document).ready(function () {
  tabla = $("#dpex_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/DespachosExternos.php?op=listar",
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
function anulado(ae_id) {

  swal({
      title: "Confirmar Anulacion",
      text: "Desea confirmar la anulacion del Despacho",
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-primary",
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      closeOnConfirm: false,
      
  }, function (isConfirm) {
      if (isConfirm) { 
          $.post("../../controller/DespachosExternos.php?op=anulado", { ae_id : ae_id}, function(data) { 
              
          });   
          $('#dpex_data').DataTable().ajax.reload();
          swal({
              title: "Correcto", 
              text: "Datos enviados correctamente",
              type: "success",
              confirmButtonClass: "btn-success",
          });
                      
      }
  });
}
init();

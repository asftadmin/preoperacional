function init() {
  $("#dss_form").on("submit", function (e) {
    guardar(e);
  });
}

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

  // Inicializa la primera tabla
  $("#ssc_data").dataTable({
    aProcessing: true,
    aServerSide: true,
    dom: "Bfrtip",
    searching: true,
    lengthChange: false,
    colReorder: true,
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
    ajax: {
      url: "../../controller/DespachosExternos.php?op=ListarDespActivos",
      type: "post",
      dataType: "json",
      data: { user_id: userId },
      error: function (e) {
        console.log(e.responseText);
      },
    },
    columnDefs: [{ type: "date-uk", targets: 0 }],
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
      sSearch: "Buscar:",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
    },
  });

  // Inicializa la segunda tabla
  $("#dss_data").dataTable({
    aProcessing: true,
    aServerSide: true,
    dom: "Bfrtip",
    searching: true,
    lengthChange: false,
    colReorder: true,
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
    ajax: {
      url: "../../controller/DespachosExternos.php?op=listardssCond", // Cambia esto a la URL adecuada
      type: "post",
      dataType: "json",
      data: { user_id: userId },
      error: function (e) {
        console.log(e.responseText);
      },
    },
    columnDefs: [{ type: "date-uk", targets: 0 }], // Cambia esto si la estructura de la tabla es diferente
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
      sSearch: "Buscar:",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
    },
  });
});

$(document).ready(function () {
  var userId = $("#user_id").val();
  console.log(userId);
  var knobElement = $(".knob");

  $.post(
    "../../controller/DespachosExternos.php?op=grafico",
    { user_id: userId },
    function (data) {
      var valorKnob = data[0].galones_disponibles;

      // Inicializa el knob con los parámetros deseados
      knobElement.knob({
        min: 0,
        max: 5000, // Establece el rango máximo adecuado
        readOnly: true, // Para que el usuario no pueda modificar el valor
        width: 160,
        height: 160,
        displayInput: true, // Muestra el valor en el knob
        format: function (value) {
          return value;
        },
      });
      // Actualiza el valor del knob con los datos obtenidos
      knobElement.val(valorKnob).trigger("change");
    }
  );
});

$(document).ready(function () {
  // Escuchar cuando se muestra el modal
  $("#modaldss").on("shown.bs.modal", function () {
    // Inicializar Select2 y evitar problemas de z-index con dropdownParent
    $("#dss_vehi").select2({
      dropdownParent: $("#modaldss"),
    });
    $("#dss_operador").select2({
      dropdownParent: $("#modaldss"),
    });
  });
});
$.post(
  "../../controller/Vehiculo.php?op=comboVehiculoPreop",
  function (data, status) {
    $("#dss_vehi").html(data);
  }
);
$.post(
  "../../controller/Usuario.php?op=comboCondVehi",
  function (data, status) {
    $("#dss_operador").html(data);
  }
);

function dss(ae_id) {
  console.log(ae_id);
  $("#dss_id").val("");
  $("#mdltitulo").html("ACPM");
  $("#dss_form")[0].reset();
  $("#dss_ae").val(ae_id);
  $("#modaldss").modal("show");
}
function guardar(e) {
  e.preventDefault();
  let formData = new FormData($("#dss_form")[0]);

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
          url: "../../controller/DespachosExternos.php?op=guardarDistribucion",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (datos) {
            var data = JSON.parse(datos);
            $("#dss_form")[0].reset();
            $("#modaldss").modal("hide");

            if (data.status.trim().toLowerCase() === "error") {
              swal({
                title: "Error",
                text: data.message,
                type: "error",
                confirmButtonClass: "btn-danger",
              });
            } else {
              swal(
                {
                  title: "Correcto",
                  text: data.message,
                  type: "success",
                  confirmButtonClass: "btn-success",
                },
                function () {
                  // Solo recargar después de que el usuario haga clic en "OK"
                  location.reload();
                }
              );
            }
          },
        });
      }
    }
  );
}

init();

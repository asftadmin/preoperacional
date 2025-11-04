let tabla;

function init() {
  $("#desp_form").on("submit", function (e) {
    guardaryeditar(e);
  });
}

$(document).ready(function() {
  // Escuchar cuando se muestra el modal
  $('#modaldesp').on('shown.bs.modal', function () {
    // Inicializar Select2 y evitar problemas de z-index con dropdownParent
    $('#desp_vehi').select2({
      dropdownParent: $('#modaldesp'),
    });
    $('#desp_obra').select2({
      dropdownParent: $('#modaldesp'),
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
/* LLamamos al Modal id del boton y id del modal  */
$(document).on("click", "#btnnuevodespacho", function () {
  $("#desp_id").val("");
  $("#mdltitulo").html("Nuevo Despacho");
  $("#desp_form")[0].reset();
  $("#modaldesp").modal("show");
});

$.post(
  "../../controller/Vehiculo.php?op=comboEquiposDesp",
  function (data, status) {
    $("#desp_vehi").html(data);
  }
);
$.post("../../controller/Obras.php?op=comboObras",function(data){
  $('#desp_obra').html(data);
});

function guardaryeditar(e) {
  e.preventDefault();
  let formData = new FormData($("#desp_form")[0]);

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
          url: "../../controller/Despachos.php?op=guardaryeditarDespacho",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (datos) {
            var data = JSON.parse(datos);
            $("#desp_form")[0].reset();
            $("#modaldesp").modal("hide");

            $("#despachos_data").DataTable().ajax.reload();

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
  var userId = $('#user_id').val();
    console.log(userId);

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
        url: "../../controller/Despachos.php?op=listarDespacho",
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
/* LLamamos al Modal id del boton y id del modal  */
function cambio_estado(desp_id) {

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
          $.post("../../controller/Despachos.php?op=cambioestado", { desp_id : desp_id}, function(data) { 
              
          });   
          $('#despachos_data').DataTable().ajax.reload();
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

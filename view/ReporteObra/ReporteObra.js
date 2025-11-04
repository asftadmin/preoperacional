let tabla;
function init() {}

$(document).ready(function () {
  // Inicializar select2 en todos los selects dentro de la tabla cuando se cargue la página
  $("#ro_data select").each(function () {
    $(this).select2({
      dropdownParent: $("#ro_data"),
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

  // Aplicar select2 a nuevos selects que se agreguen dinámicamente a la tabla
  $(document).on("DOMNodeInserted", "#ro_data", function (e) {
    let $select = $(e.target).find("select");

    if ($select.length > 0 && !$select.hasClass("select2-hidden-accessible")) {
      $select.select2({
        dropdownParent: $("#ro_data"),
      });
    }
  });
});

document.getElementById("agregarFila").addEventListener("click", function () {
  let tbody = document.getElementById("tabla-body");
  let fila = document.createElement("tr");

  fila.innerHTML = `
      <td class="text-center">
          <input type="date" class="form-control" name="ro_fecha[]" required>
      </td>
      <td class="text-center">
        <span id="selectVehiculo">
          <select class="form-control ro_id_inspector" name="ro_id_inspector[]" required>
              <option value="">Cargando...</option>
          </select>
        </span>
      </td>
      <td class="text-center">
        <span id="selectVehiculo">
          <select class="form-control ro_id_obra" name="ro_id_obra[]" required>
              <option value="">Cargando...</option>
          </select>
        </span>
      </td>
      <td class="text-center">
        <span id="selectCond">
          <select class="form-control ro_id_operador" name="ro_id_operador[]" required>
              <option value="">Cargando...</option>
          </select>
        </span>
      </td>
      <td class="text-center">
          <input type="time" class="form-control" name="ro_hr_inicio[]" placeholder="Hora Inicio" required>
      </td>
      <td class="text-center">
          <input type="text" class="form-control" name="ro_actv[]" placeholder="Actividad" required>
      </td>
      <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm eliminar-fila">🗑 Eliminar</button>
      </td>
  `;

  tbody.appendChild(fila);

  // Llamar AJAX para obtener las obras
  $.post("../../controller/Obras.php?op=comboObras", function (data) {
    fila.querySelector(".ro_id_obra").innerHTML = data;
  });
  $.post("../../controller/Usuario.php?op=comboUsuario", function (data) {
    fila.querySelector(".ro_id_operador").innerHTML = data;
  });
  $.post("../../controller/Usuario.php?op=comboUsuarioInspector", function (data) {
    fila.querySelector(".ro_id_inspector").innerHTML = data;
  });
});

// Eliminar fila
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("eliminar-fila")) {
    e.target.closest("tr").remove();
  }
});

// Enviar formulario con AJAX
$("#form-reporte").submit(function (e) {
  e.preventDefault();
  $.ajax({
    url: "../../controller/ReporteObra.php?op=guardar",
    type: "POST",
    data: $(this).serialize(),
    success: function (datos) {
      console.log(datos);
      $("#tabla-body").empty();
      $("#ro_clouse_data").DataTable().ajax.reload();
      swal({
        title: "Correcto",
        text: "Datos enviados correctamente",
        type: "success",
        confirmButtonClass: "btn-success",
      });
    },
  });
});
$(document).ready(function () {
  var user_id = $("#user_id").val();
  console.log(user_id);

  tabla = $("#ro_clouse_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/ReporteObra.php?op=listar",
        type: "post",
        dataType: "json",
        data: { user_id: user_id },
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
      iDisplayLength: 4,
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

// Evento para actualizar la hora final
$(document).on("click", ".btn-update", function () {
  let ro_id = $(this).data("id"); // Obtener el ID desde el botón
  let nuevaHora = $("#hora_final_" + ro_id).val(); // Obtener la hora desde el input

  if (!nuevaHora) {
    swal("Error", "Debe ingresar una hora final.", "error");
    return;
  }

  swal(
    {
      title: "Guardar Hora Final",
      text: "¿Estás seguro de guardar la Hora Final?",
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-primary",
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      closeOnConfirm: false,
      showLoaderOnConfirm: true,
    },
    function (isConfirm) {
      if (isConfirm) {
        $.ajax({
          url: "../../controller/ReporteObra.php?op=update",
          type: "POST",
          data: { ro_id: ro_id, ro_hr_final: nuevaHora },
          success: function (datos) {
            try {
              var data = JSON.parse(datos);
              console.log("Respuesta del servidor:", data);

              if (data.status.trim().toLowerCase() === "success") {
                swal({
                  title: "Correcto",
                  text: data.message,
                  type: "success",
                  confirmButtonClass: "btn-success",
                });

                $("#ro_clouse_data").DataTable().ajax.reload();
              } else {
                swal({
                  title: "Error",
                  text: data.message,
                  type: "error",
                  confirmButtonClass: "btn-danger",
                });
              }
            } catch (error) {
              console.error("Error al parsear JSON:", error, datos);
            }
          },
          error: function (xhr, status, error) {
            console.error("Error en AJAX:", error);
          },
        });
      }
    }
  );
});

init();

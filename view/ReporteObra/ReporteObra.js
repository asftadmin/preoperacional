let tabla;
let filaReporte = 0;

const combosReporte = {
  inspectores: "",
  obras: "",
  operadores: "",
};

const dataTableLanguage = {
  sProcessing: "Procesando...",
  sLengthMenu: "Mostrar _MENU_ registros",
  sZeroRecords: "No se encontraron resultados",
  sEmptyTable: "Ningun dato disponible en esta tabla",
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
    sLast: "Ultimo",
    sNext: "Siguiente",
    sPrevious: "Anterior",
  },
  oAria: {
    sSortAscending: ": Activar para ordenar la columna de manera ascendente",
    sSortDescending: ": Activar para ordenar la columna de manera descendente",
  },
};

const FORMATO_FECHA_HORA_VISIBLE = "YYYY-MM-DD hh:mm A";
const FORMATO_FECHA_HORA_BD = "YYYY-MM-DD HH:mm";

const datePickerLocale = {
  format: FORMATO_FECHA_HORA_VISIBLE,
  separator: " - ",
  applyLabel: "Aplicar",
  cancelLabel: "Cancelar",
  fromLabel: "Desde",
  toLabel: "Hasta",
  customRangeLabel: "Personalizado",
  weekLabel: "S",
  daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
  monthNames: [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ],
  firstDay: 1,
};

function init() {}

function registrarOrdenFecha() {
  jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function (a) {
      if (a == null || a === "") {
        return 0;
      }

      const ukDatea = a.split("/");
      return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function (a, b) {
      return a < b ? -1 : a > b ? 1 : 0;
    },
    "date-uk-desc": function (a, b) {
      return a < b ? 1 : a > b ? -1 : 0;
    },
  });
}

function normalizarCombo(html, placeholder) {
  if (html && $.trim(html) !== "") {
    return html;
  }

  return `<option value="" disabled selected>${placeholder}</option>`;
}

function cargarCombos() {
  $("#agregarFila").prop("disabled", true);

  return $.when(
    $.post("../../controller/Usuario.php?op=comboUsuarioInspector"),
    $.post("../../controller/Obras.php?op=comboObras"),
    $.post("../../controller/Usuario.php?op=comboOperadores")
  )
    .done(function (inspectores, obras, operadores) {
      combosReporte.inspectores = normalizarCombo(
        inspectores[0],
        "--Seleccione el Inspector--"
      );
      combosReporte.obras = normalizarCombo(obras[0], "--Seleccione la Obra--");
      combosReporte.operadores = normalizarCombo(
        operadores[0],
        "--Seleccione el Operador--"
      );
      $("#agregarFila").prop("disabled", false);
    })
    .fail(function () {
      $("#agregarFila").prop("disabled", false);
      swal("Error", "No fue posible cargar las listas de seleccion.", "error");
    });
}

function inicializarSelect2($contenedor) {
  if (!$.fn.select2) {
    return;
  }

  $contenedor.find(".reporte-select").each(function () {
    const $select = $(this);

    if ($select.hasClass("select2-hidden-accessible")) {
      $select.select2("destroy");
    }

    $select.select2({
      theme: "bootstrap4",
      width: "100%",
      dropdownParent: $("body"),
      placeholder: $select.data("placeholder") || "Seleccione",
    });
  });
}

function inicializarFechaHoraInicio($input) {
  const $fila = $input.closest("tr");
  const fechaActual = $fila.find(".ro-fecha-hidden").val();
  const horaActual = $fila.find(".ro-hora-hidden").val();
  const inicio = moment(`${fechaActual} ${horaActual}`, FORMATO_FECHA_HORA_BD, true);
  const fechaHora = inicio.isValid() ? inicio : moment();

  $input.daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    timePicker: true,
    timePicker24Hour: false,
    timePickerIncrement: 5,
    autoUpdateInput: false,
    startDate: fechaHora,
    locale: datePickerLocale,
  });

  $input.on("apply.daterangepicker", function (ev, picker) {
    const seleccion = picker.startDate;

    $(this).val(seleccion.format(FORMATO_FECHA_HORA_VISIBLE));
    $fila.find(".ro-fecha-hidden").val(seleccion.format("YYYY-MM-DD"));
    $fila.find(".ro-hora-hidden").val(seleccion.format("HH:mm"));
  });
}

function inicializarHoraFinal($contenedor) {
  $contenedor.find(".ro-fecha-hora-final").each(function () {
    const $input = $(this);

    if ($input.data("picker-ready")) {
      return;
    }

    const fechaActual = $input.data("fecha") || moment().format("YYYY-MM-DD");
    const horaActual = $input.data("hora24") || moment().format("HH:mm");
    const fechaHoraActual = moment(
      `${fechaActual} ${horaActual}`,
      FORMATO_FECHA_HORA_BD,
      true
    );

    $input.daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      timePicker: true,
      timePicker24Hour: false,
      timePickerIncrement: 5,
      autoUpdateInput: false,
      startDate: fechaHoraActual.isValid() ? fechaHoraActual : moment(),
      locale: datePickerLocale,
    });

    $input.on("apply.daterangepicker", function (ev, picker) {
      const seleccion = picker.startDate;

      $(this)
        .val(seleccion.format(FORMATO_FECHA_HORA_VISIBLE))
        .data("fecha", seleccion.format("YYYY-MM-DD"))
        .data("hora24", seleccion.format("HH:mm"));
    });

    $input.data("picker-ready", true);
  });
}

function inicializarHoraFinalMasiva() {
  const $input = $("#horaFinalMasiva");
  const ahora = moment();

  $input
    .val(ahora.format(FORMATO_FECHA_HORA_VISIBLE))
    .data("fecha", ahora.format("YYYY-MM-DD"))
    .data("hora24", ahora.format("HH:mm"));

  $input.daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    timePicker: true,
    timePicker24Hour: false,
    timePickerIncrement: 5,
    autoUpdateInput: false,
    startDate: ahora,
    locale: datePickerLocale,
  });

  $input.on("apply.daterangepicker", function (ev, picker) {
    const seleccion = picker.startDate;

    $(this)
      .val(seleccion.format(FORMATO_FECHA_HORA_VISIBLE))
      .data("fecha", seleccion.format("YYYY-MM-DD"))
      .data("hora24", seleccion.format("HH:mm"));
  });
}

function obtenerHora24($input) {
  const horaData = $input.data("hora24");

  if (horaData) {
    return horaData;
  }

  const fechaHora = moment($input.val(), FORMATO_FECHA_HORA_VISIBLE, true);
  return fechaHora.isValid() ? fechaHora.format("HH:mm") : "";
}

function agregarFilaReporte() {
  if (!combosReporte.inspectores || !combosReporte.obras || !combosReporte.operadores) {
    swal("Atencion", "Las listas aun se estan cargando.", "info");
    return;
  }

  filaReporte += 1;

  const ahora = moment();
  const fecha = ahora.format("YYYY-MM-DD");
  const hora = ahora.format("HH:mm");
  const fechaHora = ahora.format(FORMATO_FECHA_HORA_VISIBLE);

  const $fila = $(`
    <tr data-fila="${filaReporte}">
      <td data-label="Fecha y hora inicio">
        <input type="text" class="form-control ro-fecha-hora-inicio" value="${fechaHora}" readonly required>
        <input type="hidden" class="ro-fecha-hidden" name="ro_fecha[]" value="${fecha}">
        <input type="hidden" class="ro-hora-hidden" name="ro_hr_inicio[]" value="${hora}">
      </td>
      <td data-label="Inspector">
        <select class="form-control reporte-select ro_id_inspector" name="ro_id_inspector[]" data-placeholder="Inspector" required>
          ${combosReporte.inspectores}
        </select>
      </td>
      <td data-label="Obra">
        <select class="form-control reporte-select ro_id_obra" name="ro_id_obra[]" data-placeholder="Obra" required>
          ${combosReporte.obras}
        </select>
      </td>
      <td data-label="Operador">
        <select class="form-control reporte-select ro_id_operador" name="ro_id_operador[]" data-placeholder="Operador" required>
          ${combosReporte.operadores}
        </select>
      </td>
      <td data-label="Actividad">
        <input type="text" class="form-control ro-actividad" name="ro_actv[]" placeholder="Actividad" required>
      </td>
      <td class="text-center" data-label="Accion">
        <button type="button" class="btn btn-danger btn-sm eliminar-fila" title="Eliminar">
          <i class="fas fa-trash"></i> Eliminar
        </button>
      </td>
    </tr>
  `);

  $("#tabla-body").append($fila);
  inicializarSelect2($fila);
  inicializarFechaHoraInicio($fila.find(".ro-fecha-hora-inicio"));
}

function recargarTablaCierre() {
  if ($.fn.DataTable.isDataTable("#ro_clouse_data")) {
    $("#ro_clouse_data").DataTable().ajax.reload(null, false);
  }
}

function inicializarTablaCierre() {
  const userId = $("#user_id").val();

  tabla = $("#ro_clouse_data").DataTable({
    processing: true,
    serverSide: false,
    searching: true,
    lengthChange: false,
    colReorder: true,
    ajax: {
      url: "../../controller/ReporteObra.php?op=listar",
      type: "post",
      dataType: "json",
      data: { user_id: userId },
      dataSrc: "aaData",
      error: function (e) {
        console.log(e.responseText);
      },
    },
    columnDefs: [
      { orderable: false, searchable: false, targets: [0, 7] },
      { type: "date-uk", targets: 1 },
    ],
    order: [[1, "desc"]],
    destroy: true,
    responsive: false,
    info: true,
    pageLength: 6,
    autoWidth: false,
    language: dataTableLanguage,
    drawCallback: function () {
      $("#seleccionarTodosCerrar").prop("checked", false);
      actualizarContadorSeleccionados();
      inicializarHoraFinal($("#ro_clouse_data"));
    },
  });
}

function obtenerReportesSeleccionados() {
  return $(".ro-cerrar-check:checked")
    .map(function () {
      return $(this).val();
    })
    .get();
}

function actualizarContadorSeleccionados() {
  const seleccionados = obtenerReportesSeleccionados().length;
  $("#contadorSeleccionados").text(`${seleccionados} seleccionados`);
}

function cerrarReportesSeleccionados() {
  const roIds = obtenerReportesSeleccionados();
  const $horaFinal = $("#horaFinalMasiva");
  const hora24 = obtenerHora24($horaFinal);

  if (roIds.length === 0) {
    swal("Atencion", "Seleccione al menos un reporte.", "info");
    return;
  }

  if (!hora24) {
    swal("Error", "Seleccione la fecha y hora final.", "error");
    return;
  }

  swal(
    {
      title: "Cerrar reportes",
      text: `Se cerraran ${roIds.length} reportes con ${$horaFinal.val()}.`,
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-primary",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: false,
      showLoaderOnConfirm: true,
    },
    function (isConfirm) {
      if (!isConfirm) {
        return;
      }

      $.ajax({
        url: "../../controller/ReporteObra.php?op=update_multiple",
        type: "POST",
        data: {
          ro_ids: roIds,
          ro_hr_final: hora24,
          user_id: $("#user_id").val(),
        },
        dataType: "json",
        success: function (data) {
          if (data.status && data.status.trim().toLowerCase() === "success") {
            swal({
              title: "Correcto",
              text: data.message,
              type: "success",
              confirmButtonClass: "btn-success",
            });
            recargarTablaCierre();
            return;
          }

          swal("Error", data.message || "No fue posible cerrar los reportes.", "error");
        },
        error: function (xhr) {
          console.log(xhr.responseText);
          swal("Error", "No fue posible cerrar los reportes seleccionados.", "error");
        },
      });
    }
  );
}

function guardarReportes(e) {
  e.preventDefault();

  if ($("#tabla-body tr").length === 0) {
    swal("Atencion", "Agregue al menos una fila.", "info");
    return;
  }

  if (this.reportValidity && !this.reportValidity()) {
    return;
  }

  $.ajax({
    url: "../../controller/ReporteObra.php?op=guardar",
    type: "POST",
    data: $(this).serialize(),
    dataType: "json",
    success: function (respuesta) {
      if (respuesta.status === "success") {
        $("#tabla-body").empty();
        recargarTablaCierre();
        swal({
          title: "Correcto",
          text: "Datos enviados correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
        return;
      }

      swal("Error", respuesta.message || "No fue posible guardar.", "error");
    },
    error: function (xhr) {
      console.log(xhr.responseText);
      swal("Error", "No fue posible guardar el reporte.", "error");
    },
  });
}

function actualizarHoraFinal(roId, nuevaHora) {
  $.ajax({
    url: "../../controller/ReporteObra.php?op=update",
    type: "POST",
    data: { ro_id: roId, ro_hr_final: nuevaHora },
    dataType: "json",
    success: function (data) {
      if (data.status && data.status.trim().toLowerCase() === "success") {
        swal({
          title: "Correcto",
          text: data.message,
          type: "success",
          confirmButtonClass: "btn-success",
        });
        recargarTablaCierre();
        return;
      }

      swal({
        title: "Error",
        text: data.message || "No fue posible actualizar.",
        type: "error",
        confirmButtonClass: "btn-danger",
      });
    },
    error: function (xhr) {
      console.log(xhr.responseText);
      swal("Error", "No fue posible actualizar la hora final.", "error");
    },
  });
}

$(document).ready(function () {
  registrarOrdenFecha();
  cargarCombos();
  inicializarHoraFinalMasiva();
  inicializarTablaCierre();

  $("#agregarFila").on("click", agregarFilaReporte);
  $("#form-reporte").on("submit", guardarReportes);
  $("#btnCerrarSeleccionados").on("click", cerrarReportesSeleccionados);

  $(document).on("click", ".eliminar-fila", function () {
    $(this).closest("tr").remove();
  });

  $(document).on("change", "#seleccionarTodosCerrar", function () {
    $(".ro-cerrar-check").prop("checked", $(this).is(":checked"));
    actualizarContadorSeleccionados();
  });

  $(document).on("change", ".ro-cerrar-check", function () {
    const total = $(".ro-cerrar-check").length;
    const seleccionados = obtenerReportesSeleccionados().length;

    $("#seleccionarTodosCerrar").prop(
      "checked",
      total > 0 && total === seleccionados
    );
    actualizarContadorSeleccionados();
  });

  $(document).on("click", ".btn-update", function () {
    const roId = $(this).data("id");
    const $horaFinal = $(`#hora_final_${roId}`);
    const nuevaHora = obtenerHora24($horaFinal);

    if (!nuevaHora) {
      swal("Error", "Debe ingresar una fecha y hora final.", "error");
      return;
    }

    swal(
      {
        title: "Guardar Hora Final",
        text: `Esta seguro de guardar la Hora Final ${$horaFinal.val()}?`,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-primary",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          actualizarHoraFinal(roId, nuevaHora);
        }
      }
    );
  });
});

init();

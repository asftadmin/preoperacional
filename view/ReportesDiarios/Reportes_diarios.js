function init() {
  $("#repdia_form").on("submit", function (e) {
    guardar(e);
  });
}

$('.select2').select2();

$("#btnCerrarAct").click(function () {
  $(location).prop("href", "CerrarActividad.php");
});

//Tracto camion condicion
function comprobar(obj) {
  if (obj.checked) {
    document.getElementById("repdia_volu").style.display = "";
  } else {
    document.getElementById("repdia_volu").style.display = "none";
  }
}

$(document).on("click", "#btnfinalizar", function () {
  swal(
    {
      title: "Cerrar Reporte",
      text: "Al cerrar el reporte no se podran agregar mas actividades hasta el siguiente dia",
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-primary",
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      closeOnConfirm: false,
    },
    function (isConfirm) {
      if (isConfirm) {
        var repdia_recib = $("#repdia_recib").val();
        $.post(
          "../../controller/Finalizar.php?op=finalizar",
          { repdia_recib: repdia_recib },
          function (data) {}
        );
        document.getElementById("repdia_form").reset();
        $("#modalFinalizar").modal("hide");
        document.getElementById("hide-me").style.display = "none";
        document.getElementById("displayNone").style.display = "none";
        document.getElementById("btnCerrarAct").style.display = "none";
        swal({
          title: "Correcto",
          text: "Datos enviados correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      }
    }
  );
});

function guardar(e) {
  e.preventDefault();
  let formData = new FormData($("#repdia_form")[0]);
  formData.append("opcion", "guardar_respuestas");
  swal(
    {
      title: "Envío de Formulario",
      text: "¿Estás seguro de enviar el Formulario?",
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
          url: "../../controller/ReportesDiarios.php",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,

          success: function (datos) {
            var data = JSON.parse(datos);
            console.log(data.status);
            if (data.status.trim().toLowerCase() == "errores") {
              swal({
                title: "Error",
                text: data.message,
                type: "error",
                confirmButtonClass: "btn-danger",
              });
              document.getElementById("repdia_form").reset();
            } else {
              swal({
                title: "Correcto",
                text: data.message,
                type: "success",
                confirmButtonClass: "btn-success",
              });
              document.getElementById("repdia_form").reset();
            }
          },
        });
      }
    }
  );
}

$(document).ready(function () {
  // Cuando cambia la selección del vehículo
  $("#repdia_vehi").on("change", function () {
    var tipo_id = $(this).find("option:selected").data("tipo-id");
    var vehi_id = $(this).find("option:selected").data("vehi-id");
    var vehi_placa = $(this).find("option:selected").data("vehi-placa");
    $("#tipo_id").val(tipo_id);
    $("#vehi_id").val(vehi_id);
    console.log(vehi_id);
    $("#vehi_placa").val(vehi_placa);
    cargarPreguntas(tipo_id,vehi_id);
  });
});

function cargarPreguntas(tipo_id,vehi_id) {
  // Cargar las preguntas mediante AJAX al cargar la página
  $.ajax({
    url: "../../controller/ReportesDiarios.php",
    method: "POST",
    data: {
      opcion: "listarKilo_Horo",
      action: "cargar_preguntas",
      tipo_id: tipo_id,
      vehi_id: vehi_id,
    },
    success: function (data) {
      $("#kilo_horo").html(data);
      $('#repdia_obras').select2();
      $('#repdia_actv').select2();
      $('#repdia_mtprima').select2();
    },
  });
  $(document).on('change', '#repdia_actv', function () {
  const valorActividad = $(this).val();
  const contenedorExtra = $('#input_extra_act');

  contenedorExtra.html('');

 if (valorActividad === '6') {
    contenedorExtra.append(`
      <label for="repdia_num_viajes" class="mr-2 mb-0" style="min-width: 90px;"># Viajes</label>
    <input type="number" name="repdia_num_viajes" id="repdia_num_viajes" class="form-control" placeholder="Numero de Viajes" required style="max-width: 200px;">
  `);
  }
});
}

$.post(
  "../../controller/Vehiculo.php?op=comboVehiculoPreop",
  function (data, status) {
    $("#repdia_vehi").html(data);
  }
);
$.post(
  "../../controller/Usuario.php?op=comboUsuarioResidente",
  function (data, status) {
    $("#repdia_residente").html(data);
  }
);
$.post(
  "../../controller/Usuario.php?op=comboUsuarioInspector",
  function (data, status) {
    $("#repdia_inspec").html(data);
  }
);
$("#displayNone").click(function () {
  document.getElementById("hide-me").style.display = "block";
});
$(document).on("click", "#finalizar", function () {
  $("#modalFinalizar").modal("show");
});
document.addEventListener("DOMContentLoaded", function () {
  var form = document.querySelector("form"); // Ajusta esto según tu HTML para seleccionar el formulario correcto
  form.addEventListener("submit", function (event) {
    var select = document.getElementById("repdia_obras");
    var select = document.getElementById("repdia_actv");
    if (select.value === "") {
      alert("Por favor, selecciona una obra.");
      event.preventDefault();
    }
  });
});
document.addEventListener("DOMContentLoaded", function () {
  // Obtener referencias a los elementos
  const radioAceite = document.getElementById("aceite");
  const radioAdicion = document.getElementById("adicion");
  const kmHrInput = document.getElementById("km/hr");

  // Función para mostrar u ocultar el campo de entrada
  function toggleKmHrInput() {
    if (radioAceite.checked || radioAdicion.checked) {
      kmHrInput.style.display = "block";
    } else {
      kmHrInput.style.display = "none";
    }
  }

  // Agregar eventos a los radio buttons
  radioAceite.addEventListener("change", toggleKmHrInput);
  radioAdicion.addEventListener("change", toggleKmHrInput);

  // Ocultar el campo al cargar la página
  kmHrInput.style.display = "none";
});

init();

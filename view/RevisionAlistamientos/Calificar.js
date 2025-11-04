function init(){
    $("#calificarInspe_form").on("submit", function(e){
        guardaryeditar(e);
        console.log("click");
    });
    $("#realistacion_form").on("submit", function (e) {
        guardaryeditarReasignacion(e);
        console.log("click");
      });
}

/* REASIGNACION DEL EQUIPO  */
function realistacion(alista_id) {
  $(".modal-backdrop").remove();

    $("#mdltitulo1").html("Reasignacion Maquinaria");
    
    $.post(
      "../../controller/VerAlistamiento.php?op=mostraralista",
      { alista_id: alista_id },
      function (data) {
        data = JSON.parse(data);
        console.log(data);
        $("#alista_id").val(data.alista_id);
        $("#alista_inspec").val(data.alista_inspec);
        $("#alista_obras").val(data.alista_obras);
  
      }
    );
    $("#realistacion").modal("show");
  }

  $(document).ready(function () {
    // Escuchar cuando se muestra el modal
    $("#realistacion").on("shown.bs.modal", function () {
      // Inicializar Select2 y evitar problemas de z-index con dropdownParent
      $("#alista_inspec").select2({
        dropdownParent: $("#realistacion"),
      });
      $("#alista_obras").select2({
        dropdownParent: $("#realistacion"),
      });
    });
  });
  
  /* function cargarVehiculoMaquinaria(alista_codigo) {
    $.post(
      "../../controller/Vehiculo.php?op=comboMaquinariaAsignada",
      { alista_codigo: alista_codigo },
      function (data) {
        $("#alista_vehi").html(data);
        $("#alista_vehi").val(data.alista_vehi);
      }
    );
  } */
  
  $.post("../../controller/Usuario.php?op=comboUsuarioInspector",function (data, status) {
      $("#alista_inspec").html(data);
    }
  );
  $.post("../../controller/Obras.php?op=comboObras", function (data) {
    $("#alista_obras").html(data);
  });
  
  function guardaryeditarReasignacion(e) {
    e.preventDefault();
    let formData = new FormData($("#realistacion_form")[0]);
    formData.append("alista_id", $("#alista_id").val());
    formData.append("alista_inspec", $("#alista_inspec").val());
    formData.append("alista_obras", $("#alista_obras").val());
    /*URL DEL CONTROLADOR OP INSERT - ROL */
  
    $.ajax({
      url: "../../controller/VerAlistamiento.php?op=reasignacion",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (datos) {
        console.log(datos);
        $("#realistacion_form")[0].reset();
        $("#realistacion").modal("hide");
        $("#HM_Inspec").DataTable().ajax.reload();
        swal({
          title: "Correcto",
          text: "Datos enviados correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      },
    });
  }

/* RECIBIDO DEL ALISTAMIENTO  */
function calificar(alista_codigo) {
    swal({
      title: "Confirmar Recibido",
      text: "Confirmacion de Recibir los Equipos Asignados",
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-primary",
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      closeOnConfirm: false,
      
  }, function (isConfirm) {
      if (isConfirm) { 
          $.post("../../controller/VerAlistamiento.php?op=calificar", { alista_codigo : alista_codigo}, function(data) { 
              
          });   
          $('#inspe_data').DataTable().ajax.reload();
          document.getElementById("alista_codigo").style.display = "none";
          swal({
              title: "Correcto", 
              text: "Datos enviados correctamente",
              type: "success",
              confirmButtonClass: "btn-success",
          });
                      
      }
  });
}
       

/* FINALIZADO DEL ALISTAMIENTO Y STOCK DEL EQUIPO */
function RepFin(alista_id) {

  $('#mdltitulo').html('Reparacion / Devolucion');
  $('#lblprecalificar').html('Motivo:');
  $('#pre_calificar').html('<select class="form-control select2bs4" id="vehi_estado" name="vehi_estado"><option selected disabled>--Seleccione el Estado--</option><option value="stock" id="Devolucion">DEVOLUCION</option><option value="no funcional" id="Nofuncional">NO FUNCIONAL</option></select>');


    $.post("../../controller/VerAlistamiento.php?op=mostraralista", { alista_id : alista_id}, function(data) { 
        data = JSON.parse(data);
        console.log(data);
        $('#alista_id').val(data.alista_id);
        $('#alista_codigo').val(data.alista_codigo);
        $('#observaciones_inspe').val(data.observaciones_inspe);
    }); 
    $('#calificar').modal('show');

}

function guardaryeditar(e){
  e.preventDefault();
  let formData = new FormData($("#calificarInspe_form")[0]);
  formData.append("alista_codigo", $("#alista_codigo").val());
  formData.append("observaciones_inspe", $("#observaciones_inspe").val());
  
  $.ajax({
      url:"../../controller/VerAlistamiento.php?op=RepFin",
      type: "POST",
      data: formData,
      contentType:false,
      processData:false,
      success: function(datos) {
         console.log(datos);
          $('#calificarInspe_form')[0].reset();
          $('#calificar').modal('hide');
          $('#HM_Inspec').DataTable().ajax.reload();
          $('#inspe_data').DataTable().ajax.reload();
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
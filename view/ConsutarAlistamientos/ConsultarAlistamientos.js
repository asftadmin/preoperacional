let tabla;

function init() {
    $("#cond_form").on("submit", function(e){
        guardarCond(e);
        console.log("click");
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
  // Inicializar la primera tabla
  tabla = $("#alistamientos_data").DataTable({
    aProcessing: true,
    aServerSide: true,
    searching: true,
    lengthChange: false,
    colReorder: true,
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
    ajax: {
      url: "../../controller/VerAlistamiento.php?op=listarAlistamiento",
      type: "post",
      dataType: "json",
      // Elimina la línea `data: tabla,` si no estás enviando datos adicionales
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

  // Inicializar la segunda tabla
  $("#alistamientosNo_data").DataTable({
    aProcessing: true,
    aServerSide: true,
    searching: true,
    lengthChange: false,
    colReorder: true,
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
    ajax: {
      url: "../../controller/VerAlistamiento.php?op=listarAlistamientoNoFuncionales",
      type: "post",
      dataType: "json",
      // Elimina la línea `data: tabla,` si no estás enviando datos adicionales
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

  // Inicializar la tercera tabla
  $("#alistamientosMaq_data").DataTable({
    aProcessing: true,
    aServerSide: true,
    searching: true,
    lengthChange: false,
    colReorder: true,
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
    ajax: {
      url: "../../controller/VerAlistamiento.php?op=listarAlistamientoMAQ",
      type: "post",
      dataType: "json",
      // Elimina la línea `data: tabla,` si no estás enviando datos adicionales
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

function ver(alista_codigo) {
  console.log(alista_codigo);
  window.location.href =
    BASE_URL + "/view/ConsutarAlistamientos/DetalleHM.php?ID=" + alista_codigo; //http://181.204.219.154:3396/preoperacional
}

function verMAQ(alista_codigo) {
  console.log(alista_codigo);
  window.location.href =
    BASE_URL + "/view/ConsutarAlistamientos/DetalleMAQ.php?ID=" + alista_codigo; //http://181.204.219.154:3396/preoperacional
}
// ASIGNACION DE CONDUCTOR DEL ALISTAMIENTO
$.post("../../controller/Usuario.php?op=comboCondVehi",function(data, status){
    $('#alista_conductor').html(data);
});
$(document).ready(function() {
    // Escuchar cuando se muestra el modal
    $('#AlistaCond').on('shown.bs.modal', function () {
      // Inicializar Select2 y evitar problemas de z-index con dropdownParent
      $('#alista_conductor').select2({
        dropdownParent: $('#AlistaCond'),
      });
    });
});
function conductor(alista_codigo) {
  $("#mdltitulo3").html("Asignacion de Conductor");
  
  $.post(
    "../../controller/VerAlistamiento.php?op=mostrar",
    { alista_codigo: alista_codigo },
    function (data) {
      data = JSON.parse(data);
      console.log(data);
      $("#alista_codigo").val(data.alista_codigo);
      $("#alista_conductor").val(data.alista_conductor);
    }
  );
  $("#AlistaCond").modal("show");
}
function guardarCond(e){
    e.preventDefault();
    let formData = new FormData($("#cond_form")[0]);
    formData.append("alista_codigo", $("#alista_codigo").val());
    formData.append("alista_conductor", $("#alista_conductor").val());
    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/VerAlistamiento.php?op=condAlista",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        success: function(datos) {
           console.log(datos);
            $('#cond_form')[0].reset();
            $('#AlistaCond').modal('hide');
            $('#alistamientos_data').DataTable().ajax.reload();
                swal({
                    title: "Correcto", 
                    text: "Datos enviados correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success",
                });
        }
    });
}
/* LLamamos al Modal id del boton y id del modal  */
function Reparado(alista_id) {
  swal(
    {
      title: "Confirmar Funcionamiento",
      text: "Al confirmar la maquinaria volvera a estar disponible",
      type: "info",
      showCancelButton: true,
      confirmButtonClass: "btn-primary",
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      closeOnConfirm: false,
    },
    function (isConfirm) {
      if (isConfirm) {
        $.post(
          "../../controller/VerAlistamiento.php?op=Reparado",
          { alista_id: alista_id },
          function (data) {}
        );
        $("#alistamientosNo_data").DataTable().ajax.reload();
        swal({
          title: "Correcto",
          text: "Datos enviados correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      }
    }
  );
}

/* LLamamos al Modal id del boton y id del modal  */
/* function calificarMAQ(alista_codigo) {

    swal({
        title: "Confirmar Alistamiento",
        text: "Al confirmar llegara la notificacion al inspector",
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-primary",
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        closeOnConfirm: false,
        
    }, function (isConfirm) {
        if (isConfirm) { 
            $.post("../../controller/VerAlistamiento.php?op=cambioestadoMAQ", { alista_codigo : alista_codigo}, function(data) { 
                
            });   
            $('#alistamientosMaq_data').DataTable().ajax.reload();
            swal({
                title: "Correcto", 
                text: "Datos enviados correctamente",
                type: "success",
                confirmButtonClass: "btn-success",
            });
                        
        }
    }); 
} */

init();

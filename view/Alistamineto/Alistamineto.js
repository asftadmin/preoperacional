let tabla;

function init() {
  $("#form_alistamiento").on("submit", function (e) {
    guardar(e);
  });
}
$(document).on("click", "#btnnuevoalista", function () {
  $("#alista_id").val("");
  $("#mdltitulo").html("Asignacion de Equipos");
  $("#form_alistamiento")[0].reset();
  $("#modalalista").modal("show");
});
function guardar(e) {
    e.preventDefault();
    let formData = new FormData($("#form_alistamiento")[0]);
    /*URL DEL CONTROLADOR OP INSERT - ROL */
  
    $.ajax({
      url: "../../controller/Alistamineto.php?op=guardar",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
  
      success: function (datos) {
        console.log(datos);
        var data = JSON.parse(datos);
        $("#form_alistamiento")[0].reset();
        $("#modalalista").modal("hide");
        $("#alista_data").DataTable().ajax.reload();
  
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

$.post("../../controller/Usuario.php?op=comboUsuario", function (data, status) {
  $("#alista_inspec").html(data);
});
$.post(
  "../../controller/Vehiculo.php?op=comboHerramientaMenor",
  function (data, status) {
    $("#alista_vehi").html(data);
  }
);

$.post("../../controller/Obras.php?op=comboObras", function (data) {
  $("#alista_obras").html(data);
});
$(document).ready(function () {
  // Escuchar cuando se muestra el modal
  $("#modalalista").on("shown.bs.modal", function () {
    // Inicializar Select2 y evitar problemas de z-index con dropdownParent
    $("#alista_inspec").select2({
      dropdownParent: $("#modalalista"),
    });
    $("#alista_vehi").select2({
      dropdownParent: $("#modalalista"),
    });
    $("#alista_obras").select2({
      dropdownParent: $("#modalalista"),
    });
  });
});

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function (a) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-uk-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

$(document).ready(function(){
    // Inicializar la primera tabla
    tabla = $('#alista_data').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '../../controller/VerAlistamiento.php?op=listar',
            type: 'post',
            dataType: 'json',
            // Elimina la línea `data: tabla,` si no estás enviando datos adicionales
            error: function(e){
                console.log(e.responseText);
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
        ],
        "columnDefs": [
            { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[0, "desc"]],
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 5,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
});

function detalle(alista_codigo) {
    console.log(alista_codigo);
    window.location.href = BASE_URL +'/view/Alistamineto/Detalle.php?ID=' + alista_codigo; 
}

init();

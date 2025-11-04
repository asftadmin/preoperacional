
let tabla;

function init(){
}

$('.select2bs4').select2({
    theme: 'bootstrap4'
});

$("#btnCorreo").click(function () {
    $("#mdltitulo").html("Correo Acumulado");
    $("#ModalCorreo").modal("show");
  });

$.post("../../controller/Vehiculo.php?op=comboVehiculoPreop",function(data, status){
    $('#repdia_vehi').html(data);
});
$.post("../../controller/Usuario.php?op=comboUsuarioCond",function(data, status){
    $('#repdia_user').html(data);
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

    tabla=$('#reporte_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [		          
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
                ],
        "ajax":{
            url: '../../controller/VerReporteDiario.php?op=listarReporte',
            type : "post",
            dataType : "json",	
            data: tabla,			    		
            error: function(e){
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 1 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[1, "desc"]],
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 5,
        "autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }     
    }).DataTable(); 
});

$('#btnBuscar').click(function() {  

    var repdia_vehi =$('#repdia_vehi').val(); 
    console.log(repdia_vehi);
    var repdia_user =$('#repdia_user').val(); 
    console.log(repdia_user);
    var fecha_inicio = $("#fecha_inicio").val();
    console.log(fecha_inicio);
    var fecha_final = $("#fecha_final").val();
    console.log(fecha_final);  

    tabla = $('#reporte_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [		          
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
            ],
    "ajax":{
        url: "../../controller/VerReporteDiario.php?op=filtrorepdia",
            type: "POST",
            data: { repdia_user: repdia_user, repdia_vehi : repdia_vehi,fecha_inicio: fecha_inicio,fecha_final: fecha_final},
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 1 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[1, "desc"]],
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 7,
        "autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }     
    }).DataTable(); 
});

$('#btnLimpiar').click(function() {
    // Limpiar los input de tipo fecha
    $('#fecha_inicio').val('');
    $('#fecha_final').val('');
    
    // Limpiar los select (dejarlos en su opción por defecto)
    $('#repdia_vehi').val('--Selecciona un Vehiculo--').trigger('change');  
    $('#repdia_user').val('--Selecciona el Conductor--').trigger('change');
});

function ver(repdia_recib) {
    console.log(repdia_recib);
    window.location.href = BASE_URL +'/view/ConsultarReportesDiarios/Detalle.php?ID=' + repdia_recib; //http://181.204.219.154:3396/preoperacional
    
}

function pdfKMS(repdia_recib) {
    console.log(repdia_recib);
    var url = BASE_URL +'/view/PDF/ReporteDiarioKMS.php?ID=' + repdia_recib;
    window.open(url, '_blank');
}
function pdfHRS(repdia_recib) {
    console.log(repdia_recib);
    var url = BASE_URL +'/view/PDF/ReporteDiarioHRS.php?ID=' + repdia_recib;
    window.open(url, '_blank');
}
document.getElementById("btnGenerarPDF").addEventListener("click", function () {
    var fecha_inicio = document.getElementById("fecha_inicio").value;
    var fecha_final = document.getElementById("fecha_final").value;
    var vehi_id = document.getElementById("repdia_vehi").value;
  
    if (fecha_inicio && fecha_final) {
      var url = BASE_URL +`/ReportesDiarios.php/Acumulado.php?var1=${encodeURIComponent(
        fecha_inicio
      )}&var2=${encodeURIComponent(fecha_final)}&var3=${encodeURIComponent(
        vehi_id
      )}`;
      window.open(url, "_blank");
    } else {
      alert("Por favor, seleccione ambas fechas.");
    }
  });

  $(document).on("click", "#btnenviar", function () {
    var user_email = $("#user_email").val();
    var repdia_vehi = $("#repdia_vehi").val();
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_final = $("#fecha_final").val();
    $("body").css("overflow", "hidden");
    if (user_email == 0) {
      swal({
        title: "!Campos Vacios!",
        text: "Por favor, asegúrate de completar el campo antes de continuar.",
        type: "info",
        confirmButtonClass: "btn-info",
      }).then(function () {
        // Reactivar el desplazamiento del cuerpo después de que se cierre el SweetAlert
        $("body").css("overflow", "auto");
      });
    } else {
      $.post(
        "../../controller/Email_RD.php?op=envio_acumulado",
        { user_email: user_email,
          fecha_inicio: fecha_inicio,
          fecha_final: fecha_final,
          repdia_vehi: repdia_vehi, },
        function (data) {
          console.log(data);
        }
      );
      swal({
        title: "¡Correcto!",
        text: "Se ha enviado un correo electrónico con el Reporte Solicitado.",
        type: "success",
        confirmButtonClass: "btn-success",
      });
    }
  });

init();
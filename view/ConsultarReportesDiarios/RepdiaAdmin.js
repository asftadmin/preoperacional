
let tabla;

function init(){
    $("#Repdia_form").on("submit", function(e){
        editarRepdia(e);
        console.log("click");
    });
}

$('.select2bs4').select2({
    theme: 'bootstrap4'
});

$.post("../../controller/Obras.php?op=comboObras",function(data, status){
    $('#repdia_obras').html(data);
});
$.post("../../controller/Usuario.php?op=comboUsuarioCond",function(data, status){
    $('#repdia_cond').html(data);
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
            url: '../../controller/VerReporteDiario.php?op=listarReporteAdmin',
            type : "post",
            dataType : "json",	
            data: tabla,			    		
            error: function(e){
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[0, "desc"]],
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

$('#btnbuscar').click(function() {  

    
    var repdia_cond =$('#repdia_cond').val(); 
    console.log(repdia_cond);    

    tabla = $('#reporte_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": false,
        lengthChange: false,
        colReorder: true,
        buttons: [		          
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
            ],
    "ajax":{
        url: "../../controller/VerReporteDiario.php?op=listarRepdiaAdminxConductor",
            type: "POST",
            data: { repdia_cond: repdia_cond},
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 0 } // Cambia 0 al índice de tu columna de fechas
        ],
        "order": [[0, "desc"]],
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

function editarRepdia(e) {
    e.preventDefault();
    let formData = new FormData($("#Repdia_form")[0]);
    formData.append("repdia_id", $("#repdia_id").val());
    formData.append("repdia_kilo", $("#repdia_kilo").val());
    formData.append("repdia_kilo_final", $("#repdia_kilo_final").val());
    formData.append("repdia_obras", $("#repdia_obras").val());
    /*URL DEL CONTROLADOR OP INSERT - ROL */
  
    $.ajax({
      url: "../../controller/VerReporteDiario.php?op=editarRepdia",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (datos) {
        console.log(datos);
        $("#Repdia_form")[0].reset();
        $("#ModalRepdia").modal("hide");
        $("#reporte_data").DataTable().ajax.reload();
        swal({
          title: "Correcto",
          text: "Datos enviados correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      },
    });
  }

function editar(repdia_id){
    $('#mdltitulo').html('Editar Reporte Diario');

    $.post("../../controller/VerReporteDiario.php?op=mostrarRepdia", { repdia_id : repdia_id}, function(data) { 
        data = JSON.parse(data);
        console.log(data);
        $('#repdia_id').val(data.repdia_id);
        $('#repdia_kilo').val(data.repdia_kilo);
        $('#repdia_kilo_final').val(data.repdia_kilo_final);
        $('#repdia_obras').val(data.repdia_obras);
    }); 

    $('#ModalRepdia').modal('show');
}

$("#btnlimpiar").click(function(){
    $(location).prop("href", "RepdiaAdmin.php");
});

function ver(repdia_recib) {
    console.log(repdia_recib);
    window.location.href = BASE_URL +'/view/ConsultarReportesDiarios/Detalle.php?ID=' + repdia_recib;
    
}



init();

let tabla;

function init(){
}

$('.select2bs4').select2({
    theme: 'bootstrap4'
});

$('#btnBuscar').click(function() {  
    // Obtener valores de mes y año
    var mes = $("#mes").val();
    var anio = $("#anio").val();

    // Validar que mes y año sean válidos
    if (!mes || !anio) {
        alert("Por favor, selecciona un mes y un año.");
        return;
    }

    console.log("Mes seleccionado: " + mes);
    console.log("Año seleccionado: " + anio);

    // Inicializar DataTable con los datos enviados al backend
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
        "ajax": {
            url: "../../controller/VerReporteDiario.php?op=ReporteCumplimientoInpsec",
            type: "POST",
            data: { mes: mes, anio: anio }, // Enviar mes y año al backend
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 1 } // Cambia 1 al índice de tu columna de fechas
        ],
        "order": [[1, "desc"]],
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
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

$('#btnconsultar').click(function() {  
    
    var fecha_inicio =$('#fecha_inicio').val(); 
    console.log(fecha_inicio);
    var fecha_final =$('#fecha_final').val(); 
    console.log(fecha_final);

    // Inicializar DataTable con los datos enviados al backend
    tabla = $('#cmpcond_data').dataTable({
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
        "ajax": {
            url: "../../controller/VerReporteDiario.php?op=CumplimientoCond",
            type: "POST",
            data: { fecha_inicio : fecha_inicio, fecha_final: fecha_final }, // Enviar mes y año al backend
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);	
            }
        },
        "columnDefs": [
            { "type": "date-uk", "targets": 1 } // Cambia 1 al índice de tu columna de fechas
        ],
        "order": [[1, "desc"]],
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
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

function ver(ro_id_inspector) {
    $('#mdltitulo').html('Detalle Cumplimiento');

   // Obtener valores de mes y año
   var mes = $("#mes").val();
   var anio = $("#anio").val();

   // Validar que mes y año sean válidos
   if (!mes || !anio) {
       alert("Por favor, selecciona un mes y un año.");
       return;
   }

   console.log("Mes seleccionado: " + mes);
   console.log("Año seleccionado: " + anio); 

    tabla = $('#rc_data').dataTable({
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
        url: "../../controller/VerReporteDiario.php?op=DetalleCumplimiento",
            type: "POST",
            data: { ro_id_inspector : ro_id_inspector, mes: mes, anio: anio},
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

    $('#modalrc').modal('show');
}


init();

let tabla;

function init(){
    $("#calificar_form").on("submit", function(e){
        guardar(e);
        console.log("click");
    });
}
$('.select2bs4').select2({
    theme: 'bootstrap4'
});

$('#btnlimpiar').click(function() {
    // Limpiar los input de tipo fecha
    $('#fecha_inicio').val('');
    $('#fecha_final').val('');
    
    // Limpiar los select (dejarlos en su opción por defecto)
    $('#pre_vehiculo').val('--Selecciona un Vehiculo--').trigger('change');  
    $('#operador').val('--Selecciona el Conductor--').trigger('change');
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

    tabla=$('#check_data').dataTable({
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
            url: '../../controller/VerPreoperacional.php?op=listarCheck',
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
        "iDisplayLength": 4,
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
$.post("../../controller/Vehiculo.php?op=comboEquiposLab",function(data, status){
    $('#pre_equipo').html(data);
});

function ver(pre_formulario) {
    console.log(pre_formulario);
    window.location.href = BASE_URL +'/view/Revision/Detalle.php?ID='+pre_formulario; //http://181.204.219.154:3396/preoperacional
}
/* LLamamos al Modal id del boton y id del modal  */
function calificar(pre_formulario) {
    $('#mdltitulo').html('Evaluar Preoperacional');
    $('#lblprecalificar').html('Validar: *');
    $('#pre_calificar').html('<select class="form-control select2bs4" id="pre_estado" name="pre_estado"><option selected disabled>--Seleccione la calificación--</option><option value="1" id="Aprobado">APROBADO</option><option value="0" id="Noaprobado">NO APROBADO</option></select>');

    $.post("../../controller/VerPreoperacional.php?op=mostrarpreo", { pre_formulario : pre_formulario}, function(data) { 
        data = JSON.parse(data);
        console.log(data);
        $('#pre_formulario').val(data.pre_formulario);
        $('#pre_observaciones_ver').val(data.pre_observaciones_ver);
        if (data.pre_estado == 'Aprobado') {
            $('#Aprobado').prop('selected', true);
        } else if (data.pre_estado == 'No aprobado') {
            $('#Noaprobado').prop('selected', true);
        }
    }); 
    $('#calificar').modal('show');
}

function guardar(e){
    e.preventDefault();
    let formData = new FormData($("#calificar_form")[0]);
    formData.append("pre_formulario", $("#pre_formulario").val());
    formData.append("pre_estado", $("#pre_estado").val());
    formData.append("pre_observaciones_ver", $("#pre_observaciones_ver").val());
    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/VerPreoperacional.php?op=calificar",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        success: function(datos) {
           console.log(datos);
            $('#calificar_form')[0].reset();
            $('#calificar').modal('hide');
            $('#check_data').DataTable().ajax.reload();
                swal({
                    title: "Correcto", 
                    text: "Datos enviados correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success",
                });
        }
    });
}

function pdfEquipoLab(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/CheckeoEquipoLab.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

init();
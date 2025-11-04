
let tabla;

function init(){
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

    tabla=$('#pre_data').dataTable({
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
            url: '../../controller/VerPreoperacional.php?op=listar',
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



//FILTRO BUSQUEDA
$('#btnBuscar').click(function(){

    var operador =$('#operador').val(); 
    console.log(operador);
    var pre_vehiculo =$('#pre_vehiculo').val(); 
    console.log(pre_vehiculo); 
    var fecha_inicio =$('#fecha_inicio').val(); 
    console.log(fecha_inicio);
    var fecha_final =$('#fecha_final').val(); 
    console.log(fecha_final);

    tabla=$('#pre_data').dataTable({
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
            url: '../../controller/VerPreoperacional.php?op=filtropreoperacional',
            type : "post",
            dataType : "json",	
            data: {operador : operador, pre_vehiculo : pre_vehiculo, fecha_inicio : fecha_inicio, fecha_final : fecha_final},			    		
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


$.post("../../controller/Usuario.php?op=comboUsuarioCond",function(data, status){
    $('#operador').html(data);
});
$.post("../../controller/Vehiculo.php?op=comboVehiculoPreop",function(data, status){
    $('#pre_vehiculo').html(data);
});

function ver(pre_formulario) {
    console.log(pre_formulario);
    window.location.href = BASE_URL +'/view/Revision/Detalle.php?ID='+pre_formulario; //http://181.204.219.154:3396/preoperacional
}

function pdfTracto(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalTracto.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfCargador(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalCargador.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfVolqueta(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalVolqueta.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfMixer(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalMixer.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfVehiculo(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalVehiculo.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfVibro(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalCompactador.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfFresadora(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalFresadora.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}
function pdfAutobomba(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalAutobomba.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}
function pdfMtnvdra(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalMtnvdra.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}
function pdfBmbaEst(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalBmbaEst.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}

function pdfPtaConcreto(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoPlantaConcreto.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}
function pdfPtAsft(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoPlantaAsfalto.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}
function pdfFinshr(pre_formulario) {
    console.log(pre_formulario);
    var url = BASE_URL +'/view/PDF/PreoperacionalFinisher.php?ID=' + pre_formulario;
    window.open(url, '_blank');
}
// Función para abrir el PDF
function abrirPDF(tipo) {
    var fecha_inicio = document.getElementById("fecha_inicio").value;
    var fecha_final = document.getElementById("fecha_final").value;
    var vehi_id = document.getElementById("pre_vehiculo").value;
  
    if (fecha_inicio && fecha_final) {
      // Creamos la URL según el tipo seleccionado
      var url = BASE_URL + `/view/AcumuladosPDF/PreoAcum${tipo}.php/Acumulado.php?var1=${encodeURIComponent(fecha_inicio)}&var2=${encodeURIComponent(fecha_final)}&var3=${encodeURIComponent(vehi_id)}`;
      window.open(url, "_blank");
    } else {
      alert("Por favor, seleccione ambas fechas.");
    }
  }
  
  // Evento del select
  document.getElementById("pdfSelect").addEventListener("change", function () {
    var tipoSeleccionado = this.value; // Obtener el valor del select
    
    // Al cambiar la opción, habilitar el botón
    var boton = document.getElementById("myBtn");
    boton.disabled = false; // Habilitar el botón cuando se seleccione algo
  
    // Event listener para el botón que abre el PDF
    boton.onclick = function () {
      abrirPDF(tipoSeleccionado); // Llamar a la función con el tipo seleccionado
    };
  });
  
  // Opcional: Deshabilitar el botón al principio si no hay selección
  document.getElementById("myBtn").disabled = true;
  
    
  
   /* LLamamos al Modal id del boton y id del modal  */
  $(document).on("click", "#Acum", function() {
    $('#mdltitulo').html('INFORME PDF');
    $('#myModal').modal('show');
  });
init();
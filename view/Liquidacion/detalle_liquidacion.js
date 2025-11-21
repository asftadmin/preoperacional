let tabla;
function init(){

    $('#filtroActividad').prop('disabled', true);
    $('#btnGuardarDetalle').prop('disabled', true);
    $('#filtroObra').prop('disabled', true);

}

function goBack() {
    history.back();
}

// Inicializar select2
$('#filtroTipoVehiculo').select2({
    placeholder: "Tipo Vehiculo",
    allowClear: true
});

$('#filtroObra').select2({
    placeholder: "Obra",
    allowClear: true
});

$('#filtroActividad').select2({
    placeholder: "Actividad",
    allowClear: true
});

$.post("../../controller/Liquidacion.php?op=combotipovehi", function (data) {
    $('#filtroTipoVehiculo').html(data);
});


$(document).on('change', '#filtroTipoVehiculo', function() {
    let tipoVehiculoId = $(this).val(); // obtener el ID del tipo de vehículo seleccionado

    $('#btnFiltrar').prop('disabled', true);

    if (tipoVehiculoId) {
        $.ajax({
            url: "../../controller/Liquidacion.php?op=comboActividades",
            type: "POST",
            data: { tipo_id: tipoVehiculoId }, // enviar el tipo_id al controller
            success: function (data) {
                $('#filtroActividad').html(data); // cargar las actividades en el select
                $('#filtroActividad').prop('disabled', false); // habilitar el select
                $('#filtroObra').html(data);
                $('#filtroObra').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar actividades: ", error);
            }
        });
    } else {
        // Si no se seleccionó nada, limpiar y deshabilitar el select de actividades
        $('#filtroActividad').html('<option value="">--Seleccione actividad--</option>');
        $('#filtroActividad').prop('disabled', true);
        ('#filtroObra').html('<option value="">--Seleccione la obra--</option>');
        $('#filtroObra').prop('disabled', true);
        $('#btnFiltrar').prop('disabled', false);
    }
});

$(document).on('change', '#filtroActividad', function() {
    let fecha_inicio = $('#fecha_inicio').val();
    let fecha_fin = $('#fecha_fin').val();
    const tipo_vehiculo = $('#filtroTipoVehiculo').val();
    
    if(fecha_inicio && fecha_fin && tipo_vehiculo){
        cargarComboObras(fecha_inicio, fecha_fin, tipo_vehiculo);
        $('#filtroObra').prop('disabled', false);
    } else {
        $('#filtroObra').html('<option value="">--Obra--</option>');
        $('#filtroObra').prop('disabled', true);
    }
});



//Cargar Obras por fecha de reportes.

function cargarComboObras(fecha_inicio, fecha_fin, tipo_vehiculo) {
  console.log(fecha_inicio, fecha_fin, tipo_vehiculo);
  const selectedObra = $('#filtroObra').val();

  $.ajax({
    url: '../../controller/Liquidacion.php?op=comboObras',
    type: 'POST',
    data: { fecha_inicio, fecha_fin, tipo_vehiculo },
    success: function(html) {
      $('#filtroObra').html(html);
      $('#filtroObra').val(selectedObra);
    },
    error: function(xhr, status, err) {
      console.error('Error al cargar obras:', err);
    }
  });
}


//Adquirir datos para el detalle ticket
$(document).ready(function() {
    var liquidacion_id = getURLParameter('id');
    
    if (!liquidacion_id) {
        alert('No se encontró el ID del ticket en la URL');
        return;
    }

    $.ajax({
        url: "../../controller/Liquidacion.php?op=detalleLiquidacion",
        method: "POST",
        data: {id: liquidacion_id},
        dataType: "json", // Esperamos una respuesta JSON
        success: function(data) {
            console.log("Respuesta completa:", data);
     
            if (data.status === 'success') {
                const liquidacion = data.data[0];
                $('#fecha_inicio').val(liquidacion.liq_fecha_inicio);
                $('#fecha_fin').val(liquidacion.liq_fecha_fin);
            } else {
                alert(data.message || 'Error desconocido');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud:", status, error);
            alert("Error al cargar los detalles del ticket");
        }
    });

});

// Función para obtener el parámetro de la URL
var getURLParameter = function(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1));
    var sURLVariables = sPageURL.split('&');
    var sParameterName;
    for (var i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}


//Variables constantes para manejo de actividades

const ES_TARIFA_FIJA = new Set([71,72,9,10,43,15,45,44,67]);
const POR_KM         = new Set([25,73,74,75,11,12,13,14,37,40,23,62,28,30,34,31,33,22,41,42,26,27]);
const POR_VOLUMEN    = new Set([8,4,2,80]); // 8 (volumen), 4/2/80 también volumen según tu lógica
const POR_VIAJE_VOL  = new Set([6,82]);   // viajes * volumen * tarifa

//Funcion calcular subtotal

function calcularSubtotal(actividadId, volumen, viajes, kmIni, kmFin, tarifa){
  const rangoKm = Math.max(0, (kmFin - kmIni) || 0);
  if (ES_TARIFA_FIJA.has(actividadId)) return 1 * tarifa;
  if (POR_KM.has(actividadId))        return rangoKm * tarifa;
  if (POR_VOLUMEN.has(actividadId))   return volumen * tarifa;
  if (POR_VIAJE_VOL.has(actividadId)) return viajes * volumen * tarifa;
  // default:
  return rangoKm * tarifa * volumen;
}

//Funcion recargar tabla

// función para cargar/recargar la tabla (llámala en btn y en change de filtros)
function cargarTabla() {
  let fecha_inicio  = $('#fecha_inicio').val();
  let fecha_fin     = $('#fecha_fin').val();
  let tipo_vehiculo = $('#filtroTipoVehiculo').val() || '';
  cargarComboObras(fecha_inicio, fecha_fin, tipo_vehiculo); // lo mantienes


  let actividad     = $('#filtroActividad').val() || ''; // puede quedar vacío
  let obra          = $('#filtroObra').val() || '';

  $('#btnGuardarDetalle').prop('disabled', false);

  tabla = $('#data_liquidacion').DataTable({
    aProcessing: true,
    aServerSide: true,
    searching: true,
    lengthMenu: [[10,20,30,50,-1],[10,20,30,50,"Todos"]],
    pageLength: 10,
    lengthChange: true,
    colReorder: true,
    responsive: {
      details: { type: 'column', target: 'td.control' }
    },
    ajax: {
      url: "../../controller/Liquidacion.php?op=readLiquidacion",
      type: "POST",
      data: { 
        tipo_vehiculo: tipo_vehiculo, 
        actividad: actividad, 
        obra: obra, 
        fecha_inicio: fecha_inicio, 
        fecha_fin: fecha_fin 
      },
      dataType: "json",
      error: function (e) { console.log(e.responseText); }
    },
    // define columnas por índice del array que envías (ver backend)
    columns: [
      { // 0 - Control + Fecha
        className: 'control',
        orderable: false,
        data: 0,
        render: (d)=> d
      },
      { data: 1 }, // 1 - Placa
      { data: 2 }, // 2 - Volumen
      { // 3 - Viajes (input)
        data: 3,
        render: function (data, type, row, meta) {
          const val = (data==null || data==='') ? 0 : data;
          return `<input type="number" step="0.01" class="form-control viajes-input" data-row="${meta.row}" value="${val}" style="width:130px;" />`;
        }
      },
      { // 4 - Km Inicial
        data: 4,
        render: function (data, type, row, meta) {
          const val = (data==null || data==='') ? 0 : data;
          return `<input type="number" step="0.01" class="form-control km-inicial-input" data-row="${meta.row}" value="${val}" style="width:130px;" />`;
        }
      },
      { // 5 - Km Final
        data: 5,
        render: function (data, type, row, meta) {
          const val = (data==null || data==='') ? 0 : data;
          return `<input type="number" step="0.01" class="form-control km-final-input" data-row="${meta.row}" value="${val}" style="width:130px;" />`;
        }
      },
      { // 6 - Rango Km
        data: null,
        render: function(data, type, row) {
          const kmin = parseFloat(row[4]) || 0;
          const kfin = parseFloat(row[5]) || 0;
          const rango = Math.max(0, kfin - kmin);
          return `<span class="rango-km">${rango.toFixed(2)}</span>`;
        }
      },
      { // 7 - Tarifa (input)
        data: 6,
        render: function(data, type, row, meta) {
          if (type !== 'display') return parseFloat(data)||0;
          let numero = parseFloat(
            (data||'').toString()
              .replace(/[^\d.,]/g,'')
              .replace(/\./g,'')
              .replace(',', '.')
          );
          if (isNaN(numero)) numero = 0;
          return `
            <input type="number" step="0.01" class="form-control tarifa-input"
              data-row="${meta.row}" value="${numero.toFixed(2)}"
              style="width:130px; text-align:right;" />
          `;
        }
      },
      { // 8 - Subtotal (render por actividad de la fila)
        data: null,
        render: function(data, type, row) {
          const vol   = parseFloat(row[2]) || 0;
          const vjs   = parseFloat(row[3]) || 0;
          const kmin  = parseFloat(row[4]) || 0;
          const kfin  = parseFloat(row[5]) || 0;
          const actId = parseInt(row[9])   || 0; // USAMOS EL ID DE LA FILA
          const tarifa  = parseFloat(
            (row[6]||'').toString()
              .replace(/[^\d.,]/g,'')
              .replace(/\./g,'')
              .replace(',', '.')
          ) || 0;

          const sub   = calcularSubtotal(actId, vol, vjs, kmin, kfin, tarifa);
          const fmt   = sub.toLocaleString('es-CO', { style:'currency', currency:'COP' });
          return `<span class="subtotal">${fmt}</span>`;
        }
      },
      { // 9 - Observaciones (lo mandaste en 7, lo dejamos "child row")
        data: 7, className: 'none', defaultContent: ''
      },
      { data: 8, visible:false, searchable:false },  // 10 - repdia_id (oculto)
      { data: 9, visible:false, searchable:false },  // 11 - actividad_id (oculto)
      { data:10, visible:false, searchable:false },  // 12 - obra_id (oculto)
      { // 13 - Checkbox para guardar selectivo
        data: null, orderable:false, className: 'text-center all',
        render: function(data, type, row){
          return `<input type="checkbox" class="row-check" data-repdia="${row[8]}">`;
        }
      }
    ],
	columnDefs: [
      { targets: 13, responsivePriority: 1 } // prioridad alta por si no usas 'all'
    ],
    order: [[0, "desc"]],
    bDestroy: true,
    bInfo: true,
    iDisplayLength: 7,
    autoWidth: false,
    language: { /* ...tu config de i18n igual...*/ },
    drawCallback: function(){
      // Recalcula rangos/subtotales si DataTables re-dibuja (por paginación, búsqueda, etc.)
      $('#data_liquidacion tbody tr').each(function(){
        const $row = $(this);
        const kmin = parseFloat($row.find('.km-inicial-input').val()) || 0;
        const kfin = parseFloat($row.find('.km-final-input').val())   || 0;
        const rango = Math.max(0, kfin - kmin);
        $row.find('.rango-km').text(rango.toFixed(2));
      });
    }
  });
}

// Botón filtrar y cambios en selects disparan la misma carga
$('#btnFiltrar').on('click', function(e) {
    e.preventDefault();
    cargarTabla();
});
$('#filtroTipoVehiculo,  #filtroObra').on('change', cargarTabla);

$('#data_liquidacion tbody').on('input', '.viajes-input, .km-inicial-input, .km-final-input, .tarifa-input', function () {
  const $curTr   = $(this).closest('tr');
  const $parent  = $curTr.hasClass('child') ? $curTr.prev('tr') : $curTr; // fila indexada por DT
  const $child   = $parent.next('tr').hasClass('child') ? $parent.next('tr') : $(); // si hay child abierto

  const rowApi   = tabla.row($parent);
  if (!rowApi.node()) return; // seguridad

  // helper: busca el input en la fila donde esté (child o parent)
  const findVal = (selector) => {
    const $src = $curTr.find(selector).length ? $curTr : $parent;
    const v = parseFloat(($src.find(selector).val() || '').toString().replace(',', '.'));
    return isNaN(v) ? 0 : v;
  };

  const data   = rowApi.data();
  const actId  = parseInt(data[9]) || 0;
  const vol    = parseFloat(data[2]) || 0;     // viene del dataset
  const viajes = findVal('.viajes-input');
  const kmin   = findVal('.km-inicial-input');
  const kfin   = findVal('.km-final-input');
  const tarifa = findVal('.tarifa-input');

  const rangoKm  = Math.max(0, kfin - kmin);
  const subtotal = calcularSubtotal(actId, vol, viajes, kmin, kfin, tarifa);

  // pinta en padre e hijo (si existe)
  const paint = ($tr) => {
    if (!$tr || !$tr.length) return;
    $tr.find('.rango-km').text(rangoKm.toLocaleString('es-CO', { minimumFractionDigits:2, maximumFractionDigits:2 }));
    $tr.find('.subtotal').text(subtotal.toLocaleString('es-CO', { style:'currency', currency:'COP' }));
  };
  paint($parent);
  paint($child);
});




$('#btnGuardarDetalle').on('click', function() {
  const liquidacion_id = getURLParameter('id');
  const tipoVehiculoId = $('#filtroTipoVehiculo').val();
  const table = $('#data_liquidacion').DataTable();
  const detalles = [];

  // Recorre las filas seleccionadas
  table.rows().every(function() {
    const row = this.data();
    const $row = $(this.node());

    // Asegúrate de que la fila esté marcada
    if ($row.find('.row-check').prop('checked')) {

      const km_inicial = parseFloat($row.find('.km-inicial-input').val()) || 0;  // 4 - Km Inicial
      const km_final   = parseFloat($row.find('.km-final-input').val()) || 0;  // 5 - Km Final
      const km_total   = parseFloat($row.find('.rango-km').text()) || 0; // Km Final - Km Inicial

      console.log("Fila seleccionada: ", row);
      console.log("Km Inicial: ", km_inicial);
      console.log("Km Final: ", km_final);
      console.log("Km Total calculado: ", km_total);

      detalles.push({
        repdia_id: row[8],              // 8 - repdia_id
        km_inicial: km_inicial,  // 4 - Km Inicial
        km_final: km_final,    // 5 - Km Final
        km_total: km_total, // Calculado: Km Final - Km Inicial
        tarifa: parseFloat(
          row[6].toString()
            .replace(/[^\d.,]/g, '')       // Limpia caracteres no numéricos
            .replace('.', '')              // Quita puntos
            .replace(',', '.')            // Cambia coma por punto
        ) || 0, // 7 - Tarifa
        subtotal: parseFloat(
          $(this.node()).find('.subtotal').text()
            .replace(/[^\d.,]/g, '')       // Limpia caracteres no numéricos
            .replace('.', '')              // Quita puntos
            .replace(',', '.')            // Cambia coma por punto
        ) || 0
      });
    }
  });

  // Si no se seleccionaron filas, muestra un mensaje de error
  if (detalles.length === 0) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Por favor, seleccione al menos un registro para guardar.',
      confirmButtonText: 'Ok',
      customClass: {
        confirmButton: 'btn btn-danger'
      },
      buttonsStyling: false
    });
    return; // Detiene la ejecución si no hay detalles seleccionados
  }

  console.log("Detalles a enviar:", detalles);

  $.ajax({
    url: '../../controller/Liquidacion.php?op=saveDetalle&id=' + liquidacion_id,
    type: 'POST',
    dataType: 'json',
    data: {
      tipo_vehiculo: tipoVehiculoId,
      detalles: JSON.stringify(detalles)
    },
    success: function(resp) {
      if (resp.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Correcto',
          text: resp.message,
          confirmButtonText: 'Ok',
          customClass: {
            confirmButton: 'btn btn-success'
          },
          buttonsStyling: false
        });
        $('#data_liquidacion').DataTable().ajax.reload(null, false);
        $('#btnGuardarDetalle').prop('disabled', true);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: resp.message,
          confirmButtonText: 'Ok',
          customClass: {
            confirmButton: 'btn btn-danger'
          },
          buttonsStyling: false
        });
      }
    },
    error: function(xhr) {
      console.error(xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Hubo un problema al guardar los detalles.',
        confirmButtonText: 'Ok'
      });
    }
  });
});

// 2) Función que habilita/deshabilita según la selección
function toggleGuardarBtn() {
  const anyChecked = $('#data_liquidacion tbody .row-check:checked').length > 0;
  $('#btnGuardarDetalle').prop('disabled', !anyChecked);
}

// 3) Escucha cambios en los checkboxes de las filas (delegado por DataTables)
$(document).on('change', '#data_liquidacion tbody .row-check', toggleGuardarBtn);


$(document).ready(function(){

    const liquidacion_id  = getURLParameter('id');

    tabla=$('#data_liquidacion_cerrada').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        dom: 'Bfrtip',
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax":{
            url: '../../controller/Liquidacion.php?op=listarLiquidacionesCerradas',
            type: 'POST',
            data: { id: liquidacion_id },  // ← manda el id por POST
            dataSrc: 'aaData',		    		
            error: function(e){
                console.log(e.responseText);	
            }
        },
     
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 10,
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




init();
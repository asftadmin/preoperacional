function init(){

  inicializarTabla();

  $('#btnGenerarComision').on('click', cargarListado);


}

$('#filtroTipoVehiculo').select2({
  theme: 'bootstrap4',
  allowClear: true,
  ajax: {
    url: '../../controller/Liquidacion.php?op=combotipovehiMultiple',
    dataType: 'html',
    delay: 250,
    processResults: function (html) {
      const $tmp = $('<select>' + html + '</select>');
      const results = [];
      $tmp.find('option').each(function () {
        results.push({ id: $(this).val(), text: $(this).text() });
      });
      return { results };
    },
    cache: true
  }
});


/* $.post("../../controller/Liquidacion.php?op=combotipovehiMultiple", function (data) {
    $('#filtroTipoVehiculo').html(data);
}); */




// Formateadores peso colombiano
const formatoMoneda = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
const formatoPorc   = new Intl.NumberFormat('es-CO', { style: 'percent', minimumFractionDigits: 2, maximumFractionDigits: 2 });

const COMISIONES_POR_PLACA = {
  // Ejemplos:
  'SXS660': 0.07, //Volqueta sencilla
  'SXS661': 0.07, //Volqueta sencilla
  'SXT576': 0.07, //Cama baja
  'XMD683': 0.07, //Volqueta sencilla
  'WFB907': 0.05, //Tracto volqueta (mula)
  'WFC435': 0.05, //Volqueta dobletroque
  'WFC575': 0.05, //Volqueta dobletroque
  'WFC436': 0.05, //Volqueta dobletroque
  'TTT-402': 0.05 //Volqueta dobletroque
};

const COMISION_POR_DEFECTO = 0.07;

/** Retorna el % de comisión (0–1) con base en la placa; si no existe, usa el default. */
function calcularComisionPorPlaca(placa) {
  const key = String(placa || '').trim().toUpperCase();
  return (key && Object.prototype.hasOwnProperty.call(COMISIONES_POR_PLACA, key))
    ? COMISIONES_POR_PLACA[key]
    : COMISION_POR_DEFECTO;
}

function enriquecerFilas(filas) {
  return (filas || []).map((fila, idx) => {
    const produccion = Number(fila.subtotal_liquidado ?? 0);
    const placa      = String(fila.placa || '').toUpperCase();

    const tasa       = calcularComisionPorPlaca(placa);
    const subtotal   = Math.round(produccion * tasa);
    const aPagar     = subtotal; // mismo valor que subtotal

    return {
      _idx: idx + 1,
      conductor: fila.conductor || '',
      placa: placa,
      produccion: produccion, // = subtotal_liquidado
      tasa: tasa,             // % comisión (0–1)
      subtotal: subtotal,
      a_pagar: aPagar
    };
  });
}

function goBack() {
    history.back();
}

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

/** Suma y pinta los totales en el footer. */
function actualizarTotales(filas) {
  const totalProd = filas.reduce((s, r) => s + (r.produccion || 0), 0);
  const totalSub  = filas.reduce((s, r) => s + (r.subtotal   || 0), 0);
  const totalPago = filas.reduce((s, r) => s + (r.a_pagar    || 0), 0);

  $('#t-total-produccion').text(formatoMoneda.format(totalProd));
  $('#t-total-subtotal').text(formatoMoneda.format(totalSub));
  $('#t-total-pagar').text(formatoMoneda.format(totalPago));
}

function asegurarFooterValido() {
  const $t = $('#data_comisiones');
  const columnas = $t.find('thead th').length;
  const $tfoot = $t.find('tfoot');
  const celdasFooter = $t.find('tfoot th, tfoot td').length;

  if ($tfoot.length === 0) {
    $t.append('<tfoot><tr></tr></tfoot>');
  }
  if ($t.find('tfoot th, tfoot td').length !== columnas) {
    // reconstruye un footer “vacío” con el mismo número de columnas
    let html = '<tr>';
    for (let i = 0; i < columnas; i++) html += '<th></th>';
    html += '</tr>';
    $t.find('tfoot').html(html);
  }
}



let dt = null;

/** Inicializa DataTable (estructura de columnas y renderizadores). */
function inicializarTabla() {

  asegurarFooterValido();

  if ($.fn.DataTable.isDataTable('#data_comisiones')) {
    dt = $('#data_comisiones').DataTable();
    return dt;
  }

  $('#data_comisiones').DataTable({
    data: [],
    autoWidth: false,
    destroy: true,
    deferRender: true,
    paging: false,
    searching: false,
    info: false,
    order: [],
    columns: [
      { data: null, width: 36, defaultContent: '',
        render: (d,t,r,m) => m.row + 1
      },
      { data: 'conductor', defaultContent: '' },
      { data: 'placa',     defaultContent: '' },
      { data: 'produccion', className: 'num', defaultContent: 0,
        render: v => formatoMoneda.format(Number(v||0))
      },
      { data: 'tasa',       className: 'num', defaultContent: 0,
        render: v => formatoPorc.format(Number(v||0))
      },
      { data: 'subtotal',   className: 'num', defaultContent: 0,
        render: v => formatoMoneda.format(Number(v||0))
      },
      { data: 'a_pagar',    className: 'num', defaultContent: 0,
        render: v => formatoMoneda.format(Number(v||0))
      },
    ],
    footerCallback: function (row, data) {
      const sum = (k) => data.reduce((s,r)=> s + Number(r[k]||0), 0);
      $(this.api().column(3).footer()).html(formatoMoneda.format(sum('produccion')));
      $(this.api().column(5).footer()).html(formatoMoneda.format(sum('subtotal')));
      $(this.api().column(6).footer()).html(formatoMoneda.format(sum('a_pagar')));
    }
  });

    return dt;
}

// Agrupa por CONDUCTOR y suma a_pagar (ya calculado por placa)
function construirResumen3Cols(filasDetalladas) {
  const mapa = new Map(); // conductor => total_a_pagar
  (filasDetalladas || []).forEach(r => {
    const key = (r.conductor || '').toUpperCase();
    const val = Number(r.a_pagar || 0);
    mapa.set(key, (mapa.get(key) || 0) + val);
  });

  let i = 1;
  const salida = [];
  for (const [conductor, totalPagar] of mapa.entries()) {
    salida.push({
      _idx: i++,
      conductor: conductor,
      a_pagar: Math.round(totalPagar)
    });
  }
  return salida;
}

let dtResumen = null;

function asegurarFooterResumenValido3() {
  const $t = $('#data_resumen');
  const cols = $t.find('thead th').length; // 3
  if ($t.find('tfoot').length === 0) $t.append('<tfoot><tr></tr></tfoot>');
  if ($t.find('tfoot th, tfoot td').length !== cols) {
    $t.find('tfoot').html('<tr><th colspan="2">TOTAL</th><th id="r-total-pagar"></th></tr>');
  }
}

function inicializarTablaResumen3() {
  asegurarFooterResumenValido3();

  if ($.fn.DataTable.isDataTable('#data_resumen')) {
    dtResumen = $('#data_resumen').DataTable();
    return dtResumen;
  }

  dtResumen = $('#data_resumen').DataTable({
    data: [],
    autoWidth: false,
    paging: false,
    searching: false,
    info: false,
    order: [],
    columns: [
      { data: null, width: 36, defaultContent: '', render: (d,t,r,m)=> m.row + 1 }, // idx
      { data: 'conductor', defaultContent: '' },
      { data: 'a_pagar', className: 'num', defaultContent: 0,
        render: v => formatoMoneda.format(Number(v||0)) }
    ],
    footerCallback: function (row, data) {
      const totalPagar = data.reduce((s,r)=> s + Number(r.a_pagar||0), 0);
      $(this.api().column(2).footer()).html(formatoMoneda.format(totalPagar)); // col 2 (0-based)
    }
  });
  return dtResumen;
}

/** Llama al backend con POST (filtros) y repinta la tabla. */
function cargarListado() {
  const liquidacionId = getURLParameter('id');
  const tipos = $('#filtroTipoVehiculo').val() || []; // arreglo de ids del select múltiple
  $.ajax({
    url: '../../controller/Liquidacion.php?op=comisiones',    // Controller@listar
    type: 'POST',
    dataType: 'json',
    data: {
      liquidacion_id: liquidacionId,
      tipos: tipos      // importante: envía como arreglo
    },
    success: function (respuesta) {
      // Esperado: [{conductor, placa, equipo, subtotal_liquidado}, ...]
      //console.log('POST recibido en PHP:', respuesta);
      const filas = enriquecerFilas(Array.isArray(respuesta) ? respuesta : (respuesta.data || []));
      const table = $.fn.DataTable.isDataTable('#data_comisiones')
        ? $('#data_comisiones').DataTable()
        : inicializarTabla();
      table.clear().rows.add(filas).draw();
      actualizarTotales(filas);

      // 2) resumen por CONDUCTOR (3 columnas)
      const filasRes = construirResumen3Cols(filas);
      const dtRes = $.fn.DataTable.isDataTable('#data_resumen')
        ? $('#data_resumen').DataTable()
        : inicializarTablaResumen3();
      dtRes.clear().rows.add(filasRes).draw();

    },
    error: function (xhr) {
      console.error('Error al cargar listado:', xhr?.responseText || xhr?.statusText);
      if ($.fn.DataTable.isDataTable('#data_comisiones')) {
        $('#data_comisiones').DataTable().clear().draw();
      }
      actualizarTotales([]);
      alert('No fue posible obtener los datos.');
    }
  });
}


$(function () {
  $('#btnDescargar').on('click', function () {
    const liquidacionId = getURLParameter('id');           // ya la tienes
    const tipos = $('#filtroTipoVehiculo').val() || [];     // array del select2

    if (!liquidacionId) {
      alert('ID inválido');
      return;
    }

    // Crea el form dinámico
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../controller/Liquidacion.php?op=reporteComisiones';
    form.target = '_blank'; // que abra el PDF en otra pestaña
    form.acceptCharset = 'utf-8';

    // liquidacion_id
    const hId = document.createElement('input');
    hId.type = 'hidden';
    hId.name = 'liquidacion_id';         // <- nombre que leerá el controller
    hId.value = liquidacionId;
    form.appendChild(hId);

    // tipos[] (varios hidden, uno por valor)
    tipos.forEach(function (t) {
      const h = document.createElement('input');
      h.type = 'hidden';
      h.name = 'tipos[]';                // <- arreglo en PHP: $_POST['tipos']
      h.value = t;
      form.appendChild(h);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  });
});








init();

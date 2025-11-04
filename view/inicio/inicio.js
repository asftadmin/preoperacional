
function init() {
  var user_id = $("#user_idx").val(); 
  cargarSelectGrafico(user_id);
  
}

$(document).ready(function(){

  var user_id = $("#user_id").val(); 
  console.log(user_id);

  $.post("../../controller/usuario.php?op=total", {user_id:user_id}, function (data) {
    data = JSON.parse(data); 
    console.log(data.total);
        $('#TOTAL').text(data.total);
}); 
});

$('#btnvermas').click(function (vehi_id_asfalto) {
  var vehi_id_asfalto = $("#vehi_id_asfalto").val();
  console.log(vehi_id_asfalto);
  window.location.href = BASE_URL +'/view/Graficos/TablaGrafico.php?ID=' + vehi_id_asfalto; //http://181.204.219.154:3396/preoperacional
});

$('#btnvermasMaq').click(function (vehi_id_maquinaria) {
  var vehi_id_maquinaria = $("#vehi_id_maquinaria").val();
  console.log(vehi_id_maquinaria);
  window.location.href = BASE_URL +'/view/Graficos/TablaGrafico.php?ID=' + vehi_id_maquinaria;
});

$('#btnvermasx').click(function (vehi_id_concreto) {
  var vehi_id_concreto = $("#vehi_id_concreto").val();
  console.log(vehi_id_concreto);
  window.location.href = BASE_URL +'/view/Graficos/TablaGrafico.php?ID=' + vehi_id_concreto;
});

$('#btnvermasfrsd').click(function (vehi_id_fresadora) {
  var vehi_id_fresadora = $("#vehi_id_fresadora").val();
  console.log(vehi_id_fresadora);
  window.location.href = BASE_URL +'/view/Graficos/TablaGraficoFrsd.php?ID=' + vehi_id_fresadora;
});

$('#btnvermasfrsd_pnts').click(function (vehi_id_fresadora) {
  var vehi_id_fresadora = $("#vehi_id_fresadora").val();
  console.log(vehi_id_fresadora);
  window.location.href = BASE_URL +'/view/Graficos/TablaGraficoFrsd_P.php?ID=' + vehi_id_fresadora;
});

function cargarSelectGrafico(user_id){
  $.ajax({
    url: "../../controller/Vehiculo.php?op=comboVehiculoPreopUser",
    method: "POST",
    data: {user_id: user_id },
    success: function (data) {
        $('#vehi_placax').html(data);
    }
  }); 
}


$(document).ready(function() {
    // Cuando cambia la selección del vehículo
    $('#vehi_placax').on('change',function() {
      var vehi_placa = $(this).find('option:selected').data('vehi-placa');
      $('#vehi_placa').val(vehi_placa);
      graficoKilometraje(vehi_placa);
  });

});

// GRAFICO VEHICULOS ASFALTO
$('#btnver').click(function(){
  var repdia_vehi = $("#repdia_vehi").val();
  var vehi_id_asfalto =document.getElementById("vehi_id_asfalto");
  console.log(repdia_vehi); 
  vehi_id_asfalto.value = repdia_vehi;
  document.getElementById("divgrafico").innerHTML = "";

$.post("../../controller/Finalizar.php?op=graficoVolqueta",{repdia_vehi : repdia_vehi},function (data) {
  data = JSON.parse(data);

  new Morris.Bar({
      element: 'divgrafico',
      data: data,
      xkey: ['repdia_fech'],
      ykeys: ['rendimiento'],
      barColors: ["#009BA9"],
      labels: ['Value']
  });
});
});

//SELECT DE VOLQUETAS Y TRACTOCMAION
$.post("../../controller/Vehiculo.php?op=comboVehiculoVolqueta",function(data, status){
  $('#repdia_vehi').html(data);
});

//GRAFICO VEHICULOS CONCRETO
$('#btnverx').click(function(){
  var repdia_vehi = $("#repdia_vehix").val();
  console.log(repdia_vehi); 
  var vehi_id_concreto =document.getElementById("vehi_id_concreto");
  vehi_id_concreto.value = repdia_vehi;
  document.getElementById("divgraficoMixer").innerHTML = "";


$.post("../../controller/Finalizar.php?op=graficoMixer",{repdia_vehix : repdia_vehi},function (data) {
  data = JSON.parse(data);

  new Morris.Bar({
      element: 'divgraficoMixer',
      data: data,
      xkey: ['repdia_fech'],
      ykeys: ['rendimiento'],
      barColors: ["#009BA9"],
      labels: ['Value']
  });
});
});

//SELECT PARA MIXER
$.post("../../controller/Vehiculo.php?op=comboVehiculoMixer",function(data, status){
  $('#repdia_vehix').html(data);
});

//GRAFICO EQUIPOS CONCRETO
$('#btnverMaq').click(function(){
  var repdia_vehi = $("#repdia_maquinaria").val();
  console.log(repdia_vehi); 
  var vehi_id_maquinaria =document.getElementById("vehi_id_maquinaria");
  vehi_id_maquinaria.value = repdia_vehi;
  document.getElementById("divgraficoMaquinaria").innerHTML = "";

$.post("../../controller/Finalizar.php?op=graficoMaquinaria",{repdia_maquinaria : repdia_vehi},function (data) {
  data = JSON.parse(data);

  new Morris.Bar({
      element: 'divgraficoMaquinaria',
      data: data,
      xkey: ['repdia_fech'],
      ykeys: ['rendimiento'],
      barColors: ["#009BA9"],
      labels: ['Value']
  });
});
});
//SELECT PARA MAQUINARIA
$.post("../../controller/Vehiculo.php?op=comboVehiculoMaquinaria",function(data, status){
  $('#repdia_maquinaria').html(data);
});

// VENCIMINETO DE LA POLIZA
$('#btnPoliza').click(function() {
  $(location).prop("href", "../VenciminetoPoliza/VenPoliza.php");
});

function graficoKilometraje(vehi_placa){
  var user_id = $("#user_idx").val(); 
  if( $('#rol_idx').val() == 1){
  // Define el selector para el elemento knob que deseas actualizar
  var knobElement = $('.knob');
  // Realiza la solicitud AJAX para obtener los datos
  var opcion = "grafico";
  $.post("../../controller/Preoperacional.php", { opcion: opcion, vehi_placax: vehi_placa }, function (data) {
      var valorKnob = data[0].kilometraje;

    
      // Inicializa el knob con los parámetros deseados
      knobElement.knob({
        'min': 0,
        'max': 500000, // Establece el rango máximo adecuado
        'readOnly': true, // Para que el usuario no pueda modificar el valor
        'width': 160,
        'height': 160, 
        'displayInput': true, // Muestra el valor en el knob
        'format': function (value) {
          return value;
        },
        
      });
      // Actualiza el valor del knob con los datos obtenidos
      knobElement.val(valorKnob).trigger('change');
  });

  }else if($('#rol_idx').val() == 2 || $('#rol_idx').val() == 3 || $('#rol_idx').val() == 4 || $('#rol_idx').val() == 5){
      
      
      // Selecciona todos los elementos con la clase "card"
      const tarjetas = document.querySelectorAll(".card");
          
      // Itera sobre los elementos seleccionados y los elimina uno por uno
      tarjetas.forEach(function(tarjeta) {
          tarjeta.parentNode.removeChild(tarjeta);
      });
  }
}

// GRAFICO RENDIMIENTO FRESADORA M3/HR TRABAJADAS
$.post("../../controller/Vehiculo.php?op=comboFresadoras",function(data, status){
  $('#repdia_fresadora').html(data);
});

$('#btnFresadora').click(function() {
  var fecha_inicio =$('#fecha_inicio').val(); 
  console.log(fecha_inicio);
  var fecha_final =$('#fecha_final').val(); 
  console.log(fecha_final); 
  var repdia_vehi = $("#repdia_fresadora").val();
  console.log(repdia_vehi);
  var vehi_id_fresadora =document.getElementById("vehi_id_fresadora");
  vehi_id_fresadora.value = repdia_vehi;
  document.getElementById("divgraficoFresadora").innerHTML = "";
  document.getElementById("divgraficoFresadora_Puntas").innerHTML = "";
  document.getElementById("divgraficoPuntas").innerHTML = "";
  
$.post("../../controller/Finalizar.php?op=graficoFresadora",{repdia_fresadora : repdia_vehi,fecha_inicio : fecha_inicio,fecha_final : fecha_final},function (data) {
  data = JSON.parse(data);
  new Morris.Line({
      element: 'divgraficoFresadora',
      data: data,
      xkey: ['repdia_fech'],
      ykeys: ['rendimiento'],
      lineColors: ["#009BA9"],
      labels: ['Value'],
      resize: true
  });
});

$.post("../../controller/Finalizar.php?op=datosFresadora",{repdia_fresadora : repdia_vehi, fecha_inicio : fecha_inicio,fecha_final : fecha_final}, function(data) { 
  data = JSON.parse(data); 
  $('#vehi_placa').val(data.vehi_placa);
  $('#tipo_nombre').val(data.tipo_nombre);
})

$.post("../../controller/Finalizar.php?op=graficoFresadora_Puntas",{repdia_fresadora : repdia_vehi, fecha_inicio : fecha_inicio, fecha_final : fecha_final},function (data) {
  data = JSON.parse(data);

  new Morris.Line({
      element: 'divgraficoFresadora_Puntas',
      data: data,
      xkey: ['repdia_fech'],
      ykeys: ['rendimiento'],
      lineColors: ["#009BA9"],
      labels: ['Value'],
      resize: true
  });
});

$.post("../../controller/Finalizar.php?op=graficoPuntas", {
  repdia_fresadora: repdia_vehi,
  fecha_inicio: fecha_inicio,
  fecha_final: fecha_final
}, function(data) {
  data = JSON.parse(data);

  // Transforma los datos para el gráfico de dona
  var donutData = data.map(function(item) {
      return {
          label: item.repdia_fech,  // Aquí la fecha como etiqueta
          value: item.total_puntas  // Aquí las puntas diarias como valor
      };
  });

  new Morris.Donut({
      element: 'divgraficoPuntas',
      data: donutData, // Usamos la data ya transformada
      resize: true,
      colors: ['#0B62A4', '#7A92A3',  '#009BA9', '#AFD8F8']
  });
});

});

init();


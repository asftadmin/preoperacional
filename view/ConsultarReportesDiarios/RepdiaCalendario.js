function init() {}
$(".select2bs4").select2({
  theme: "bootstrap4",
});

$.post(
  "../../controller/Usuario.php?op=comboUsuarioCond",
  function (data, status) {
    $("#user_id").html(data);
  }
);


$("#btnbuscar").click(function () {
  var user_id = $("#user_id").val(); // La variable que necesitas enviar
  
  var calendarEl = document.getElementById("Calendario");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    locale: "es",
    initialView: "dayGridMonth",
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    editable: false,
    events: function (fetchInfo, successCallback, failureCallback) {

      // Primera solicitud AJAX
      var request1 = $.ajax({
        url: "../../controller/VerReporteDiario.php?op=listaRepdiaCalendario",
        method: "POST",
        data: { user_id: user_id }
      });

      // Segunda solicitud AJAX
      var request2 = $.ajax({
        url: "../../controller/VerPreoperacional.php?op=listarPreCalendario",
        method: "POST",
        data: { user_id: user_id }
      });

      $.post("../../controller/usuario.php?op=PorcentajePreo", {user_id:user_id}, function (data) {
        data = JSON.parse(data); 
        console.log(data.porcentaje_preoperacionales);
        // Modificar el valor para mostrar el porcentaje con el signo '%'
        var porcentajeConSigno1 = data.porcentaje_preoperacionales + '%';
      
        // Asignar el valor al div #PORCENTAJE
        $('#Porcentaje_preo').text(porcentajeConSigno1);
      }); 
    
      $.post("../../controller/usuario.php?op=PorcentajeRD", {user_id:user_id}, function (data) {
        data = JSON.parse(data); 
        console.log(data.porcentaje_cumplimiento);
        // Modificar el valor para mostrar el porcentaje con el signo '%'
        var porcentajeConSigno = data.porcentaje_cumplimiento + '%';
    
        // Asignar el valor al div #PORCENTAJE
        $('#PORCENTAJE').text(porcentajeConSigno);
    }); 

      // Esperar a que ambas solicitudes se completen
      $.when(request1, request2).then(function (response1, response2) {
        var allEvents = [];

        // Procesar respuesta de la primera solicitud
        var events1 = JSON.parse(response1[0]);
        events1.forEach(function (event) {
          event.backgroundColor = "#009BA9"; // Color para la primera consulta
        });
        allEvents = allEvents.concat(events1);

        // Procesar respuesta de la segunda solicitud
        var events2 = JSON.parse(response2[0]);
        events2.forEach(function (event) {
          event.backgroundColor = "#09aa41"; // Color para la segunda consulta
        });
        allEvents = allEvents.concat(events2);

        // Llamar a successCallback con todos los eventos
        successCallback(allEvents);
      }, function () {
        failureCallback(); // En caso de error en cualquiera de las solicitudes
      });
    }
  });

  // Renderizar el calendario
  calendar.render();

  // Obtener el mes actual mostrado en el calendario
  var currentDate = calendar.getDate(); // Esto obtiene la fecha actual mostrada
  var currentMonth = currentDate.getMonth(); // Esto obtiene el mes (0-11)

  // Si deseas mostrar el mes en texto
  var monthNames = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
  ];

  var monthName = monthNames[currentMonth];
  console.log("Mes mostrado en el calendario: " + monthName);

  // Si quieres trabajar con el número del mes, puedes usar `currentMonth` que es un número de 0 a 11.
  // Por ejemplo, si quieres el mes en formato de número, puedes hacer algo como:
  console.log("Mes (número): " + (currentMonth + 1)); // +1 para que sea de 1 a 12
});
  

init();

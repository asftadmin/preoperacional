
const $canvas = document.querySelector("#canvas");
$btnDescargar = document.querySelector("#btnDescargar"),
$btnLimpiar = document.querySelector("#btnLimpiar");
$btnGuardar = document.querySelector("#btnGuardar");
const contexto = $canvas.getContext("2d");
const COLOR_PINCEL = "black";
const COLOR_FONDO = "white";
const GROSOR = 2;
let xAnterior =0, yAnterior =0, xActual=0, yActual=0;
const obtenerXReal = (clientX) => clientX - $canvas.getBoundingClientRect().left;
const obtenerYReal = (clientY) => clientY - $canvas.getBoundingClientRect().top;
let haComenzadoDibujo =false;


// Lo demás tiene que ver con pintar sobre el canvas en los eventos del mouse
$canvas.addEventListener("touchstart", evento => {
    // Previene el comportamiento predeterminado para evitar el desplazamiento de la página
    evento.preventDefault();

    // Solo se ha iniciado el toque, así que dibujamos un punto
    xAnterior = xActual;
    yAnterior = yActual;
    xActual = obtenerXReal(evento.touches[0].clientX);
    yActual = obtenerYReal(evento.touches[0].clientY);
    contexto.beginPath();
    contexto.fillStyle = COLOR_PINCEL;
    contexto.fillRect(xActual, yActual, GROSOR, GROSOR);
    contexto.closePath();
    // Establecemos la bandera
    haComenzadoDibujo = true;
});


$canvas.addEventListener("touchmove", evento => {
    if (!haComenzadoDibujo) {
        return;
    }

    // Previene el comportamiento predeterminado para evitar el desplazamiento de la página
    evento.preventDefault();

    // El dedo se está moviendo, así que dibujamos
    xAnterior = xActual;
    yAnterior = yActual;
    xActual = obtenerXReal(evento.touches[0].clientX);
    yActual = obtenerYReal(evento.touches[0].clientY);
    contexto.beginPath();
    contexto.moveTo(xAnterior, yAnterior);
    contexto.lineTo(xActual, yActual);
    contexto.strokeStyle = COLOR_PINCEL;
    contexto.lineWidth = GROSOR;
    contexto.stroke();
    contexto.closePath();
});

["touchend", "touchcancel"].forEach(nombreDeEvento => {
    $canvas.addEventListener(nombreDeEvento, () => {
        haComenzadoDibujo = false;
    });
});

const limpiarCanvas = () => {
    // Colocar color blanco en fondo de canvas
    contexto.fillStyle = COLOR_FONDO;
    contexto.fillRect(0, 0, $canvas.width, $canvas.height);
};
limpiarCanvas();
$btnLimpiar.onclick = limpiarCanvas;

const guardarFirma = () => {

    // Obtener el valor del input
    const repdia_recib = document.getElementById('repdia_recib').value;
    // Convertir el canvas a una imagen en formato Base64
    const dataURL = $canvas.toDataURL('image/png');

    // Enviar la imagen al servidor usando AJAX
    $.ajax({
        url: '../../controller/VerReporteDiario.php?op=guardarFirma',  // La URL del endpoint en tu servidor
        type: 'POST',            // El método HTTP que estás usando
        data: {
            image: dataURL,     // El objeto que contiene la imagen en Base64
            repdia_recib: repdia_recib       
        },
        success: function(response) {
            console.log('Firma guardada con éxito:', response);
            $("#modalFirma").modal("hide");
            $('#repdia_data').DataTable().ajax.reload();
            swal({
                title: "Correcto",
                text: "Datos enviados correctamente",
                type: "success",
                confirmButtonClass: "btn-success",
              });
              // Limpiar el canvas
            limpiarCanvas();
        },
        error: function(xhr, status, error) {
            console.error('Error al guardar la firma:', error);
        }
    });
};
$btnGuardar.onclick = guardarFirma;
// Escuchar clic del botón para descargar el canvas
$btnDescargar.onclick = () => {
    const enlace = document.createElement('a');
    // El título
    enlace.download = "Firma.png";
    // Convertir la imagen a Base64 y ponerlo en el enlace
    enlace.href = $canvas.toDataURL();
    // Hacer click en él
    enlace.click();
};
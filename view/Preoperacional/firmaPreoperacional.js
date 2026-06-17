(function (window, $) {
    "use strict";

    const canvas = document.getElementById("preFirmaCanvas");
    const btnLimpiar = document.getElementById("btnPreFirmaLimpiar");
    const btnGuardar = document.getElementById("btnPreFirmaGuardar");
    const modal = $("#modalFirmaPreoperacional");

    if (!canvas || !btnLimpiar || !btnGuardar || !modal.length) {
        return;
    }

    const contexto = canvas.getContext("2d");
    const COLOR_PINCEL = "black";
    const COLOR_FONDO = "white";
    const GROSOR = 2;

    let dibujando = false;
    let tieneFirma = false;
    let callbackEnvio = null;
    let xAnterior = 0;
    let yAnterior = 0;

    function mostrarError(mensaje) {
        if (typeof swal === "function") {
            swal({
                title: "Firma requerida",
                text: mensaje,
                type: "warning",
                confirmButtonClass: "btn-warning"
            });
            return;
        }

        alert(mensaje);
    }

    function limpiarCanvas() {
        contexto.fillStyle = COLOR_FONDO;
        contexto.fillRect(0, 0, canvas.width, canvas.height);
        tieneFirma = false;
    }

    function obtenerCoordenadas(evento) {
        const rect = canvas.getBoundingClientRect();
        const punto = evento.touches ? evento.touches[0] : evento;
        const escalaX = canvas.width / rect.width;
        const escalaY = canvas.height / rect.height;

        return {
            x: (punto.clientX - rect.left) * escalaX,
            y: (punto.clientY - rect.top) * escalaY
        };
    }

    function iniciarDibujo(evento) {
        evento.preventDefault();
        const punto = obtenerCoordenadas(evento);

        dibujando = true;
        tieneFirma = true;
        xAnterior = punto.x;
        yAnterior = punto.y;

        contexto.beginPath();
        contexto.fillStyle = COLOR_PINCEL;
        contexto.fillRect(punto.x, punto.y, GROSOR, GROSOR);
        contexto.closePath();
    }

    function dibujar(evento) {
        if (!dibujando) {
            return;
        }

        evento.preventDefault();
        const punto = obtenerCoordenadas(evento);

        contexto.beginPath();
        contexto.moveTo(xAnterior, yAnterior);
        contexto.lineTo(punto.x, punto.y);
        contexto.strokeStyle = COLOR_PINCEL;
        contexto.lineWidth = GROSOR;
        contexto.lineCap = "round";
        contexto.stroke();
        contexto.closePath();

        xAnterior = punto.x;
        yAnterior = punto.y;
    }

    function finalizarDibujo(evento) {
        if (evento) {
            evento.preventDefault();
        }
        dibujando = false;
    }

    function guardarFirma() {
        if (!tieneFirma) {
            mostrarError("Debe registrar la firma del conductor/operador antes de enviar.");
            return;
        }

        const firma = canvas.toDataURL("image/png");
        const enviar = callbackEnvio;

        callbackEnvio = null;
        modal.modal("hide");

        if (typeof enviar === "function") {
            enviar(firma);
        }
    }

    canvas.addEventListener("mousedown", iniciarDibujo);
    canvas.addEventListener("mousemove", dibujar);
    canvas.addEventListener("mouseup", finalizarDibujo);
    canvas.addEventListener("mouseleave", finalizarDibujo);
    canvas.addEventListener("touchstart", iniciarDibujo, { passive: false });
    canvas.addEventListener("touchmove", dibujar, { passive: false });
    canvas.addEventListener("touchend", finalizarDibujo, { passive: false });
    canvas.addEventListener("touchcancel", finalizarDibujo, { passive: false });

    btnLimpiar.onclick = limpiarCanvas;
    btnGuardar.onclick = guardarFirma;

    modal.on("hidden.bs.modal", function () {
        callbackEnvio = null;
    });

    window.PreoperacionalFirma = {
        solicitar: function (callback) {
            callbackEnvio = callback;
            limpiarCanvas();
            modal.modal("show");
        }
    };
})(window, jQuery);

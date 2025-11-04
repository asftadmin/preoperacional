function init(){
    $("#cambioEstado_form").on("submit", function(e){
        guardaryeditar(e);
        console.log("click");
    });
}

/* LLamamos al Modal id del boton y id del modal  */
function estado(alista_id) {
    $('#mdltitulo').html('Evaluar Alistamiento');
    $('#lblprecalificar').html('Validar: *');
    $('#pre_calificar').html('<select class="form-control select2bs4" id="vehi_estado" name="vehi_estado"><option selected disabled>--Seleccione el Estado--</option><option value="operativo" id="Alistado">OPERATIVO</option><option value="no funcional" id="Nofuncional">NO FUNCIONAL</option></select>');

    $.post("../../controller/VerAlistamiento.php?op=mostraralista", { alista_id : alista_id}, function(data) { 
        data = JSON.parse(data);
        console.log(data);
        $('#alista_id').val(data.alista_id);
        $('#alista_codigo').val(data.alista_codigo);
        if (data.alista_estado == 'Alistado') {
            $('#Alistado').prop('selected', true);
        }
    }); 
    $('#cambioEstado').modal('show');
}
       
function guardaryeditar(e){
    e.preventDefault();
    let formData = new FormData($("#cambioEstado_form")[0]);
    formData.append("alista_id", $("#alista_id").val());
    formData.append("alista_codigo", $("#alista_codigo").val());
    formData.append("vehi_estado", $("#vehi_estado").val());
    /*URL DEL CONTROLADOR OP INSERT - ROL */
    
    $.ajax({
        url:"../../controller/VerAlistamiento.php?op=cambioestadoHM",
        type: "POST",
        data: formData,
        contentType:false,
        processData:false,
        success: function(datos) {
           console.log(datos);
            $('#cambioEstado_form')[0].reset();
            $('#cambioEstado').modal('hide');
            $('#detalle_data').DataTable().ajax.reload();
                swal({
                    title: "Correcto", 
                    text: "Datos enviados correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success",
                });
        }
    });
}
init();
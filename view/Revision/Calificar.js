function init(){
    $("#calificar_form").on("submit", function(e){
        guardaryeditar(e);
        console.log("click");
    });
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
       
function guardaryeditar(e){
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
            $('#pre_data').DataTable().ajax.reload();
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
init();
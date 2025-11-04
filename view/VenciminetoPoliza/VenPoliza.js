
$(document).on("click","#btnenviar", function(){
    var vehi_id = $("#vehi_id").val();
    console.log(vehi_id);
    $("body").css("overflow", "hidden");
    if(vehi_id == 0){
          swal({
            title: "!Campos Vacios!", 
            text: "Por favor, asegúrate de completar el campo antes de continuar.",
            type: "info",
            confirmButtonClass: "btn-info",
         
        }).then(function() {
            // Reactivar el desplazamiento del cuerpo después de que se cierre el SweetAlert
            $("body").css("overflow", "auto");
          }); 
        
    }else{ 
                $.post("../../controller/VenciminetoPoliza.php?op=vencimiento_poliza", {vehi_id : vehi_id}, function(data){
                    console.log(data);
                });
                swal({
                    title: "¡Correcto!", 
                    text: "Se ha enviado un correo electrónico con la alerta de vencimineto de la poliza.",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });
              
        }
    });

$.post("../../controller/Vehiculo.php?op=comboVehiculoPreop",function(data, status){
    $('#vehi_id').html(data);
});
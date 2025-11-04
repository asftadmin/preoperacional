
$(document).on("click","#btnenviar", function(){
    var user_email = $("#user_email").val();
    $("body").css("overflow", "hidden");
    if(user_email == 0){
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
    $.post ("../../controller/Usuario.php?op=Email", {user_email:user_email}, function(data){
            if(data.trim() == "Existe"){
                $.post("../../controller/Email.php?op=recuperar_contrasena", {user_email : user_email}, function(data){
                    console.log(data);
                });
                swal({
                    title: "¡Correcto!", 
                    text: "Se ha enviado un correo electrónico con la nueva contraseña.",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });
            }else if(data.trim() == "Error"){
                swal({
                    title: 'Error', 
                    text: "El correo electrónico no ha sido encontrado.",
                    type: "error",
                    button: "Aceptar",
                    confirmButtonClass: "btn-danger",
                });    
            }
        });
     }   
});
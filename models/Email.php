<?php

require '../include/vendor/autoload.php';
require('class.phpmailer.php');
include("class.smtp.php");
require_once("../config/conexion.php");
require_once("../models/Usuario.php");

class Email extends PHPMailer{

    protected $gCorreo = 'correoasft@gmail.com'; /* CORREO QUE SE ENVIA  */
    protected $gContrasena= 'wcnqiljxulvqafxz'; 

    /* FUNCION PARA RECUPERAR LA CONTRASEÑA*/ 
    public function recuperar_contrasena($user_email){
        $usuario = new Usuario();
        $usuario -> get_user_cambiar_pass($user_email); //FUNCION PARA CAMBIAR LA CONTRASEÑA 
        $datos = $usuario->get_user_email($user_email); //FUNCION PARA SELECCIONAR EL CORREO ELECTRONICO DEL USUARIO
        foreach($datos as $row){
            $user_id = $row["user_id"];
            $user_nombre = $row["user_nombre"];
            $user_apellidos = $row["user_apellidos"];
            $user_email = $row["user_email"];
            $user_contrasena = $row["user_contrasena"];
        }
        $this -> isSMTP();
        $this -> Host = 'smtp.gmail.com';  /* USO DEL PROTOCOLO SMTP PARA EL ENVIO DEL CORREO */ 
        $this -> Port = 587;    
        $this -> SMTPAuth = true;
        $this -> SMTPSecure = 'tls';

        $this -> Username = $this -> gCorreo;
        $this -> Password = $this -> gContrasena;
        $this -> setFrom ($this -> gCorreo, "Recuperar Contraseña"); 

        $this -> CharSet = 'utf8';
        $this -> addAddress($user_email);
        $this -> isHTML(true);
        $this -> Subject = "Cambio de Contraseña Exitoso - Asfaltart S.A.S";

        $cuerpo = file_get_contents('../public/RecuperarContrasena.html');  /* CONTENIDO DEL CORREO  */
        $cuerpo = str_replace("xusernom", $user_nombre, $cuerpo); 
        $cuerpo = str_replace("xuserape", $user_apellidos, $cuerpo); 
        
        $cuerpo = str_replace("xnuevopass", $user_contrasena, $cuerpo);
        $this -> Body = $cuerpo;
        $this -> AltBody = strip_tags("Recuperar Contraseña");

        try{
            $this -> Send();
            $usuario-> get_user_encriptar_nuevo_pass($user_id,$user_contrasena); /* FUNCION PARA ENCRIPTAR LA NUEVA CONTRASEÑA */
            return true;
        }catch(Exception $e){
            return false;
        }

    }
} 

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
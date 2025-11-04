<?php

require '../include/vendor/autoload.php';
require('class.phpmailer.php');
include("class.smtp.php");
require_once("../config/conexion.php");

class Email_RD extends PHPMailer{

    protected $gCorreo = 'correoasft@gmail.com'; /* CORREO QUE SE ENVIA  */
    protected $gContrasena= 'wcnqiljxulvqafxz'; 

    /* FUNCION PARA RECUPERAR LA CONTRASEÑA*/ 
    public function envia_acumulado($user_email, $var1, $var2, $var3){

        $link = "http://localhost/preoperacional/ReportesDiarios.php/Acumulado.php?var1={$var1}&var2={$var2}&var3={$var3}";
        
        $this -> isSMTP();
        $this -> Host = 'smtp.gmail.com';  /* USO DEL PROTOCOLO SMTP PARA EL ENVIO DEL CORREO */ 
        $this -> Port = 587;    
        $this -> SMTPAuth = true;
        $this -> SMTPSecure = 'tls';

        $this -> Username = $this -> gCorreo;
        $this -> Password = $this -> gContrasena;
        $this -> setFrom ($this -> gCorreo, "Acumulado Reportes Diarios"); 

        $this -> CharSet = 'utf8';
        $this -> addAddress($user_email);
        $this -> isHTML(true);
        $this -> Subject = "Envio de Reportes Exitoso - Asfaltart S.A.S";

        // Encapsular el link con el texto "ingrese aquí"
        $enlace = "<a href='{$link}' style='color: blue; text-decoration: none;'>Ingrese aquí</a>";
        $cuerpo = file_get_contents('../public/ReporteDiario.html');  /* CONTENIDO DEL CORREO  */
        $cuerpo = str_replace("xlink", $enlace, $cuerpo); 
         
        
        $this -> Body = $cuerpo;
        $this -> AltBody = strip_tags("Reporte Diario");
        return $this -> send();

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
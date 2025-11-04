<?php

require '../include/vendor/autoload.php';
require('class.phpmailer.php');
include("class.smtp.php");
require_once("../config/conexion.php");
require_once("../models/Usuario.php");
require_once("../models/Vehiculo.php");

class VenciminetoPoliza extends PHPMailer{

    protected $gCorreo = 'correoasft@gmail.com'; /* CORREO QUE SE ENVIA  */
    protected $gContrasena= 'wcnqiljxulvqafxz'; 

    /* FUNCION PARA ALERTA D VENCIMIENTO DE LA POLIZA*/ 
    public function vencimineto_poliza($vehi_id){
        $vehiculo = new Vehiculo();
        $datos = $vehiculo->get_car_poliza_email($vehi_id); //FUNCION PARA SELECCIONAR EL CORREO ELECTRONICO DEL USUARIO
        foreach($datos as $row){
            $vehi_id = $row["vehi_id"];
            $vehi_placa = $row["vehi_placa"];
            $vehi_poliza_vence = $row["vehi_poliza_vence"];
        }
        $this -> isSMTP();
        $this -> Host = 'smtp.gmail.com';  /* USO DEL PROTOCOLO SMTP PARA EL ENVIO DEL CORREO */ 
        $this -> Port = 587;    
        $this -> SMTPAuth = true;
        $this -> SMTPSecure = 'tls';

        $this -> Username = $this -> gCorreo;
        $this -> Password = $this -> gContrasena;
        $this -> setFrom ($this -> gCorreo, "Alerta Vencimineto Poliza"); 

        $this -> CharSet = 'utf8';
        $this -> addAddress("jdborjarueda@gmail.com");
        $this -> addAddress("coordinadorsst@asfaltart.com");
        $this -> isHTML(true);
        $this -> Subject = "Envio de alerta Exitoso - Asfaltart S.A.S";

        $cuerpo = file_get_contents('../public/VencimientoPoliza.html');  /* CONTENIDO DEL CORREO  */ 
        $cuerpo = str_replace("xidVehiculo", $vehi_id, $cuerpo);
        $cuerpo = str_replace("xvehiPlaca", $vehi_placa, $cuerpo);
        $cuerpo = str_replace("xfechavenPoliza", $vehi_poliza_vence, $cuerpo);
        $this -> Body = $cuerpo;
        $this -> AltBody = strip_tags("Vencimineto Poliza");
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
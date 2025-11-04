<?php
    require_once ("../config/conexion.php");
    require_once ("../models/Email.php");

    $email = new Email();

    switch ($_GET["op"]){
        case "recuperar_contrasena":
            $email -> recuperar_contrasena($_POST["user_email"]);
            break;
    }

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
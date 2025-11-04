<?php
require_once("../config/conexion.php");
require_once("../models/Email_RD.php");

$email_RD = new Email_RD();

switch ($_GET["op"]) {

    case "envio_acumulado":

        $var1 = $_POST["fecha_inicio"];
        $var2 = $_POST["fecha_final"];
        $var3 = $_POST["repdia_vehi"];

        $email_RD->envia_acumulado($_POST["user_email"], $var1, $var2, $var3);
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
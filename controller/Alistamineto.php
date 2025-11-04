<?php
// Resto de tu código
/* CONTROLADOR PREOPERACIONAL  - FORMULARIO */

require_once("../config/conexion.php");
require_once("../models/Alistamineto.php");

$Alistamiento = new Alistamiento();

switch ($_GET["op"]) {

     /* GUARDAR */
     case 'guardar':
        if (isset($_POST['alista_inspec'])) {
            $inspector = $_POST["alista_inspec"];
            $residente = $_POST["alista_residente"];
            $fecha = date('Ymd');
            $obra = $_POST["alista_obras"];
            $equipo = $_POST["alista_vehi"];
            $observaciones = $_POST["alista_observaciones"];
            $codigo = $fecha.$obra.$inspector;

            $Alistamiento->guardar_preguntas($obra,$inspector, $residente,$observaciones,$codigo,$equipo);

            echo json_encode(array("status" => "success", "message" => "Se envio correctamente"));
        } else {
        }
        break;
        
}


?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>

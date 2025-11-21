<?php

require_once("../config/conexion.php");
require_once("../models/ReportesDiarios.php");

//require_once("../models/Whatsapp.php");
//$whatsapp = new Whatsapp();

$reportes_diarios = new ReportesDiarios();

switch ($_POST["opcion"]) {

    case 'guardar_respuestas':
        /* TRAEMOS LOS DATOS */
        $repdia_fech = date('Ymd');
        $vehi_placa = $_POST['repdia_vehi'];
        $repdia_no = $_POST['repdia_cond'];
        $repdia_mtprima = isset($_POST['repdia_mtprima']) ? $_POST['repdia_mtprima'] : null;
        $repdia_ca = isset($_POST['repdia_ca']) ? $_POST['repdia_ca'] : null;
        $repdia_num_viajes = isset($_POST['repdia_num_viajes']) ? $_POST['repdia_num_viajes'] : null;
        $repdia_km_hm = isset($_POST['repdia_km_hm']) && $_POST['repdia_km_hm'] !== '' ? (int)$_POST['repdia_km_hm'] : null;
        $repdia_recib = $repdia_fech . $repdia_no;
        $repdia_placa = $repdia_fech . $vehi_placa;
        $repdia_recib = trim($repdia_recib);
        /* SI EL FORMULARIO DE ESE DIA YA SE ENCUENTRA REGISTRADO */
        $repdia_id = $_POST["repdia_id"];
        if (empty($repdia_id) && $reportes_diarios->repExiste($_POST["repdia_recib"])) {
            echo json_encode(array("status" => "errores", "message" => "El Reporte ya se encuentra cerrado"));
        } else {
            if (empty($repdia_id)) {
                $reportes_diarios->guardar_preguntas(
                    $_POST['repdia_cond'],
                    $_POST['repdia_vehi'],
                    $_POST['repdia_actv'],
                    $_POST['repdia_volu'],
                    $repdia_recib,
                    $_POST['repdia_gaso'],
                    $_POST['repdia_acpm'],
                    $_POST['repdia_acet_moto'],
                    $_POST['repdia_acet_hidr'],
                    $_POST['repdia_acet_tram'],
                    $_POST['repdia_acet_gras'],
                    $_POST['repdia_kilo'],
                    $_POST['repdia_estado'],
                    $repdia_placa,
                    $_POST['repdia_observa'],
                    $_POST['repdia_obras'],
                    $_POST['repdia_kilo_final'],
                    $_POST['repdia_puntas'],
                    $repdia_mtprima,
                    $_POST['repdia_residente'],
                    $_POST['repdia_inspec'],
                    $repdia_ca,
                    $repdia_km_hm,
                    $repdia_num_viajes
                );
            }
            echo json_encode(array("status" => "success", "message" => "Se envio correctamente"));
        }
        break;

    case 'listarKilo_Horo':
        if (isset($_POST['action']) && $_POST['action'] == 'cargar_preguntas') {
            $datos =  $reportes_diarios->combo_actividades($_POST['tipo_id']);
            $obras_asfl =  $reportes_diarios->combo_obras_asfl();
            $obras_cnct =  $reportes_diarios->combo_obras_cnct();
            $materia_prima =  $reportes_diarios->get_mtprm_combo();
            // GENERAR EL HTML DE LAS PREGUNTAS 
            $html = '';

            if ($_POST["tipo_id"] == 2 || $_POST["tipo_id"] == 5 || $_POST["tipo_id"] == 6 || $_POST["tipo_id"] == 7 || $_POST["tipo_id"] == 8 || $_POST["tipo_id"] == 9 || $_POST["tipo_id"] == 11 || $_POST["tipo_id"] == 12 || $_POST["tipo_id"] == 14 || $_POST["tipo_id"] == 15 || $_POST["tipo_id"] == 16 || $_POST["tipo_id"] == 17 || $_POST["tipo_id"] == 20) {

                $html .= '<div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"  for="repdia_kilo">Horometro:</label>&nbsp;&nbsp;
                                        <input class="form" type="text" id="repdia_kilo"  name="repdia_kilo"   placeholder="Horometro Incial" maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                    </div>
                                </div>
                            </div>';
            } else {
                $html .= '<div class="row mt-3 ">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"  for="repdia_kilo">Kilometraje:</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input class="form" type="text" id="repdia_kilo"  name="repdia_kilo"   placeholder="Kilometraje Incial" maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                    </div>
                                </div>
                            </div>';
            }
            $html .= "<div class='form-group d-flex align-items-center'>
                <label for='repdia_actv' class='mr-2'>Actividad:</label>
            <select class='form-control select2' id='repdia_actv' name='repdia_actv' style='width: 100%;' required >";
            $html .= "<option value='' disabled selected>--Selecciona una Actividad--</option>";
            foreach ($datos as $row) {

                $html .= "
                    <option value='" . $row['act_id'] . "'>" . $row['act_nombre'] . "</option>";
            }
            $html .= "</select>
                    </div>";

            if ($_POST["tipo_id"] == 2 || $_POST["tipo_id"] == 5 || $_POST["tipo_id"] == 6 || $_POST["tipo_id"] == 7 || $_POST["tipo_id"] == 8 || $_POST["tipo_id"] == 9 || $_POST["tipo_id"] == 12 || $_POST["tipo_id"] == 14 || $_POST["tipo_id"] == 15 || $_POST["tipo_id"] == 19 || $_POST["tipo_id"] == 17 || $_POST["tipo_id"] == 16 || $_POST["tipo_id"] == 20) {
                $html .= '
                    <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="repdia_volu">Volumen:</label>&nbsp;&nbsp;
                                        <input class="form" type="text" id="repdia_volu"  name="repdia_volu"   placeholder="Volumen" value=0 maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                <input class="form" type="hidden" id="repdia_puntas"  name="repdia_puntas"  placeholder="Puntas" value=0 maxlength="10" required>
                                    </div>
                                </div>
                            </div> ';
            } else if ($_POST["tipo_id"] == 4) {
                $html .= '
                    <div class="row mt-4 text-center">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" style=" width: 100%; max-width: 200px;" for="repdia_volu">Volumen/Equipos:</label>
                                        <input class="form" type="text" id="repdia_volu"  name="repdia_volu"   placeholder="Volumen" value=0 maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input class="form" type="hidden" id="repdia_puntas"  name="repdia_puntas"  placeholder="Puntas" value=0 maxlength="10" required>
                                    </div>
                                    </div>
                                </div>
                            </div> ';
            } else if ($_POST["tipo_id"] == 11) {
                $html .= '
                    <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"  for="repdia_volu">Volumen:</label>&nbsp;&nbsp;
                                        <input class="form" type="text" id="repdia_volu"  name="repdia_volu"   placeholder="Volumen" value=0 maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                    <label class="form-label" style="  for="repdia_puntas">Puntas:</label>&nbsp;&nbsp;
                                        <input class="form" type="text" id="repdia_puntas"  name="repdia_puntas"  placeholder="Puntas" value=0 maxlength="10" required>
                                    </div>
                                </div>
                            </div> ';
            } else if ($_POST["tipo_id"] == 1) {
                $html .= '
                    <div class="form-group d-flex align-items-center mb-3" id="input_extra_act">
                        <!-- Aquí el JS insertará el campo ID 4 si aplica -->
                    </div>

                    <div class="form-group d-flex align-items-center mb-3">
                        <label for="repdia_volu" class="mr-2 mb-0" style="min-width: 90px;">Volumen:</label>
                        <input class="form-control" type="text" id="repdia_volu" name="repdia_volu" placeholder="Volumen" value="0" maxlength="10" style="max-width: 200px;">
                        <input type="hidden" id="repdia_puntas" name="repdia_puntas" value="0">
                    </div>

                    <div class="form-group d-flex align-items-center mb-3">
                        <label for="repdia_mtprima" class="mr-2 mb-0" style="min-width: 90px;">Material:</label>
                        <select class="form-control select2" id="repdia_mtprima" name="repdia_mtprima" style="max-width: 300px;">
                            <option value="" disabled selected>--Selecciona un Material--</option>';
                foreach ($materia_prima as $row) {
                    $html .= "<option value='" . $row['mtprm_id'] . "'>" . $row['mtprm_nombre'] . "</option>";
                }
                $html .= '
                        </select>
                    </div>';
            } else {
                $html .= '<div class="row mt-4 text-center">
                <div class="col-md-12">
                    <div class="form-group">
                        <input class="form" type="hidden" id="repdia_volu"  name="repdia_volu"   placeholder="Volumen" value=0 maxlength="10" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <input class="form" type="hidden" id="repdia_puntas"  name="repdia_puntas"  placeholder="Puntas" value=0 maxlength="10" required>
                    </div>
                </div>
                </div> ';
            }
            if ($_POST["tipo_id"] == 1 || $_POST["tipo_id"] == 3 || $_POST["tipo_id"] == 4 || $_POST["tipo_id"] == 5 || $_POST["tipo_id"] == 6 || $_POST["tipo_id"] == 7 || $_POST["tipo_id"] == 8 || $_POST["tipo_id"] == 9 || $_POST["tipo_id"] == 11 || $_POST["tipo_id"] == 12 || $_POST["tipo_id"] == 14 || $_POST["tipo_id"] == 15 || $_POST["tipo_id"] == 19 || $_POST["tipo_id"] == 21) {
                // EXCEPCIÓN: Si es tipo 3 y el vehículo es el ID 196, entonces debe cargar las obras de concreto
                if ($_POST["tipo_id"] == 3 && isset($_POST["vehi_id"]) && $_POST["vehi_id"] == 196) {
                    $obras = $obras_cnct; // Asignamos las obras de concreto
                } else {
                    $obras = $obras_asfl; // Asignamos las obras de asfalto para los demás
                }
            } else if ($_POST["tipo_id"] == 2 || $_POST["tipo_id"] == 16 || $_POST["tipo_id"] == 17 || $_POST["tipo_id"] == 20) {
                $obras = $obras_cnct; // Directamente obras de concreto para estos tipos de vehículo
            }

            // Generamos el select de obras
            $html .= "<div class='form-group d-flex align-items-center'>
                        <label for='repdia_obras' class='mr-2'>Obras:</label>
                        <select class='form-control select2' id='repdia_obras' name='repdia_obras' style='width: 100%;' required>";
            $html .= "<option value='' disabled selected>--Selecciona una Obra--</option>";

            foreach ($obras as $row) {
                $html .= "<option value='" . $row['obras_id'] . "'>" . $row['obras_nom'] . "</option>";
            }

            $html .= "</select></div>";

            $html .= "</div></div>";

            echo $html;
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
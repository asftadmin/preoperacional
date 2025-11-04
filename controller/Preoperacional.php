

<?php
// Resto de tu código
/* CONTROLADOR PREOPERACIONAL  - FORMULARIO */

require_once("../config/conexion.php");
require_once("../models/Preoperacional.php");

$preoperacional = new Preoperacional();

switch ($_POST["opcion"]) {


    case 'listarPreguntas':
        if (isset($_POST['action']) && $_POST['action'] == 'cargar_preguntas') {
            $preguntas = $preoperacional->listar_preguntas($_POST['tipo_id']);

            // GENERAR EL HTML DE LAS PREGUNTAS CON LOS ITEMS - OPERACIONES Y SUBOPERACIONES
            $html = '';
            foreach ($preguntas as $pregunta) {
                $html .= '<div class="row justify-content-center">';
                $html .= '<div class="col-sm-3 border-div">';
                $html .= '<input  type="hidden"  name="' . $pregunta['oper_id'] . '" value="' . $pregunta['oper_id'] . '"> ';
                $html .= '<p>' . $pregunta['oper_nombre'] . '</p>';
                $html .= '</div>';
                $html .= '<div class="col-sm-3 border-div">';
                $html .= '<input  type="hidden"  name="' . $pregunta['suboper_id'] . '" value="' . $pregunta['suboper_id'] . '">';
                $html .= '<p>' . $pregunta['suboper_nombre'] . '</p>';
                $html .= '</div>';
                $html .= '<div class="opciones">';
                $html .= '<p class="conteFlex">';
                $html .= '<span class="border-div">';
                $html .= '<input  type="radio" id="idResp_' . $pregunta['suboper_id'] . 'B' . '" class="opcion-radio" name="respuesta_' . $pregunta['suboper_id'] . '" value="B" >';
                $html .= '<label for="idResp_' . $pregunta['suboper_id'] . 'B' . '" class="radio-label">B</label>';
                $html .= '<input type="radio" id="idResp_' . $pregunta['suboper_id'] . 'M' . '" class="opcion-radio" name="respuesta_' . $pregunta['suboper_id'] . '" value="M">';
                $html .= '<label for="idResp_' . $pregunta['suboper_id'] . 'M' . '" class="radio-label">M</label>';
                $html .= '<input  type="radio" id="idResp_' . $pregunta['suboper_id'] . 'NA' . '" class="opcion-radio" name="respuesta_' . $pregunta['suboper_id'] . '" value="NA" >';
                $html .= '<label for="idResp_' . $pregunta['suboper_id'] . 'NA' . '" class="radio-label">N/A</label>';
                $html .= '</span>';
                $html .= '</p>';
                $html .= '</div>';
                $html .= '</div>';
            }

            if ($_POST["tipo_id"] == 2 || $_POST["tipo_id"] == 5 || $_POST["tipo_id"] == 6 || $_POST["tipo_id"] == 7 || $_POST["tipo_id"] == 8 || $_POST["tipo_id"] == 9 || $_POST["tipo_id"] == 11 || $_POST["tipo_id"] == 12 || $_POST["tipo_id"] == 14 || $_POST["tipo_id"] == 15 || $_POST["tipo_id"] == 16 || $_POST["tipo_id"] == 17 || $_POST["tipo_id"] == 20 || $_POST["tipo_id"] == 21) {

                $html .= '<div class="row mt-4 text-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" style=" width: 100%; max-width: 200px;" for="pre_kilometraje_inicial">Horometro Incial:</label>
                                    <input class="form" type="text" id="pre_kilometraje_inicial"  name="pre_kilometraje_inicial"   placeholder="Horometro Incial" maxlength="10" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                </div>
                            </div>
                        </div>';
            } else {

                $html .= '<div class="row mt-4 text-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" style=" width: 100%; max-width: 200px;" for="pre_kilometraje_inicial">Kilometraje Incial:</label>
                                    <input class="form" type="text" id="pre_kilometraje_inicial"  name="pre_kilometraje_inicial"   placeholder="Kilometraje Incial" maxlength="10" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                </div>
                            </div>
                        </div>';
            }
            $html .= '<div class="row justify-content-center mt-3">';
            $html .= '<button type="button" id="btnmodal" class="btn btn-info" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="showPlate()">Formulario Fallas</button>';
            $html .= '</div>';
            $html .= '<div class="row justify-content-center mt-3">';
            $html .= '<h5 style="font-weight: bold;">Observaciones</h5>';
            $html .= '</div>';
            $html .= '<div class="row justify-content-center"><textarea id="pre_observaciones" class="textarea"  style="resize: none;" name="pre_observaciones" rows="4" cols="100" placeholder="Escribe Aquí..."  autocapitalize="sentences" spellcheck="true" maxlength="255"></textarea></div>';
            $html .= '<br><div class="row justify-content-center"><h6><i>El Espirítu de las Grandes Obras</i></h6></div><br><br>';
            $html .= '<div class="text-center" style="height: 5px;">
                <!-- Button trigger modal -->
                    <input type="submit" id="preo_enviar" class="btn btn-info" value="Enviar">
                </div>';
            echo $html;
        }
        break;

    case 'guardar_respuestas':
        /* TRAEMOS LOS DATOS */
        $tipo_id = $_POST['tipo_id'];
        $pre_placa = $_POST['vehi_placa'];
        $pre_id = $_POST['select_placa'];
        $pre_observaciones = $_POST['pre_observaciones'];
        $pre_kilometraje_inicial = $_POST['pre_kilometraje_inicial'];
        $pre_fecha = date('Ymd'); //date('Ymd')
        $pre_formulario = $pre_fecha . $pre_placa;
        $pre_formulario = trim($pre_formulario);
        $pre_usuario = $_POST['user_id'];
        // CARGA LAS PREGUTNAS SEGUN EL TIPO DE VEHICULO
        $preguntas = $preoperacional->listar_preguntas($tipo_id);

        /* SI EL FORMULARIO DE ESE DIA YA SE ENCUENTRA REGISTRADO */
        if ($preoperacional->preExiste($pre_formulario)) {
            echo json_encode(array("status" => "errores", "message" => "El Formulario ya se encuentra registrado"));
        } else {
            /* GUARDA LAS RESPUESTAS */
            foreach ($preguntas as $pregunta) {
                $suboper_id = $pregunta['suboper_id'];
                $respuesta = $_POST['respuesta_' . $suboper_id];
                $preoperacional->guardar_preguntas($pre_id, $pre_observaciones, $suboper_id, $pre_formulario, $respuesta, $pre_kilometraje_inicial, $pre_usuario);
            }
            echo json_encode(array("status" => "success", "message" => "Se envio correctamente"));
        }
        break;

        /* GRAFICO KILOMETRAJE */
    case 'grafico':

        $datos = $preoperacional->get_kilometraje_grafico($_POST['vehi_placax']);
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        break;

    case 'listarCheckeo':
        if (isset($_POST['action']) && $_POST['action'] == 'cargar_check') {
            $preguntas = $preoperacional->listar_preguntas($_POST['tipo_id']);

            // GENERAR EL HTML DE LAS PREGUNTAS CON LOS ITEMS - OPERACIONES Y SUBOPERACIONES
            $html = '';
            foreach ($preguntas as $pregunta) {
                $html .= '<div class="row justify-content-center">';
                $html .= '<div class="col-sm-3 border-div">';
                $html .= '<input  type="hidden"  name="' . $pregunta['oper_id'] . '" value="' . $pregunta['oper_id'] . '"> ';
                $html .= '<p>' . $pregunta['oper_nombre'] . '</p>';
                $html .= '</div>';
                $html .= '<div class="col-sm-3 border-div">';
                $html .= '<input  type="hidden"  name="' . $pregunta['suboper_id'] . '" value="' . $pregunta['suboper_id'] . '">';
                $html .= '<p>' . $pregunta['suboper_nombre'] . '</p>';
                $html .= '</div>';
                $html .= '<div class="opciones">';
                $html .= '<p class="conteFlex">';
                $html .= '<span class="border-div">';
                $html .= '<input  type="radio" id="idResp_' . $pregunta['suboper_id'] . 'B' . '" class="opcion-radio" name="respuesta_' . $pregunta['suboper_id'] . '" value="C" >';
                $html .= '<label for="idResp_' . $pregunta['suboper_id'] . 'B' . '" class="radio-label">C</label>';
                $html .= '<input type="radio" id="idResp_' . $pregunta['suboper_id'] . 'M' . '" class="opcion-radio" name="respuesta_' . $pregunta['suboper_id'] . '" value="N/C">';
                $html .= '<label for="idResp_' . $pregunta['suboper_id'] . 'M' . '" class="radio-label">N/C</label>';
                $html .= '</span>';
                $html .= '</p>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '<input class="form" type="hidden" id="pre_kilometraje_inicial"  name="pre_kilometraje_inicial"  value=0 required>';
            $html .= '<div class="row justify-content-center mt-3">';
            $html .= '<h5 style="font-weight: bold;">Observaciones</h5>';
            $html .= '</div>';
            $html .= '<div class="row justify-content-center"><textarea id="pre_observaciones" class="textarea"  style="resize: none;" name="pre_observaciones" rows="4" cols="100" placeholder="Escribe Aquí..."  autocapitalize="sentences" spellcheck="true" maxlength="255"></textarea></div>';
            $html .= '<br><div class="row justify-content-center"><h6><i>El Espirítu de las Grandes Obras</i></h6></div><br><br>';
            $html .= '<div class="text-center" style="height: 5px;">
                <!-- Button trigger modal -->
                    <input type="submit" id="preo_enviar" class="btn btn-info" value="Enviar">
                </div>';
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
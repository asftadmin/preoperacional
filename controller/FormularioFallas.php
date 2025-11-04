

<?php
// Resto de tu código
    /* CONTROLADOR PREOPERACIONAL  - FORMULARIO */

    require_once("../config/conexion.php");
    require_once("../models/FormularioFallas.php");

    $FormularioFallas = new FormularioFallas();

    switch ($_POST["opcion"]) {
       
        case 'listarPreguntas': 
            if (isset($_POST['action']) && $_POST['action'] == 'cargar_preguntas') {
                $preguntas = $FormularioFallas->listar_preguntas();
                   
                // GENERAR EL HTML DE LAS PREGUNTAS CON LOS ITEMS - OPERACIONES Y SUBOPERACIONES
                $html = '';
                foreach ($preguntas as $pregunta) {

                    $html .= '<div class="row justify-content-center" >';
                    $html .= '<div class="col-sm-6 border-div">';
                    $html .= '<input type="hidden"  name="'. $pregunta['id_fallas'] . '" value="' . $pregunta['id_fallas'] . '">';
                    $html .= '<p>' . $pregunta['fallas_nombre'] . '</p>';
                    $html .= '</div>';
                    $html .= '<div class="opcion">';
                    $html .= '<p class="conteFlex">';
                    $html .= '<span class="border-div">';
                    $html .= '<input type="radio" id="idResp_'. $pregunta['id_fallas'].'M'.'" class="opcion-checkbox" name="respuesta_' . $pregunta['id_fallas'] . '" value="M" checked>';
                    $html .= '<input  type="radio" id="idResp_'. $pregunta['id_fallas'].'F'.'" class="opcion-checkbox" name="respuesta_' . $pregunta['id_fallas'] . '" value="F" >';
                    $html .= '<label type="hidden" for="idResp_'. $pregunta['id_fallas'].'F' .'" class="radio-label" >F</label>';
                    $html .= '</span>';
                    $html .= '</p>';
                    $html .= '</div>';
                    $html .= '</div>';
                    
                } 
                

                echo $html;
            }
            break;
            

            case 'guardar_respuestas_form':
                /* TRAEMOS LOS DATOS */
                $form_fecha = date('Ymd');
                $pre_user = $_POST['pre_user'];
                $form_placa = $_POST['Placa'];
                $form_vehi = $_POST['form_vehiculo'];
                $pre_fallas = $form_fecha.$form_placa;
                $pre_fallas = trim($pre_fallas);
                // CARGA LAS PREGUTNAS SEGUN EL TIPO DE VEHICULO
                $preguntas = $FormularioFallas->listar_preguntas();

                /* SI EL FORMULARIO DE ESE DIA YA SE ENCUENTRA REGISTRADO */
                if ($FormularioFallas->formExiste($pre_fallas)) {
                    echo json_encode(array("status" => "errores", "message" => "El Formulario ya se encuentra registrado"));
                }else{
                    /* GUARDA LAS RESPUESTAS */
                    foreach ($preguntas as $pregunta) {
                        $id_fallas = $pregunta['id_fallas'];
                        $respuesta = $_POST['respuesta_' . $id_fallas];
                        $FormularioFallas->guardar_preguntas($form_vehi, $id_fallas, $respuesta,$pre_fallas,$pre_user);
                    }
                    echo json_encode(array("status" => "success", "message" => "Se envio correctamente"));
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
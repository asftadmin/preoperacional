<?php
/*DESARROLLADO POR: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
2023*/

require_once("../config/conexion.php");
require_once("../models/VerReporteDiario.php");

$verreportediario = new VerReporteDiario();

switch ($_GET['op']) {

    /* LISTAR REPORTES DIARIOS  */
    case 'listarReporte':
        $datos = $verreportediario->listarReportesDiarios();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["repdia_recib"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["vehi_placa"];


            // Verificar tipo_id para decidir si mostrar el botón de PDF
            if ($row["tipo_id"] == 1 || $row["tipo_id"] == 3 || $row["tipo_id"] == 4 || $row["tipo_id"] == 19) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="pdfKMS(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="pdfHRS(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            }
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;

    case 'listarReporteCond':
        $repdia_cond = $_POST["repdia_cond"];

        $datos = $verreportediario->listarReportesDiariosCond($repdia_cond);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["repdia_recib"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["vehi_placa"];

            if ($row["repdia_firma"] == null) {
                $sub_array[] = '<div class="button-container text-center">
                <button type="button" onClick="verRepdia_cond(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-info btn-icon">
                    <div><i class="fa fa-eye"></i></div>
                </button>
                <button type="button" onClick="firma(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-secondary btn-icon">
                    <div><i class="fa fa-pen-nib"></i></div>
                </button>
            </div>';
            } else {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="verRepdia_cond(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                        </div>';
            }

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case 'listarReporteAdmin':
        $datos = $verreportediario->listarReportesDiariosAdmin();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["act_nombre"];
            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];


            $sub_array[] = '<div class="button-container text-center">
                                
                                
                                <button type="button" onClick="editar(' . $row["repdia_id"] . ');" id="' . $row["repdia_id"] . '" class="btn btn-warning btn-icon">
                                    <div><i class="fa fa-edit"></i></div>
                                </button>
                            </div>';

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case 'listarRepdiaAdminxConductor':
        $datos = $verreportediario->listarRepdiaAdminxConductor($_POST['repdia_cond']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["act_nombre"];
            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];


            $sub_array[] = '<div class="button-container text-center">
                                    
                                    
                                    <button type="button" onClick="editar(' . $row["repdia_id"] . ');" id="' . $row["repdia_id"] . '" class="btn btn-warning btn-icon">
                                        <div><i class="fa fa-edit"></i></div>
                                    </button>
                                </div>';

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case 'listarActividadesCerrar':
        $datos = $verreportediario->listarActividadesCerar($_POST['repdia_recib']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["repdia_recib"];
            $sub_array[] = $row["act_nombre"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["obras_nom"];


            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="editar(\'' . $row["repdia_id"] . '\');" id="' . $row["repdia_id"] . '" class="btn btn-info btn-icon">
                                        <div><i class="fa fa-eye"></i></div>
                                        </button></div>';
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    /* EDITAR KILOMETRAJE FINAL */
    case 'editarKilo':
        $repdia_id = $_POST["repdia_id"];

        if (!empty($repdia_id)) {
            $verreportediario->update_user_pass($_POST["repdia_id"], $_POST["repdia_kilo_final"]);
            echo json_encode(array("status" => "success", "message" => "Kilometraje final registrado correctamente"));
        }
        break;

    case 'mostrarKilo':
        $datos = $verreportediario->get_repdia_id($_POST["repdia_id"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["repdia_id"] = $row['repdia_id'];
                $output["repdia_kilo_final"] = $row['repdia_kilo_final'];
            }
            echo json_encode($output);
        }
        break;

    /* UPDATE - FIRMA DE LOS REPORTES DIARIOS */
    case "guardarFirma":
        $repdia_firma = $_POST["image"];

        $datos = $verreportediario->update_firma($repdia_firma, $_POST["repdia_recib"],);
        break;

    /* MOSTRAR EL REPORTE AL EDITAR */
    case 'mostrarReporte':
        $datos = $verreportediario->get_repdia_recib_id($_POST["repdia_recib"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["repdia_recib"] = $row['repdia_recib'];
                $output["repdia_firma"] = $row['repdia_firma'];
            }
            echo json_encode($output);
        }
        break;

    /* EDITAR REPORTE DIARIO */
    case 'editarRepdia':
        $repdia_id = $_POST["repdia_id"];

        if (!empty($repdia_id)) {
            $verreportediario->update_repdia($_POST["repdia_id"], $_POST["repdia_kilo"], $_POST["repdia_kilo_final"], $_POST["repdia_obras"]);
            echo json_encode(array("status" => "success", "message" => "Reporte Diario actualizado correctamente"));
        }
        break;

    /* MOSTRAR REPORTE DIARIO PARA EDITAR*/
    case 'mostrarRepdia':
        $datos = $verreportediario->get_repdia_id($_POST["repdia_id"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["repdia_id"] = $row['repdia_id'];
                $output["repdia_kilo"] = $row['repdia_kilo'];
                $output["repdia_kilo_final"] = $row['repdia_kilo_final'];
                $output["repdia_obras"] = $row['repdia_obras'];
            }
            echo json_encode($output);
        }
        break;

    case 'filtrorepdia':
        $datos = $verreportediario->filtrorepdia($_POST['repdia_vehi'], $_POST['repdia_user'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["repdia_recib"];
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["vehi_placa"];


            // Verificar tipo_id para decidir si mostrar el botón de PDF
            if ($row["tipo_id"] == 1 || $row["tipo_id"] == 3 || $row["tipo_id"] == 4 || $row["tipo_id"] == 19) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="pdfKMS(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="pdfHRS(\'' . $row["repdia_recib"] . '\');" id="' . $row["repdia_recib"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            }
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);

        break;

    case "detalle":
        $datos = $verreportediario->detalle($_POST["repdia_recib"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $hora_inic = new DateTime($row["repdia_hr_inic"]);
            $sub_array[] = $hora_inic->format('H:i:s');
            $hora_term = new DateTime($row["repdia_hr_term"]);
            $sub_array[] = $hora_term->format('H:i:s');
            $sub_array[]  = $row["act_nombre"];
            $sub_array[]  = $row["repdia_gaso"];
            $sub_array[]  = $row["repdia_acpm"];
            $sub_array[]  = $row["repdia_acet_moto"];
            $sub_array[]  = $row["repdia_acet_hidr"];
            $sub_array[]  = $row["repdia_acet_tram"];
            $sub_array[]  = $row["repdia_acet_gras"];
            $sub_array[]  = $row["repdia_volu"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["residente_nombre_completo"];
            $sub_array[]  = $row["inspec_nombre_completo"];
            // Verificar tipo_id para decidir si mostrar las puntas
            if ($row["tipo_id"] == 11) {
                $sub_array[] = $row["repdia_puntas"];
            } else {
                $sub_array[] = "no aplica";
            }
            if ($row["repdia_ca"] !== null) {
                $sub_array[] = ($row["repdia_ca"]);
            } else {
                $sub_array[] = "";
            }
            if ($row["repdia_km_hr"] !== null) {
                $sub_array[] = ($row["repdia_km_hr"]);
            } else {
                $sub_array[] = "";
            }
            $sub_array[]  = $row["repdia_observa"];
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;


    case "datos":
        $datos = $verreportediario->detalle($_POST["repdia_recib"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["user_cedula"] = $row['user_cedula'];
                $output["conductor_nombre_completo"] = $row['conductor_nombre_completo'];
                $output["repdia_fech"] = $row['repdia_fech'];
                $output["repdia_recib"] = $row['repdia_recib'];
            }
            echo json_encode($output);
        }
        break;

    case "RDMensuales":
        $datos = $verreportediario->RDMensuales($_POST['repdia_vehi'], $_POST['repdia_user'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $hora_inic = new DateTime($row["repdia_hr_inic"]);
            $sub_array[] = $hora_inic->format('H:i:s');
            $hora_term = new DateTime($row["repdia_hr_term"]);
            $sub_array[] = $hora_term->format('H:i:s');
            $sub_array[]  = $row["repdia_gaso"];
            $sub_array[]  = $row["repdia_acpm"];
            $sub_array[]  = $row["repdia_acet_moto"];
            $sub_array[]  = $row["repdia_acet_hidr"];
            $sub_array[]  = $row["repdia_acet_tram"];
            $sub_array[]  = $row["repdia_ca"];
            $sub_array[]  = $row["repdia_km_hr"];
            $sub_array[]  = $row["repdia_acet_gras"];
            $sub_array[]  = $row["repdia_puntas"];
            $sub_array[]  = $row["repdia_volu"];
            $sub_array[]  = $row["act_nombre"];
            $sub_array[]  = $row["act_tarifa"];
            $sub_array[]  = $row["repdia_num_viajes"];
            $sub_array[]  = $row["km_hr_gastado"];
            $sub_array[]  = $row["facturacion_total"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["repdia_kilo"];
            $sub_array[]  = $row["repdia_kilo_final"];
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["residente_nombre_completo"];
            $sub_array[]  = $row["inspec_nombre_completo"];
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["repdia_observa"];

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;



    case "datosCombustible":
        $datos = $verreportediario->detalleCombustible($_POST["repdia_placa"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["vehi_placa"] = $row['vehi_placa'];
                $output["repdia_fech"] = $row['repdia_fech'];
                $output["repdia_recib"] = $row['repdia_recib'];
                $output["tipo_nombre"] = $row['tipo_nombre'];
            }
            echo json_encode($output);
        }
        break;
    case "listaRepdiaCalendario":
        $datos = $verreportediario->listarReportesDiarios_Calendario($_POST['user_id']);
        echo json_encode($datos);
        break;
    case 'ReporteConsumibles':
        $datos = $verreportediario->lsitarConsumibles($_POST['repdia_obras'], $_POST['repdia_vehi'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["vehi_placa"];
            $sub_array[] = $row["tipo_nombre"];
            $sub_array[] = $row["facturacion_total"];
            $sub_array[] = $row["horas_trabajadas"];
            $sub_array[]  = $row["acpm"];
            $sub_array[]  = $row["gasolina"];
            $sub_array[]  = $row["aceite_hidraulico"];
            $sub_array[]  = $row["aceite_motor"];
            $sub_array[]  = $row["aceite_trasmicion"];
            $sub_array[]  = $row["grasa"];
            $sub_array[]  = $row["puntas"];
            $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["vehi_id"] . '\');" id="' . $row["vehi_id"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                        </div>';

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;
    case 'detalleConsumibles':

        $vehi_id = $_POST['vehi_id'];
        // Validar si los parámetros son enviados y no vienen como texto "null"
        $obra =  $_POST['repdia_obras'];
        $fecha_inicio =  $_POST['fecha_inicio'];
        $fecha_final =  $_POST['fecha_final'];

        // Convierte string "null" a NULL real
        $obra = ($obra === "null") ? null : $obra;
        $fecha_inicio = ($fecha_inicio === "null") ? null : $fecha_inicio;
        $fecha_final = ($fecha_final === "null") ? null : $fecha_final;

        // Llamada al modelo con parámetros opcionales
        $datos = $verreportediario->detalleConsumibles($vehi_id, $obra, $fecha_inicio, $fecha_final);

        // Formatear resultados para DataTables
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["repdia_fech"]), 'd/m/Y');
            $sub_array[] = $row["act_nombre"];
            $sub_array[] = $row["tarifa"];
            $sub_array[] = $row["operador"];
            $sub_array[] = $row["inspector"];
            $sub_array[] = $row["acpm"];
            $sub_array[] = $row["gasolina"];
            $sub_array[] = $row["aceite_hidraulico"];
            $sub_array[] = $row["aceite_motor"];
            $sub_array[] = $row["aceite_trasmicion"];
            $sub_array[] = $row["grasa"];
            $sub_array[] = $row["volumen"];
            $sub_array[] = $row["puntas"];
            $sub_array[] = $row["repdia_kilo"];
            $sub_array[] = $row["repdia_kilo_final"];
            $sub_array[] = $row["kh_gastado"];
            $sub_array[] = $row["facturacion"];
            $sub_array[] = $row["repdia_observa"];

            $data[] = $sub_array;
        }

        // Respuesta JSON para DataTables
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;

    case "datosConsumibles":

        $vehi_id = $_POST['vehi_id'];
        // Validar si los parámetros son enviados y no vienen como texto "null"
        $obra =  $_POST['repdia_obras'];
        $fecha_inicio =  $_POST['fecha_inicio'];
        $fecha_final =  $_POST['fecha_final'];
        // Convierte string "null" a NULL real
        $obra = ($obra === "null") ? null : $obra;
        $fecha_inicio = ($fecha_inicio === "null") ? null : $fecha_inicio;
        $fecha_final = ($fecha_final === "null") ? null : $fecha_final;

        $datos = $verreportediario->detalleConsumibles($vehi_id, $obra, $fecha_inicio, $fecha_final);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["vehi_placa"] = $row['vehi_placa'];
                $output["tipo_nombre"] = $row['tipo_nombre'];
                $output["obras_nom"] = $row['obras_nom'];
            }
            echo json_encode($output);
        }
        break;
    case 'ReporteCumplimientoInpsec':
        $datos = $verreportediario->listarCumplimientoInspec();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["inpector"];
            $sub_array[] = $row["operador"];
            $sub_array[]  = $row["cumplimiento"];

            $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["ro_id_inspector"] . '\');" id="' . $row["ro_id_inspector"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                        </div>';

            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;
    case 'DetalleCumplimiento':
        $datos = $verreportediario->DetalleCumplimiento($_POST["ro_id_inspector"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["operador"];
            $sub_array[]  = $row["porcentaje_cumplimiento"];
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;
    case 'CumplimientoCond':
        $datos = $verreportediario->CumplimientoCond();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["conductor"];
            $sub_array[]  = $row["porcentaje_preoperacionales"];
            $sub_array[]  = $row["porcentaje_repdia"];
            $data[] = $sub_array;
        }
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($resultado);
        break;
}
?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
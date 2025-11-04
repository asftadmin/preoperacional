<?php
/*DESARROLLADO POR: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
2023*/

require_once("../config/conexion.php");
require_once("../models/VerPreoperacional.php");

$verpreoperacional = new VerPreoperacional();

switch ($_GET['op']) {
        /* LISTAR RESPUESTAS */
    case 'listar':
        $datos = $verpreoperacional->listarPreoperacional();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["pre_fecha_crea_form"]), 'd/m/Y');
            $sub_array[] = $row["tipo_nombre"];

            if ($row["pre_estado"] === "Aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Aprobado</span></div>';
            } else if ($row["pre_estado"] === "No aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No aprobado</span></div>';
            } else if (is_null($row["pre_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ccc; padding: 8px; border-radius: 5px; width: 200px;">N/A</span></div>';
            }
            if ($row["pre_fecha_revision"] !== null) {
                $sub_array[] = date_format(new DateTime($row["pre_fecha_revision"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }

            $sub_array[]  = $row["conductor_nombre_completo"];

            // Verificar tipo_id para decidir si mostrar el botón de PDF
            if ($row["tipo_id"] == 4 || $row["tipo_id"] == 19) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfTracto(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 8 || $row["tipo_id"] == 9 || $row["tipo_id"] == 12 || $row["tipo_id"] == 15) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfCargador(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 1) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfVolqueta(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 2) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfMixer(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 3) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfVehiculo(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 11 || $row["tipo_id"] == 5) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfFresadora(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 6) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfVibro(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 17) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfAutobomba(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 7) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfMtnvdra(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else {

                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
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

    case 'filtropreoperacional':
        $datos = $verpreoperacional->filtropreoperacional($_POST['operador'], $_POST['pre_vehiculo'], $_POST['fecha_inicio'], $_POST['fecha_final']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["pre_fecha_crea_form"]), 'd/m/Y');
            $sub_array[] = $row["tipo_nombre"];

            if ($row["pre_estado"] === "Aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Aprobado</span></div>';
            } else if ($row["pre_estado"] === "No aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No aprobado</span></div>';
            } else if (is_null($row["pre_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ccc; padding: 8px; border-radius: 5px; width: 200px;">N/A</span></div>';
            }
            if ($row["pre_fecha_revision"] !== null) {
                $sub_array[] = date_format(new DateTime($row["pre_fecha_revision"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }

            $sub_array[]  = $row["conductor_nombre_completo"];
            // Verificar tipo_id para decidir si mostrar el botón de PDF
            if ($row["tipo_id"] == 4 || $row["tipo_id"] == 19) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfTracto(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 8 || $row["tipo_id"] == 9 || $row["tipo_id"] == 12 || $row["tipo_id"] == 15) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfCargador(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 1) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfVolqueta(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 2) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfMixer(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 3) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfVehiculo(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 11) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfFresadora(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 5) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfFinshr(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 6) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfVibro(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 17) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfAutobomba(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 7) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfMtnvdra(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 16) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfBmbaEst(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 20) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfPtaConcreto(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else if ($row["tipo_id"] == 21) {
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
                            </button>
                            <button type="button" onClick="pdfPtAsft(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                <div><i class="fa fa-file-pdf"></i></div>
                            </button>
                        </div>';
            } else {
                // Si tipo_id no es 4, no se añade el botón de PDF
                $sub_array[] = '<div class="button-container text-center">
                            <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                <div><i class="fa fa-eye"></i></div>
                            </button>
                            <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                <div><i class="fa fa-star"></i></div>
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

        /* LISTAR KILOMETRAJE PARA EL VERIFICADOR */
    case 'listarKilometraje':
        $datos = $verpreoperacional->listarKilometraje();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["pre_fecha_crea_form"]), 'd/m/Y');
            $hora = new DateTime($row["pre_hora"]);
            $sub_array[] = $hora->format('H:i:s');
            $sub_array[] = $row["pre_kilometraje_inicial"];

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

    case 'FiltrarFecha':
        $datos = $verpreoperacional->FiltroFechas($_POST["fecha_inicio"], $_POST["fecha_final"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["pre_fecha_crea_form"]), 'd/m/Y');
            $sub_array[] = $row["pre_formulario"];
            $sub_array[] = $row["pre_kilometraje_inicial"];

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


        /* MOSTRAR DETALLE */
    case "detalle":
        $datos = $verpreoperacional->detalle($_POST["pre_formulario"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["oper_nombre"];
            $sub_array[]  = $row["suboper_nombre"];
            $sub_array[]  = $row["pre_repuesta"];
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

        /* MOSTRAR DATOS DEL CONDUCTOR*/
    case "datos":
        $datos = $verpreoperacional->detalle($_POST["pre_formulario"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["user_cedula"] = $row['user_cedula'];
                $output["conductor_nombre_completo"] = $row['conductor_nombre_completo'];
                $output["vehi_placa"] = $row['vehi_placa'];
                $output["tipo_nombre"] = $row['tipo_nombre'];
                $output["tipo_id"] = $row['tipo_id'];
                $output["pre_observaciones"] = $row['pre_observaciones'];
                $output["pre_kilometraje_inicial"] = $row['pre_kilometraje_inicial'];
            }
            echo json_encode($output);
        }
        break;

        /* UPDATE - CALIFICAR */
    case "calificar":
        $datos = $verpreoperacional->calificar($_POST["pre_formulario"], $_POST["pre_estado"], $_POST["pre_observaciones_ver"]);
        break;

        /* MOSTRAR DATOS AL EDITAR */
    case "mostrarpreo":
        $datos = $verpreoperacional->mostrarPreoperacional($_POST["pre_formulario"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["pre_formulario"] = $row["pre_formulario"];
                $output["pre_estado"] = $row["pre_estado"];
            }
            echo json_encode($output);
        }
        break;





    case 'listarPreoCond':
        $datos = $verpreoperacional->listarPreoCond($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["pre_fecha_crea_form"]), 'd/m/Y');

            if ($row["pre_fecha_revision"] !== null) {
                $sub_array[] = date_format(new DateTime($row["pre_fecha_revision"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }

            if ($row["pre_estado"] === "Aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Aprobado</span></div>';
            } else if ($row["pre_estado"] === "No aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No aprobado</span></div>';
            } else if (is_null($row["pre_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ccc; padding: 8px; border-radius: 5px; width: 200px;">N/A</span></div>';
            }
            $sub_array[]  = $row["pre_observaciones_ver"];
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="verPreo(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
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



        /* FORMULARIO FALLAS */


        /* LISTAR FALLAS EN EL VERIFICADOR */
    case 'listarFormulario':
        $datos = $verpreoperacional->listarFormularioFallas();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["pre_fallas"];
            $sub_array[] = date_format(new DateTime($row["fecha_form"]), 'd/m/Y');
            $sub_array[]  = $row["vehi_placa"];


            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="ver(\'' . $row["pre_fallas"] . '\');" id="' . $row["pre_fallas"] . '" class="btn btn-info btn-icon">
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

    case "detalleForm":
        $datos = $verpreoperacional->detalleFallas($_POST["pre_fallas"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["oper_nombre"];
            $sub_array[]  = $row["fallas_nombre"];
            $sub_array[]  = $row["form_respuesta"];
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

    case "datosForm":
        $datos = $verpreoperacional->detalleFallas($_POST["pre_fallas"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["user_cedula"] = $row['user_cedula'];
                $output["conductor_nombre_completo"] = $row['conductor_nombre_completo'];
                $output["vehi_placa"] = $row['vehi_placa'];
                $output["tipo_nombre"] = $row['tipo_nombre'];
            }
            echo json_encode($output);
        }
        break;

    case 'listarFallaCond':
        $datos = $verpreoperacional->listarFallaCond($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["fecha_form"]), 'd/m/Y');

            $sub_array[] = '<div class="button-container text-center" >
                                                    <button type="button" onClick="ver(\'' . $row["pre_fallas"] . '\');" id="' . $row["pre_fallas"] . '" class="btn btn-info btn-icon">
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
    case "listarPreCalendario":

        $datos = $verpreoperacional->listarPreoperacional_Calendario($_POST['user_id']);
        echo json_encode($datos);
        break;
    case 'listarCheck':
        $datos = $verpreoperacional->listarCheckeos();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();

            $sub_array[]  = $row["vehi_placa"];
            $sub_array[] = date_format(new DateTime($row["pre_fecha_crea_form"]), 'd/m/Y');
            $sub_array[] = $row["vehi_modelo"];

            if ($row["pre_estado"] === "Aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Aprobado</span></div>';
            } else if ($row["pre_estado"] === "No aprobado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No aprobado</span></div>';
            } else if (is_null($row["pre_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ccc; padding: 8px; border-radius: 5px; width: 200px;">N/A</span></div>';
            }
            if ($row["pre_fecha_revision"] !== null) {
                $sub_array[] = date_format(new DateTime($row["pre_fecha_revision"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[] = '<div class="button-container text-center">
                                <button type="button" onClick="ver(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-info btn-icon">
                                    <div><i class="fa fa-eye"></i></div>
                                </button>
                                <button type="button" onClick="calificar(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-secondary btn-icon">
                                    <div><i class="fa fa-star"></i></div>
                                </button>
                                <button type="button" onClick="pdfEquipoLab(\'' . $row["pre_formulario"] . '\');" id="' . $row["pre_formulario"] . '" class="btn btn-danger btn-icon">
                                    <div><i class="fa fa-file-pdf"></i></div>
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
}
?>




<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
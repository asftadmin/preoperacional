<?php
/*DESARROLLADO POR: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
2023*/

require_once("../config/conexion.php");
require_once("../models/VerAlistamiento.php");

$veralistamiento = new VerAlistamiento();

switch ($_GET['op']) {

    case 'consultar':
        $datos = $veralistamiento->consultar();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');
            $sub_array[]  = $row["alista_id"];
            $sub_array[]  = $row["vehi_placa"];
            if ($row["vehi_estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["vehi_estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["vehi_estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["vehi_estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            } else if ($row["vehi_estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["alista_codigo"];

            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ff0b0b; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Recibido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Recibido</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["inspector"];
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[] = '<div class="button-container" style="display: inline-block; text-align:center;" >
                                            <button type="button" onClick="conductor(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-info btn-icon">
                                        <div><i class="fa fa-car"></i></div>
                                        </button>
                                        <button type="button"  onClick="Reparado(\'' . $row["alista_id"] . '\');" id="' . $row["alista_id"] . '" class="btn btn-secondary btn-icon">
                                        <div><i class="fa fa-check"></i></div>
                                        </button>
                                            <button type="button"  onClick="RepFin(\'' . $row["alista_id"] . '\');" id="' . $row["alista_id"] . '" class="btn btn-danger btn-icon">
                                            <div><i class="fa fa-sign-out-alt"></i></div>
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

    case 'listar':
        $datos = $veralistamiento->listar();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');


            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ff0b0b; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Recibido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Recibido</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["inspector_nombre_completo"];
            $sub_array[]  = $row["obras_nom"];
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="detalle(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-info btn-icon">
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

    case 'listarAlistamientoxResidente':
        $datos = $veralistamiento->listarAlistamientoxResidente($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["alista_codigo"];
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');


            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #ff0b0b; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Recibido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Recibido</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["alista_observaciones"];
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="ver(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-info btn-icon">
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

    case 'listarAlistamientoxInspector':
        $datos = $veralistamiento->listarAlistamientoxInspector($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');
            $sub_array[]  = $row["alista_codigo"];

            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Recibido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Recibido</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["alista_observaciones"];
            $sub_array[]  = $row["conductor_nombre_completo"];
            $sub_array[]  = $row["obras_nom"];
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[]  = $row["observaciones_inspe"];
            $sub_array[] = '<div class="button-container text-center" >
                                           <button type="button"  onClick="calificar(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-secondary btn-icon">
                                            <div><i class="fa fa-star"></i></div>
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

    case 'listarAlistamiento':
        $datos = $veralistamiento->listarAlistamientoHM();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["alista_codigo"];
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');


            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Recibido") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Recibido</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[] = '<div class="button-container text-center" >
                                        <button type="button" onClick="ver(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-info btn-icon">
                                        <div><i class="fa fa-eye"></i></div>
                                        </button>
                                        <button type="button" onClick="conductor(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-warning btn-icon">
                                        <div><i class="fa fa-car"></i></div>
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
    case 'listarHM_Inspec':
        $datos = $veralistamiento->listarHMxInspector($_POST["user_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["alista_id"];
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');
            $sub_array[]  = $row["alista_codigo"];
            $sub_array[]  = $row["vehi_placa"];

            if ($row["vehi_estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["vehi_estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["vehi_estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["vehi_estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            } else if ($row["vehi_estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["obras_nom"];
            $sub_array[]  = $row["alista_observaciones"];
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button"  onClick="realistacion(\'' . $row["alista_id"] . '\');" id="' . $row["alista_id"] . '" class="btn btn-warning  btn-icon">
                                            <div><i class="fa fa-handshake"></i></div>
                                            </button>
                                            <button type="button"  onClick="RepFin(\'' . $row["alista_id"] . '\');" id="' . $row["alista_id"] . '" class="btn btn-danger btn-icon">
                                            <div><i class="fa fa-sign-out-alt"></i></div>
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

    case 'listarAlistamientoNoFuncionales':
        $datos = $veralistamiento->listarAlistamientoNoFuncionales();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["alista_codigo"];
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');
            $sub_array[]  = $row["vehi_placa"];

            if ($row["vehi_estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["vehi_estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["vehi_estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["vehi_estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            } else if ($row["vehi_estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[]  = $row["inspector"];
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button"  onClick="Reparado(\'' . $row["alista_id"] . '\');" id="' . $row["alista_id"] . '" class="btn btn-secondary btn-icon">
                                        <div><i class="fa fa-check"></i></div>
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

        /* case 'listarAlistamientoMAQ':
        $datos = $veralistamiento->listarAlistamientoMAQ();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["alista_codigo"];
            $sub_array[] = date_format(new DateTime($row["alista_fecha"]), 'd/m/Y');


            if ($row["alista_estado"] === "Operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["alista_estado"] === "No Funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["alista_estado"] === "Alistado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Alistado</span></div>';
            } else if ($row["alista_estado"] === "Traslado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Traslado</span></div>';
            } else if ($row["alista_estado"] === "Finalizado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #680000; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Finalizado</span></div>';
            } else if (is_null($row["alista_estado"])) {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            if ($row["alista_fecha_recibe"] !== null) {
                $sub_array[] = date_format(new DateTime($row["alista_fecha_recibe"]), 'd/m/Y');
            } else {
                $sub_array[] = "";
            }
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="verMAQ(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-info btn-icon">
                                            <div><i class="fa fa-eye"></i></div>
                                            </button>
                                            <button type="button"  onClick="calificarMAQ(\'' . $row["alista_codigo"] . '\');" id="' . $row["alista_codigo"] . '" class="btn btn-secondary btn-icon">
                                            <div><i class="fa fa-star"></i></div>
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

        break; */

        /* MOSTRAR DATOS AL CAMBIAR EL ESTADO  */
    case "mostraralista":
        $datos = $veralistamiento->mostrarAlistamiento($_POST["alista_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["alista_id"] = $row["alista_id"];
                $output["alista_estado"] = $row["alista_estado"];
                $output["alista_codigo"] = $row["alista_codigo"];
            }
            echo json_encode($output);
        }
        break;

        /* MOSTRAR DATOS AL HACER UN ALISTAMIENTO  */
    case "mostrar":
        $datos = $veralistamiento->mostrarAlistamientoAsignado($_POST["alista_codigo"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["alista_codigo"] = $row["alista_codigo"];
                $output["alista_inspec"] = $row["alista_inspec"];
                $output["alista_obras"] = $row["alista_obras"];
                $output["alista_vehi"] = $row["alista_vehi"];
                $output["alista_conductor"] = $row["alista_conductor"];
            }
            echo json_encode($output);
        }
        break;

        /* UPDATE - ESTADO ASIGNADO HERAAMIENTA MENOR */
    case "cambioestadoHM":
        $datos = $veralistamiento->asignado_HM($_POST["alista_id"], $_POST["alista_codigo"], $_POST["vehi_estado"]);
        break;

        /* UPDATE - ESTADO ASIGNADO MAQUINARIA */
        /* case "cambioestadoMAQ":
        $datos = $veralistamiento->asignado_MAQ($_POST["alista_codigo"]);
        break; */

        /* UPDATE - ASIGNACION DEL CONDUCTOR */
    case "condAlista":
        $datos = $veralistamiento->conductorAlista($_POST["alista_codigo"], $_POST["alista_conductor"]);
        break;

        /* UPDATE - ESTADO OPERATIVO-NO FUNCIONAL */
    case "calificar":
        $datos = $veralistamiento->calificar($_POST["alista_codigo"]);
        break;

        /* UPDATE - REASIGNACION DE MAQUINARIA */
    case "reasignacion":
        $datos = $veralistamiento->reasignacion($_POST["alista_id"], $_POST["alista_inspec"], $_POST["alista_obras"]);
        break;

        /* UPDATE - ESTADO */
    case "RepFin":
        $datos = $veralistamiento->finalizar($_POST["alista_codigo"], $_POST["vehi_estado"], $_POST["observaciones_inspe"], $_POST["alista_id"]);
        break;

        /* UPDATE - ESTADO MAQUINARIA DISPONIBLE Y FINALIZADO PARA EL ALISTAMIENTO */
    case "Reparado":
        $datos = $veralistamiento->Reparado($_POST["alista_id"]);
        break;

        /* MOSTRAR DETALLE */
    case "detalle":
        $datos = $veralistamiento->detalle($_POST["alista_codigo"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["vehi_placa"];
            if ($row["vehi_estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["vehi_estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["vehi_estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["vehi_estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            } else if ($row["vehi_estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["vehi_marca"];

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

        /* MOSTRAR DETALLE HERRAMIENTA MENOR */
    case "detalleHM":
        $datos = $veralistamiento->detalleHM($_POST["alista_codigo"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["alista_id"];
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["vehi_placa"];
            if ($row["estado"] === "operativo") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #26AE2A; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Operativo</span></div>';
            } else if ($row["estado"] === "no funcional") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #dc3545; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">No Funcional</span></div>';
            } else if ($row["estado"] === "asignado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #fb8500; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Asignado</span></div>';
            } else if ($row["estado"] === "stock") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #009BA9; color: #fff; padding: 8px; border-radius: 5px; width: 200px;">Stock</span></div>';
            } else if ($row["estado"] === "solicitado") {
                $sub_array[] = '<div style="text-align: center;"><span style="background-color: #f0dc00; padding: 8px; border-radius: 5px; width: 200px;">Solicitado</span></div>';
            }
            $sub_array[]  = $row["vehi_marca"];
            $sub_array[] = '<div class="button-container text-center" >
                                            <button type="button" onClick="estado(\'' . $row["alista_id"] . '\');" id="' . $row["alista_id"] . '" class="btn btn-info btn-icon">
                                            <div><i class="fa fa-star"></i></div>
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

        /* MOSTRAR DETALLE MAQUINARIA */
    case "detalleMAQ":
        $datos = $veralistamiento->detalleMAQ($_POST["alista_codigo"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[]  = $row["tipo_nombre"];
            $sub_array[]  = $row["vehi_marca"];
            $sub_array[]  = $row["vehi_placa"];
            $sub_array[]  = $row["alista_resp_vehi"];
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
        $datos = $veralistamiento->detalle($_POST["alista_codigo"]);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output["alista_fecha"] = $row['alista_fecha'];
                $output["obras_nom"] = $row['obras_nom'];
                $output["residente_nombre_completo"] = $row['residente_nombre_completo'];
                $output["alista_observaciones"] = $row['alista_observaciones'];
                $output["inspector_nombre_completo"] = $row['inspector_nombre_completo'];
                $output["cedula_inspec"] = $row['cedula_inspec'];
                $output["conductor_nombre_completo"] = $row['conductor_nombre_completo'];
            }
            echo json_encode($output);
        }
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
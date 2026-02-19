<?php
/*CONTROLADOR USUARIO */
require_once('../config/conexion.php');
require_once('../models/Usuario.php');
require_once("curl.php");

$usuario = new Usuario();

switch ($_GET['op']) { 

    /* SELECT USUARIOS REPORTE OBRAS */
    case 'comboOperadores':
        $datos = $usuario->get_user_cond();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Operador--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;
    /* SELECT USUARIOS */
    case 'comboUsuarioCond':
        $datos = $usuario->get_user_cond();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Conductor--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;
    /* SELECT USUARIOS ALISTAMIENTOS VEHICULOS */
    case 'comboCondVehi':
        $datos = $usuario->get_cond_vehi();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Operador--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;
    /* SELECT USUARIOS */
    case 'comboUsuario':
        $datos = $usuario->get_user();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = '<option disabled selected>--Selecciona el Empleado--</option>';
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;
    /* SELECT INSPECTOR */
    case 'comboUsuarioInspector':
        $datos = $usuario->get_user_inspector();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Inspector--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>-" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;
    /* SELECT RESIDENTE */
    case 'comboUsuarioResidente':
        $datos = $usuario->get_user_inspector();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Selecciona el Residente--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>-" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;
    /* SELECT USUARIOS Y COORDINADORES REPORTES DE MANTENIMIENTO */
    case 'comboUsuarioReportes':
        $datos = $usuario->get_user_reporte();
        if (is_array($datos) == true and count($datos) > 0) {
            $html = "<option value='' disabled selected>--Seleccione--</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['user_id'] . "'>" . $row['user_nombre'] . ' ' . $row['user_apellidos'] . '</option>';
            }
            echo $html;
        }
        break;

    /* GUARDAR Y EDITAR */
    case 'guardaryeditarUsuario':
        $user_id = $_POST['user_id'];
        if (empty($user_id) && $usuario->userExiste($_POST['user_cedula'])) {
            echo json_encode(array('status' => 'error', 'message' => 'El usuario ya se encuentra registrado \n Por Favor,  Verifica los campos'));
        } else {
            if (empty($user_id)) {
                $usuario->insert_user($_POST['user_cedula'], $_POST['user_nombre'], $_POST['user_apellidos'], $_POST['user_email'], $_POST['user_usuario'], $_POST['user_contrasena'], $_POST['user_rol_usuario']);
            } else {
                $usuario->update_user($_POST['user_id'], $_POST['user_cedula'], $_POST['user_nombre'], $_POST['user_apellidos'], $_POST['user_email'], $_POST['user_usuario'], $_POST['user_rol_usuario']);
            }
            echo json_encode(array('status' => 'success', 'message' => 'Datos guardados correctamente'));
        }
        break;

    /* LISTAR USUARIOS X ROL */
    case 'listarUsuario':
        $rol_usuario_actual = $_SESSION['user_rol_usuario'];
        $data = array();
        if ($rol_usuario_actual == 4) {
            $data = $usuario->get_user();
        }
        $formattedData = array();

        foreach ($data as $row) {
            $sub_array = array();
            $sub_array[] = $row['user_id'];
            $sub_array[] = $row['user_cedula'];
            $sub_array[] = $row['user_nombre'];
            $sub_array[] = $row['user_apellidos'];
            $sub_array[] = $row['user_email'];
            $sub_array[] = $row['user_usuario'];
            $sub_array[] = $row['rol_cargo'];

            $sub_array[] = '<div class="button-container" style="display: inline-block; text-align:center;" >
                                        <button type="button" onClick="editar(' . $row['user_id'] . ');" id="' . $row['user_id'] . '" class="btn btn-warning btn-icon">
                                            <div><i class="fa fa-edit"></i></div>
                                        </button>
                                        <button type="button" onClick="eliminar(' . $row['user_id'] . ');" id="' . $row['user_id'] . '" class="btn btn-danger btn-icon">
                                            <div><i class="fa fa-trash"></i></div>
                                        </button>
                                        <button type="button" onClick="clave(' . $row['user_id'] . ');" id="' . $row['user_id'] . '" class="btn btn-info btn-icon">
                                            <div><i class="fa fa-lock"></i></div>
                                        </button>
                                    </div>';
            $formattedData[] = $sub_array;
        }
        $results = array(
            'sEcho' => 1,
            'iTotalRecords' => count($formattedData),
            'iTotalDisplayRecords' => count($formattedData),
            'aaData' => $formattedData
        );
        echo json_encode($results);
        break;

    /* ELIMINAR */
    case 'eliminarUsuario':
        $usuario->delete_user($_POST['user_id']);
        break;

    //TOTAL DE PREOPERACIONALES REALIZADOS
    case 'total';
        $datos = $usuario->get_preoperacionales_xid($_POST['user_id']);

        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['total'] = $row['total'];
            }
            echo json_encode($output);
        }
        break;
    //PORCENTAJE DE COMPLIMIENTO DE REPORTES DIARIOS
    case 'PorcentajeRD';
        $datos = $usuario->get_porcentaje_repdia($_POST['user_id']);

        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['porcentaje_cumplimiento'] = $row['porcentaje_cumplimiento'];
            }
            echo json_encode($output);
        }
        break;

    //PORCENTAJE DE COMPLIMIENTO DE PREOPERACIONALES
    case 'PorcentajePreo';
        $datos = $usuario->get_porcentaje_preo($_POST['user_id']);

        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output['porcentaje_preoperacionales'] = $row['porcentaje_preoperacionales'];
            }
            echo json_encode($output);
        }
        break;

    /* MOSTRAR AL EDITAR */
    case 'mostrarUsuario':
        $datos = $usuario->get_user_id($_POST['user_id']);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output['user_id'] = $row['user_id'];
                $output['user_cedula'] = $row['user_cedula'];
                $output['user_nombre'] = $row['user_nombre'];
                $output['user_apellidos'] = $row['user_apellidos'];
                $output['user_email'] = $row['user_email'];
                $output['user_usuario'] = $row['user_usuario'];
                $output['user_contrasena'] = $row['user_contrasena'];
                $output['user_rol_usuario'] = $row['user_rol_usuario'];
            }
            echo json_encode($output);
        }
        break;

    /* EDITAR CLAVE USUARIO */
    case 'editarClave':
        $user_id = $_POST['user_id1'];

        if (!empty($user_id)) {
            $usuario->update_user_pass($_POST['user_id1'], $_POST['user_contrasena1']);
            echo json_encode(array('status' => 'success', 'message' => 'Contraseña cambiada correctamente'));
        }
        break;

    case 'mostrarClave':
        $datos = $usuario->get_user_id($_POST['user_id']);
        if (is_array($datos) == true && count($datos) > 0) {
            foreach ($datos as $row) {
                $output['user_id'] = $row['user_id'];
                $output['user_nombre'] = $row['user_nombre'];
                $output['user_apellidos'] = $row['user_apellidos'];
                $output['user_contrasena'] = $row['user_contrasena'];
            }
            echo json_encode($output);
        }
        break;

    /*  ACTUALIZAR CONTRASEÑA CORREO */
    case 'Password':
        $usuario->update_user_pass($_POST['user_id'], $_POST['user_contrasena']);
        break;

    /* ENVIAR CORREO ELECTRONICO */
    case 'Email':
        $datos = $usuario->get_user_email($_POST['user_email']);
        if (is_array($datos) == true && count($datos) > 0) {
            echo 'Existe';
        } else {
            echo 'Error';
        }

        break;
    case "consultaEmpleadoSiesa":

        $data = array();
        $pagina = 1;
        $tamPag = 100;
        $totalPaginas = 1; // valor inicial, luego se actualiza en la primera iteración
        $fechainicio = $_GET['fechainicio'] ?? '2017-01-01';
        $fechafin = $_GET['fechafin'] ?? date('Y-m-d');

        do {
            $url = 'idCompania=6026';
            $url .= '&descripcion=asfaltart_CONSULTA_EMPLEADOS';
            $url .= '&paginacion=' . urlencode("numPag=$pagina|tamPag=$tamPag");
            $url .= '&parametros=' . urlencode("fechainicio=$fechainicio|fechafin=$fechafin");

            $method = "GET";
            $response = CurlController::requestEstandar($url, $method);

            if (isset($response->detalle->Datos) && is_array($response->detalle->Datos)) {
                if ($pagina === 1 && isset($response->detalle->total_páginas)) {
                    $totalPaginas = $response->detalle->total_páginas;
                }

                foreach ($response->detalle->Datos as $row) {
                    if (is_object($row)) {
                        $sub_array = array();
                        $sub_array[] = $row->f200_id ?? '';
                        $sub_array[] = $row->f200_nombres ?? '';
                        $sub_array[] = $row->f200_apellido1 ?? '';
                        $sub_array[] = $row->f200_apellido2 ?? '';
                        $sub_array[] = $row->c0540_fecha_ingreso ?? '';
                        $sub_array[] = $row->c0540_fecha_nacimiento ?? '';
                        $sub_array[] = $row->f015_direccion1 ?? '';
                        $sub_array[] = $row->f015_email ?? '';
                        $sub_array[] = $row->f015_celular ?? '';



                        $data[] = $sub_array;
                    }
                }
            }

            $pagina++; // avanzar a la siguiente página

        } while ($pagina <= $totalPaginas);

        // Enviar la respuesta final
        $resultado = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($resultado);
        break;
    case 'consultarEmpleados':

        $datos = $usuario->get_user();

        $data = array();
        foreach ($datos as $row) {

            $sub_array = array();
            $sub_array[] = $row["user_cedula"];
            $sub_array[] = $row["user_nombre"];
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

    case "guardarEmpleadoNuevo":

        $documento = $_POST["documento"];
        $nombre = $_POST["nombre"];
        $apellido1 = $_POST["apellido1"];
        $apellido2 = $_POST["apellido2"];
        $apellidos = $apellido1.' '.$apellido2;
        $email = $_POST["email"];
        $user = $_POST["documento"];
        

        $resultado = $usuario->insertarEmplNuevo([
            'user_cedula' => $documento,
            'user_nombre' => $nombre,
            'user_apellidos' => $apellidos,
            'user_email' => $email,
            'user_usuario' => $user,
        ]);

        echo json_encode(["success" => $resultado]);


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
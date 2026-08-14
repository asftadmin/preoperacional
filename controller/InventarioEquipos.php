<?php

ob_start();
require_once '../config/conexion.php';
require_once '../models/InventarioEquipos.php';
require_once 'curl.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

/** Emite una única respuesta JSON y termina la solicitud. */
function responderInventario($success, $message, $data = array(), $http = 200)
{
    http_response_code($http);
    echo json_encode(
        array('success' => (bool) $success, 'message' => $message, 'data' => $data),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

/** Exige una sesión válida para cualquier operación del módulo. */
function exigirSesionInventario()
{
    if (empty($_SESSION['user_id'])) {
        responderInventario(false, 'La sesión ha expirado.', array(), 401);
    }
}

/** Valida el token CSRF de todas las operaciones que cambian estado. */
function exigirCsrfInventario()
{
    $token = isset($_POST['csrf_token']) && !is_array($_POST['csrf_token'])
        ? (string) $_POST['csrf_token'] : '';
    if (empty($_SESSION['csrf_inventario_equipos'])
        || !hash_equals($_SESSION['csrf_inventario_equipos'], $token)) {
        responderInventario(false, 'La solicitud de seguridad no es válida. Recargue la página.', array(), 403);
    }
}

/** Lee texto, limita longitud y rechaza arreglos inesperados. */
function textoInventario($origen, $campo, $maximo, $obligatorio = false)
{
    $valor = isset($origen[$campo]) && !is_array($origen[$campo])
        ? trim((string) $origen[$campo]) : '';
    if ($obligatorio && $valor === '') {
        responderInventario(false, 'El campo ' . $campo . ' es obligatorio.', array(), 422);
    }
    if (mb_strlen($valor, 'UTF-8') > $maximo) {
        responderInventario(false, 'El campo ' . $campo . ' supera la longitud permitida.', array(), 422);
    }
    return $valor === '' ? null : $valor;
}

/** Lee un entero positivo obligatorio u opcional. */
function enteroInventario($origen, $campo, $obligatorio = true)
{
    $valor = isset($origen[$campo]) && !is_array($origen[$campo])
        ? filter_var($origen[$campo], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))
        : false;
    if ($valor === false && $obligatorio) {
        responderInventario(false, 'El campo ' . $campo . ' no es válido.', array(), 422);
    }
    return $valor === false ? null : (int) $valor;
}

/** Valida una fecha ISO y devuelve null cuando el campo opcional está vacío. */
function fechaInventario($origen, $campo, $obligatoria = false)
{
    $valor = textoInventario($origen, $campo, 10, $obligatoria);
    if ($valor === null) {
        return null;
    }
    $fecha = DateTime::createFromFormat('Y-m-d', $valor);
    if (!$fecha || $fecha->format('Y-m-d') !== $valor) {
        responderInventario(false, 'La fecha de ' . $campo . ' no es válida.', array(), 422);
    }
    return $valor;
}

/** Convierte un campo JSON en arreglo y limita la cantidad de elementos. */
function arregloJsonInventario($campo, $maximo = 100)
{
    $texto = isset($_POST[$campo]) && !is_array($_POST[$campo]) ? (string) $_POST[$campo] : '[]';
    $datos = json_decode($texto, true);
    if (!is_array($datos) || count($datos) > $maximo) {
        responderInventario(false, 'El contenido de ' . $campo . ' no es válido.', array(), 422);
    }
    return $datos;
}

/** Convierte un checkbox del formulario a booleano. */
function checkInventario($campo)
{
    return isset($_POST[$campo]) && in_array((string) $_POST[$campo], array('1', 'true', 'on'), true);
}

/** Normaliza la respuesta variable de la API institucional. */
function normalizarEmpleadoInventario($empleado)
{
    if (!is_object($empleado)) {
        throw new RuntimeException('La API devolvió datos de empleado inválidos.');
    }
    $jefe = isset($empleado->jefe) && is_object($empleado->jefe) ? $empleado->jefe : null;
    return array(
        'documento' => isset($empleado->documento) ? trim((string) $empleado->documento) : '',
        'nombre' => isset($empleado->nombre) ? trim((string) $empleado->nombre) : '',
        'correo' => isset($empleado->correo) ? trim((string) $empleado->correo) : '',
        'cargo' => isset($empleado->cargo) ? trim((string) $empleado->cargo) : '',
        'area' => isset($empleado->area) ? trim((string) $empleado->area) : '',
        'jefe_documento' => isset($empleado->jefe_documento) ? trim((string) $empleado->jefe_documento)
            : (isset($empleado->documento_jefe) ? trim((string) $empleado->documento_jefe)
                : ($jefe && isset($jefe->documento) ? trim((string) $jefe->documento) : '')),
        'jefe_nombre' => isset($empleado->jefe_nombre) ? trim((string) $empleado->jefe_nombre)
            : ($jefe && isset($jefe->nombre) ? trim((string) $jefe->nombre) : ''),
        'jefe_cargo' => isset($empleado->jefe_cargo) ? trim((string) $empleado->jefe_cargo)
            : ($jefe && isset($jefe->cargo) ? trim((string) $jefe->cargo) : ''),
        'activo' => !empty($empleado->activo)
    );
}

/** Consulta empleados por documento o coincidencia parcial del nombre desde PHP. */
function consultarEmpleadoInventario($valor, $criterio)
{
    if ($criterio === 'documento' && !preg_match('/^[0-9]{3,20}$/D', $valor)) {
        throw new InvalidArgumentException('El documento no es válido.');
    }
    if ($criterio === 'nombre' && (mb_strlen($valor, 'UTF-8') < 3 || mb_strlen($valor, 'UTF-8') > 150)) {
        throw new InvalidArgumentException('Escriba al menos tres caracteres del nombre.');
    }
    $respuesta = CurlController::requestApiEmpleados(
        '?' . $criterio . '=' . rawurlencode($valor),
        'GET'
    );
    if (!is_object($respuesta) || empty($respuesta->success) || !isset($respuesta->data)) {
        throw new RuntimeException(
            is_object($respuesta) && isset($respuesta->message)
                ? (string) $respuesta->message : 'No fue posible consultar el empleado.'
        );
    }
    $items = $criterio === 'nombre'
        ? (is_array($respuesta->data) ? $respuesta->data : array($respuesta->data))
        : array($respuesta->data);
    $resultado = array();
    foreach ($items as $item) {
        $normalizado = normalizarEmpleadoInventario($item);
        if ($normalizado['activo'] && $normalizado['documento'] !== '' && $normalizado['nombre'] !== '') {
            $resultado[] = $normalizado;
        }
    }
    return $resultado;
}

/** Valida y normaliza un accesorio recibido. */
function normalizarComponenteInventario($item)
{
    if (!is_array($item)) {
        responderInventario(false, 'Existe un componente con formato inválido.', array(), 422);
    }
    return array(
        'tipo_componente_id' => enteroInventario($item, 'tipo_componente_id'),
        'estado_componente_id' => enteroInventario($item, 'estado_componente_id'),
        'marca' => textoInventario($item, 'marca', 80),
        'modelo' => textoInventario($item, 'modelo', 100),
        'serial' => textoInventario($item, 'serial', 120),
        'observacion' => textoInventario($item, 'observacion', 2000)
    );
}

exigirSesionInventario();
$op = isset($_GET['op']) && !is_array($_GET['op']) ? (string) $_GET['op'] : '';

try {
    $modelo = new InventarioEquipos();
    if (!$modelo->tieneAcceso((int) $_SESSION['user_id'])) {
        responderInventario(false, 'No tiene permiso para utilizar el módulo de inventario.', array(), 403);
    }
    switch ($op) {
        case 'inicial':
            responderInventario(true, 'Datos iniciales consultados.', array(
                'catalogos' => $modelo->obtenerCatalogos(),
                'responsables' => $modelo->listarResponsables()
            ));
            break;

        case 'listar':
            $filtros = array();
            foreach (array('estado_id', 'tipo_id', 'ubicacion_id', 'responsable_id') as $campo) {
                $filtros[$campo] = enteroInventario($_GET, $campo, false);
            }
            foreach (array('marca', 'serial', 'codigo_siesa', 'codigo_activo_fijo') as $campo) {
                $filtros[$campo] = textoInventario($_GET, $campo, 120);
            }
            foreach (array('adquisicion_desde', 'adquisicion_hasta', 'mantenimiento_desde', 'mantenimiento_hasta') as $campo) {
                $filtros[$campo] = fechaInventario($_GET, $campo);
            }
            responderInventario(true, 'Inventario consultado.', $modelo->listarEquipos($filtros));
            break;

        case 'detalle':
            $equipo = $modelo->obtenerEquipo(enteroInventario($_GET, 'id'));
            if ($equipo === null) {
                responderInventario(false, 'El equipo no existe.', array(), 404);
            }
            responderInventario(true, 'Detalle consultado.', $equipo);
            break;

        case 'guardarEquipo':
            exigirCsrfInventario();
            $datos = array(
                'nombre' => textoInventario($_POST, 'nombre', 120, true),
                'tipo_equipo_id' => enteroInventario($_POST, 'tipo_equipo_id'),
                'estado_equipo_id' => enteroInventario($_POST, 'estado_equipo_id'),
                'ubicacion_id' => enteroInventario($_POST, 'ubicacion_id'),
                'custodio_id' => enteroInventario($_POST, 'custodio_id'),
                'codigo_siesa' => textoInventario($_POST, 'codigo_siesa', 60),
                'marca' => textoInventario($_POST, 'marca', 80, true),
                'modelo' => textoInventario($_POST, 'modelo', 100, true),
                'serial' => textoInventario($_POST, 'serial', 120, true),
                'disco_duro' => textoInventario($_POST, 'disco_duro', 120),
                'ram' => textoInventario($_POST, 'ram', 80),
                'procesador' => textoInventario($_POST, 'procesador', 150),
                'serial_cargador' => textoInventario($_POST, 'serial_cargador', 120),
                'mac_wlan' => textoInventario($_POST, 'mac_wlan', 50),
                'mac_lan' => textoInventario($_POST, 'mac_lan', 50),
                'serial_teclado' => textoInventario($_POST, 'serial_teclado', 120),
                'serial_mouse' => textoInventario($_POST, 'serial_mouse', 120),
                'sistema_operativo' => textoInventario($_POST, 'sistema_operativo', 120),
                'licencia_windows' => textoInventario($_POST, 'licencia_windows', 180),
                'office' => textoInventario($_POST, 'office', 120),
                'licencia_office' => textoInventario($_POST, 'licencia_office', 180),
                'fecha_mantenimiento' => fechaInventario($_POST, 'fecha_mantenimiento'),
                'observaciones' => textoInventario($_POST, 'observaciones', 4000),
                'fecha_adquisicion' => fechaInventario($_POST, 'fecha_adquisicion'),
                'codigo_activo_fijo' => textoInventario($_POST, 'codigo_activo_fijo', 60)
            );
            $componentes = array();
            foreach (arregloJsonInventario('componentes_json', 50) as $item) {
                $componentes[] = normalizarComponenteInventario($item);
            }
            $equipoId = $modelo->guardarEquipo(
                enteroInventario($_POST, 'equipo_id', false),
                $datos,
                $componentes,
                (int) $_SESSION['user_id']
            );
            responderInventario(true, 'Equipo guardado correctamente.', array('equipo_id' => $equipoId), 201);
            break;

        case 'agregarComponente':
            exigirCsrfInventario();
            $modelo->agregarComponente(
                enteroInventario($_POST, 'equipo_id'),
                normalizarComponenteInventario($_POST),
                (int) $_SESSION['user_id']
            );
            responderInventario(true, 'Componente registrado correctamente.', array(), 201);
            break;

        case 'buscarEmpleado':
            $termino = textoInventario($_GET, 'q', 150, true);
            $criterio = preg_match('/^[0-9]+$/D', $termino) ? 'documento' : 'nombre';
            $resultados = array();
            foreach (consultarEmpleadoInventario($termino, $criterio) as $empleado) {
                $resultados[] = array(
                    'id' => $empleado['documento'],
                    'text' => $empleado['documento'] . ' · ' . $empleado['nombre'],
                    'empleado' => $empleado
                );
            }
            responderInventario(true, 'Empleados consultados.', array('results' => $resultados));
            break;

        case 'buscarEquipos':
            $termino = textoInventario($_GET, 'q', 150, true);
            $soloActivos = isset($_GET['activos']) && (string) $_GET['activos'] === '1';
            $todos = isset($_GET['todos']) && (string) $_GET['todos'] === '1';
            responderInventario(true, 'Equipos consultados.', array(
                'results' => $modelo->buscarEquipos($termino, $soloActivos, $todos)
            ));
            break;

        case 'crearAsignacion':
            exigirCsrfInventario();
            $documento = textoInventario($_POST, 'empleado_documento', 20, true);
            $empleados = consultarEmpleadoInventario($documento, 'documento');
            if (count($empleados) !== 1 || $empleados[0]['documento'] !== $documento) {
                responderInventario(false, 'El empleado seleccionado no pudo validarse.', array(), 422);
            }
            $empleado = $empleados[0];
            $jefeDocumento = textoInventario($_POST, 'jefe_documento', 20, true);
            $jefes = consultarEmpleadoInventario($jefeDocumento, 'documento');
            if (count($jefes) !== 1 || $jefes[0]['documento'] !== $jefeDocumento) {
                responderInventario(false, 'El jefe inmediato seleccionado no pudo validarse como empleado activo.', array(), 422);
            }
            $jefe = $jefes[0];
            $checks = array(
                'datos_empleado_verificados', 'equipo_fisico_verificado', 'seriales_verificados',
                'componentes_entregados_verificados', 'diagnostico_diligenciado', 'software_verificado'
            );
            foreach ($checks as $check) {
                if (!checkInventario($check)) {
                    responderInventario(false, 'Debe completar todas las confirmaciones de entrega.', array(), 422);
                }
            }
            $softwareNoAplica = checkInventario('software_no_aplica');
            $software = array();
            foreach (arregloJsonInventario('software_json', 50) as $programa) {
                if (!is_array($programa)) {
                    responderInventario(false, 'El software contiene un registro inválido.', array(), 422);
                }
                $software[] = array(
                    'nombre' => textoInventario($programa, 'nombre', 150, true),
                    'version' => textoInventario($programa, 'version', 80),
                    'licencia' => textoInventario($programa, 'licencia', 180),
                    'observacion' => textoInventario($programa, 'observacion', 1000)
                );
            }
            if (!$softwareNoAplica && count($software) === 0) {
                responderInventario(false, 'Registre el software instalado o marque no aplica.', array(), 422);
            }
            $componentesIds = array();
            foreach (arregloJsonInventario('componentes_ids', 100) as $id) {
                $id = filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
                if (!$id) {
                    responderInventario(false, 'La selección de componentes no es válida.', array(), 422);
                }
                $componentesIds[] = (int) $id;
            }
            $datos = array(
                'equipo_id' => enteroInventario($_POST, 'equipo_id'),
                'empleado_documento' => $empleado['documento'],
                'empleado_nombre' => $empleado['nombre'],
                'empleado_correo' => $empleado['correo'],
                'empleado_cargo' => $empleado['cargo'],
                'empleado_area' => $empleado['area'],
                'jefe_nombre' => $jefe['nombre'],
                'jefe_cargo' => $jefe['cargo'],
                'funcionario_entrega_id' => enteroInventario($_POST, 'funcionario_entrega_id'),
                'fecha_entrega' => fechaInventario($_POST, 'fecha_entrega', true),
                'diagnostico_entrega' => textoInventario($_POST, 'diagnostico_entrega', 4000, true),
                'datos_empleado_verificados' => true,
                'equipo_fisico_verificado' => true,
                'seriales_verificados' => true,
                'componentes_entregados_verificados' => true,
                'diagnostico_diligenciado' => true,
                'software_verificado' => true,
                'software_no_aplica' => $softwareNoAplica
            );
            $asignacionId = $modelo->crearAsignacion(
                $datos, array_values(array_unique($componentesIds)), $software, (int) $_SESSION['user_id']
            );
            responderInventario(true, 'Asignación registrada. Genere y conserve el acta de entrega.', array(
                'asignacion_id' => $asignacionId,
                'acta_url' => '../PDF/ActaEquipoCOF16.php?tipo=ENTREGA&id=' . $asignacionId
            ), 201);
            break;

        case 'cargarAsignacion':
            $asignacion = $modelo->obtenerAsignacionActiva(enteroInventario($_GET, 'equipo_id'));
            if ($asignacion === null) {
                responderInventario(false, 'El equipo no tiene una asignación activa.', array(), 404);
            }
            responderInventario(true, 'Asignación cargada.', $asignacion);
            break;

        case 'crearDevolucion':
            exigirCsrfInventario();
            if (!checkInventario('confirmacion_general')) {
                responderInventario(false, 'Debe confirmar la recepción física de los elementos.', array(), 422);
            }
            $elementos = array();
            foreach (arregloJsonInventario('elementos_json', 100) as $item) {
                if (!is_array($item)) {
                    responderInventario(false, 'La verificación de elementos es inválida.', array(), 422);
                }
                $elementos[] = array(
                    'asignacion_componente_id' => enteroInventario($item, 'asignacion_componente_id'),
                    'estado_recepcion_id' => enteroInventario($item, 'estado_recepcion_id'),
                    'estado_verificacion_serial_id' => enteroInventario($item, 'estado_verificacion_serial_id'),
                    'serial_recibido' => textoInventario($item, 'serial_recibido', 120),
                    'observacion' => textoInventario($item, 'observacion', 2000) ?: ''
                );
            }
            $revisionEntrada = arregloJsonInventario('revision_json', 30);
            $revision = array();
            foreach (array(
                'estado_fisico', 'encendido', 'funcionamiento', 'pantalla', 'bateria',
                'teclado', 'mouse', 'cargador', 'puertos', 'conectividad', 'limpieza'
            ) as $campo) {
                $revision[$campo] = textoInventario($revisionEntrada, $campo, 15, true);
            }
            foreach (array('danos_visibles', 'elementos_faltantes', 'observacion') as $campo) {
                $revision[$campo] = textoInventario($revisionEntrada, $campo, 3000) ?: '';
            }
            $resultado = $modelo->crearDevolucion(array(
                'asignacion_id' => enteroInventario($_POST, 'asignacion_id'),
                'motivo_devolucion_id' => enteroInventario($_POST, 'motivo_devolucion_id'),
                'motivo_otro' => textoInventario($_POST, 'motivo_otro', 250),
                'fecha_devolucion' => fechaInventario($_POST, 'fecha_devolucion', true),
                'funcionario_recibe_id' => enteroInventario($_POST, 'funcionario_recibe_id'),
                'custodio_posterior_id' => enteroInventario($_POST, 'custodio_posterior_id'),
                'ubicacion_posterior_id' => enteroInventario($_POST, 'ubicacion_posterior_id'),
                'estado_resultante_id' => enteroInventario($_POST, 'estado_resultante_id'),
                'diagnostico_devolucion' => textoInventario($_POST, 'diagnostico_devolucion', 4000, true),
                'novedades' => textoInventario($_POST, 'novedades', 4000)
            ), $elementos, $revision, (int) $_SESSION['user_id']);
            $resultado['acta_url'] = '../PDF/ActaEquipoCOF16.php?tipo=DEVOLUCION&id='
                . enteroInventario($_POST, 'asignacion_id');
            responderInventario(true, $resultado['completa']
                ? 'Devolución completa registrada.' : 'Devolución registrada con novedades o pendientes.',
                $resultado, 201);
            break;

        case 'listarMantenimientos':
            responderInventario(true, 'Historial de mantenimiento consultado.',
                $modelo->listarMantenimientos(enteroInventario($_GET, 'equipo_id')));
            break;

        case 'crearMantenimiento':
            exigirCsrfInventario();
            $repuestos = array();
            foreach (arregloJsonInventario('repuestos_json', 100) as $item) {
                if (!is_array($item)) {
                    responderInventario(false, 'Existe un repuesto con formato inválido.', array(), 422);
                }
                $cantidad = isset($item['cantidad'])
                    ? filter_var($item['cantidad'], FILTER_VALIDATE_FLOAT) : false;
                if ($cantidad === false || $cantidad <= 0) {
                    responderInventario(false, 'La cantidad de cada repuesto debe ser mayor que cero.', array(), 422);
                }
                $repuestos[] = array(
                    'descripcion' => textoInventario($item, 'descripcion', 180, true),
                    'referencia' => textoInventario($item, 'referencia', 120),
                    'serial' => textoInventario($item, 'serial', 120),
                    'cantidad' => $cantidad,
                    'observacion' => textoInventario($item, 'observacion', 2000)
                );
            }
            $mantenimientoId = $modelo->crearMantenimiento(array(
                'equipo_id' => enteroInventario($_POST, 'equipo_id'),
                'tipo_mantenimiento_id' => enteroInventario($_POST, 'tipo_mantenimiento_id'),
                'fecha' => fechaInventario($_POST, 'fecha', true),
                'responsable_id' => enteroInventario($_POST, 'responsable_id'),
                'diagnostico' => textoInventario($_POST, 'diagnostico', 4000, true),
                'actividad_realizada' => textoInventario($_POST, 'actividad_realizada', 4000, true),
                'observaciones' => textoInventario($_POST, 'observaciones', 4000),
                'estado_resultante_id' => enteroInventario($_POST, 'estado_resultante_id'),
                'proxima_fecha' => fechaInventario($_POST, 'proxima_fecha')
            ), $repuestos, (int) $_SESSION['user_id']);
            responderInventario(true, 'Mantenimiento registrado correctamente.',
                array('mantenimiento_id' => $mantenimientoId), 201);
            break;

        case 'trazabilidad':
            responderInventario(true, 'Trazabilidad consultada.',
                $modelo->obtenerTrazabilidad(enteroInventario($_GET, 'equipo_id')));
            break;

        case 'pendientes':
            responderInventario(true, 'Pendientes consultados.', $modelo->listarPendientes());
            break;

        default:
            responderInventario(false, 'Operación no válida.', array(), 404);
    }
} catch (InvalidArgumentException $error) {
    responderInventario(false, $error->getMessage(), array(), 422);
} catch (RuntimeException $error) {
    responderInventario(false, $error->getMessage(), array(), 422);
} catch (PDOException $error) {
    error_log('InventarioEquipos PDO: ' . $error->getMessage());
    $codigo = $error->getCode() === '23505' ? 409 : 500;
    responderInventario(false, $codigo === 409
        ? 'El serial o código ingresado ya está registrado.'
        : 'No fue posible completar la operación en la base de datos.', array(), $codigo);
} catch (Throwable $error) {
    error_log('InventarioEquipos: ' . $error->getMessage());
    responderInventario(false, 'Se presentó un error interno al procesar la solicitud.', array(), 500);
}

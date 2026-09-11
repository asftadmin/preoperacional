<?php
require_once ('../../config/conexion.php');
require_once ('../../models/Rol.php');

$rol = new Rol();

$datos = $rol->validacion_acceso(
    $_SESSION['user_id'],
    'aprobarTrazabilidad'
);

if (is_array($datos) && count($datos) > 0) {
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once ('../MainHead/head.php'); ?>

    <title>Trazabilidad de Mezcla</title>

    <!-- SweetAlert -->
    <link rel="stylesheet" href="../../public/plugins/sweetalert2/sweetalert2.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="../../public/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Daterangepicker -->
    <link rel="stylesheet" href="../../public/plugins/daterangepicker/daterangepicker.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        <?php require_once ('../MainNav/nav.php'); ?>
        <?php require_once ('../MainMenu/menu.php'); ?>

        <div class="content-wrapper">

            <!-- Encabezado de la vista -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-8">
                            <h1>Trazabilidad de Mezcla</h1>
                        </div>

                        <div class="col-sm-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="../home/">Inicio</a>
                                </li>
                                <li class="breadcrumb-item">
                                    Obras
                                </li>
                                <li class="breadcrumb-item active">
                                    Trazabilidad de Mezcla
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Todo el contenido funcional debe permanecer dentro de content -->
            <section class="content">
                <div class="container-fluid p-2">

                    <form id="form_trazabilidad" autocomplete="off">

                        <input type="hidden" id="trazabilidad_id" name="trazabilidad_id" value="">

                        <!-- Información general -->
                        <div class="card card-primary card-outline">

                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    Revisión de trazabilidad de mezcla
                                </h3>

                                <div class="card-tools">
                                    <span id="estado_trazabilidad" class="badge badge-warning">
                                        Pendiente aprobación
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <!-- Tipo de mezcla -->
                                    <div class="col-lg-3 col-md-6">
                                        <div class="form-group">
                                            <label for="tipo_mezcla">
                                                Tipo de mezcla
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select class="form-control select2" id="tipo_mezcla" name="tipo_mezcla"
                                                style="width: 100%;">
                                                <option value="">
                                                    Seleccione...
                                                </option>
                                                <option value="MDC-19">
                                                    MDC-19
                                                </option>
                                                <option value="MDC-25">
                                                    MDC-25
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Fecha -->
                                    <div class="col-lg-3 col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_trazabilidad">
                                                Fecha
                                                <span class="text-danger">*</span>
                                            </label>

                                            <div class="input-group">
                                                <input type="text" class="form-control" id="fecha_trazabilidad"
                                                    name="fecha_trazabilidad" placeholder="DD-MM-AAAA" readonly>

                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tipo de actividad -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>
                                                Tipo de actividad
                                                <span class="text-danger">*</span>
                                            </label>

                                            <div class="border rounded px-3 py-2">

                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="actividad_continua" name="tipo_actividad"
                                                        value="CONTINUA" class="custom-control-input">
                                                    <label class="custom-control-label" for="actividad_continua">
                                                        Aplicación continua
                                                    </label>
                                                </div>

                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="actividad_bacheo" name="tipo_actividad"
                                                        value="BACHEO" class="custom-control-input">
                                                    <label class="custom-control-label" for="actividad_bacheo">
                                                        Bacheo
                                                    </label>
                                                </div>

                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="actividad_parcheo" name="tipo_actividad"
                                                        value="PARCHEO" class="custom-control-input">
                                                    <label class="custom-control-label" for="actividad_parcheo">
                                                        Parcheo
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <!-- Obra -->
                                    <div class="col-md-8">
                                        <div class="form-group mb-0">
                                            <label for="obra_id">
                                                Ubicación / Obra
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select class="form-control select2" id="obra_id" name="obra_id"
                                                style="width: 100%;">
                                                <option value="">
                                                    Seleccione una obra...
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Elaboro -->
                                    <div class="form-group col-md-4">
                                        <label>
                                            Elaboró
                                        </label>

                                        <input type="text" class="form-control" id="elaborado_por" readonly>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <!-- Detalle de aplicación -->
                        <div class="card card-primary card-outline">

                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-road mr-2"></i>
                                    Detalle de aplicación
                                </h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" id="btn_agregar_detalle">
                                        <i class="fas fa-plus mr-1"></i>
                                        Agregar registro
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <!-- Mensaje informativo -->
                                <div class="alert alert-light border mb-3">
                                    <i class="fas fa-info-circle text-primary mr-1"></i>
                                    Las columnas disponibles se ajustarán automáticamente
                                    según el tipo de actividad seleccionado.
                                </div>

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover table-sm text-center"
                                        id="tabla_detalle_aplicacion">
                                        <thead class="thead-light">

                                            <tr>

                                                <!-- [1] Placa de la volqueta -->
                                                <th class="align-middle text-nowrap" data-casilla="1">
                                                    <span class="badge badge-primary">[1]</span>
                                                    <br>
                                                    Placa Volqueta
                                                </th>

                                                <!-- [2] PR inicial o coordenada X -->
                                                <th class="align-middle text-nowrap" data-casilla="2">
                                                    <span class="badge badge-primary">[2]</span>
                                                    <br>
                                                    PR Inicial /
                                                    <br>
                                                    Coordenada X
                                                </th>

                                                <!-- [3] PR final o coordenada Y -->
                                                <th class="align-middle text-nowrap" data-casilla="3">
                                                    <span class="badge badge-primary">[3]</span>
                                                    <br>
                                                    PR Final /
                                                    <br>
                                                    Coordenada Y
                                                </th>

                                                <!-- [4] Número de caja -->
                                                <th class="align-middle text-nowrap" data-casilla="4">
                                                    <span class="badge badge-primary">[4]</span>
                                                    <br>
                                                    # Caja
                                                </th>

                                                <!-- [5] Longitud -->
                                                <th class="align-middle text-nowrap" data-casilla="5">
                                                    <span class="badge badge-primary">[5]</span>
                                                    <br>
                                                    Longitud
                                                    <br>
                                                    [m]
                                                </th>

                                                <!-- [6] Ancho -->
                                                <th class="align-middle text-nowrap" data-casilla="6">
                                                    <span class="badge badge-primary">[6]</span>
                                                    <br>
                                                    Ancho
                                                    <br>
                                                    [m]
                                                </th>

                                                <!-- [7] Espesor demolido -->
                                                <th class="align-middle text-nowrap" data-casilla="7">
                                                    <span class="badge badge-primary">[7]</span>
                                                    <br>
                                                    Espesor
                                                    <br>
                                                    Demolido [m]
                                                </th>

                                                <!-- [8] Espesor excavación -->
                                                <th class="align-middle text-nowrap" data-casilla="8">
                                                    <span class="badge badge-primary">[8]</span>
                                                    <br>
                                                    Espesor
                                                    <br>
                                                    Excavación [m]
                                                </th>

                                                <!-- [9] Espesor base -->
                                                <th class="align-middle text-nowrap" data-casilla="9">
                                                    <span class="badge badge-primary">[9]</span>
                                                    <br>
                                                    Espesor
                                                    <br>
                                                    Base [m]
                                                </th>

                                                <!-- [10] Espesor mezcla -->
                                                <th class="align-middle text-nowrap" data-casilla="10">
                                                    <span class="badge badge-primary">[10]</span>
                                                    <br>
                                                    Espesor
                                                    <br>
                                                    Mezcla [m]
                                                </th>

                                                <!-- [11] Volumen demolido -->
                                                <th class="align-middle text-nowrap" data-casilla="11">
                                                    <span class="badge badge-success">[11]</span>
                                                    <br>
                                                    Volumen
                                                    <br>
                                                    Demolido [m³]
                                                </th>

                                                <!-- [12] Volumen excavado -->
                                                <th class="align-middle text-nowrap" data-casilla="12">
                                                    <span class="badge badge-success">[12]</span>
                                                    <br>
                                                    Volumen
                                                    <br>
                                                    Excavado [m³]
                                                </th>

                                                <!-- [13] Volumen base -->
                                                <th class="align-middle text-nowrap" data-casilla="13">
                                                    <span class="badge badge-success">[13]</span>
                                                    <br>
                                                    Volumen
                                                    <br>
                                                    Base [m³]
                                                </th>

                                                <!-- [14] Imprimación -->
                                                <th class="align-middle text-nowrap" data-casilla="14">
                                                    <span class="badge badge-success">[14]</span>
                                                    <br>
                                                    Imprimación
                                                    <br>
                                                    [m²]
                                                </th>

                                                <!-- [15] Temperatura de aplicación -->
                                                <th class="align-middle text-nowrap" data-casilla="15">
                                                    <span class="badge badge-primary">[15]</span>
                                                    <br>
                                                    T. Aplicación
                                                    <br>
                                                    °C
                                                </th>

                                                <!-- [16] Volumen de mezcla -->
                                                <th class="align-middle text-nowrap" data-casilla="16">
                                                    <span class="badge badge-primary">[16]</span>
                                                    <br>
                                                    Volumen
                                                    <br>
                                                    Mezcla [m³]
                                                </th>

                                                <!-- Columna exclusiva para acciones de la vista -->
                                                <th class="align-middle">
                                                    Acción
                                                </th>

                                            </tr>

                                        </thead>

                                        <tbody id="tbody_detalle_aplicacion">

                                            <!-- Primera fila del detalle -->
                                            <tr class="fila-detalle" data-fila="0">

                                                <!-- [1] Volqueta - se cargará mediante AJAX y Select2 -->
                                                <td data-casilla="1">
                                                    <select class="form-control form-control-sm select2-volqueta"
                                                        name="detalle[0][vehi_id]" data-campo="vehi_id">
                                                        <option value="">
                                                            Seleccione...
                                                        </option>
                                                    </select>
                                                </td>

                                                <!-- [2] PR inicial / Coordenada X -->
                                                <td data-casilla="2">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="detalle[0][pr_inicial]" data-campo="pr_inicial"
                                                        autocomplete="off">
                                                </td>

                                                <!-- [3] PR final / Coordenada Y -->
                                                <td data-casilla="3">
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="detalle[0][pr_final]" data-campo="pr_final"
                                                        autocomplete="off">
                                                </td>

                                                <!-- [4] Número de caja -->
                                                <td data-casilla="4">
                                                    <input type="number" class="form-control form-control-sm"
                                                        name="detalle[0][numero_caja]" data-campo="numero_caja" min="1">
                                                </td>

                                                <!-- [5] Longitud -->
                                                <td data-casilla="5">
                                                    <input type="number"
                                                        class="form-control form-control-sm calcular-detalle"
                                                        name="detalle[0][longitud]" data-campo="longitud" min="0"
                                                        step="0.001">
                                                </td>

                                                <!-- [6] Ancho -->
                                                <td data-casilla="6">
                                                    <input type="number"
                                                        class="form-control form-control-sm calcular-detalle"
                                                        name="detalle[0][ancho]" data-campo="ancho" min="0"
                                                        step="0.001">
                                                </td>

                                                <!-- [7] Espesor demolido -->
                                                <td data-casilla="7">
                                                    <input type="number"
                                                        class="form-control form-control-sm calcular-detalle"
                                                        name="detalle[0][espesor_demolido]"
                                                        data-campo="espesor_demolido" min="0" step="0.001">
                                                </td>

                                                <!-- [8] Espesor excavación -->
                                                <td data-casilla="8">
                                                    <input type="number"
                                                        class="form-control form-control-sm calcular-detalle"
                                                        name="detalle[0][espesor_excavacion]"
                                                        data-campo="espesor_excavacion" min="0" step="0.001">
                                                </td>

                                                <!-- [9] Espesor base -->
                                                <td data-casilla="9">
                                                    <input type="number"
                                                        class="form-control form-control-sm calcular-detalle"
                                                        name="detalle[0][espesor_base]" data-campo="espesor_base"
                                                        min="0" step="0.001">
                                                </td>

                                                <!-- [10] Espesor mezcla -->
                                                <td data-casilla="10">
                                                    <input type="number" class="form-control form-control-sm"
                                                        name="detalle[0][espesor_mezcla]" data-campo="espesor_mezcla"
                                                        min="0" step="0.001">
                                                </td>

                                                <!-- [11] Longitud x Ancho x Espesor demolido -->
                                                <td data-casilla="11">
                                                    <input type="number" class="form-control form-control-sm bg-light"
                                                        name="detalle[0][volumen_demolido]"
                                                        data-campo="volumen_demolido" readonly>
                                                </td>

                                                <!-- [12] Longitud x Ancho x Espesor excavación -->
                                                <td data-casilla="12">
                                                    <input type="number" class="form-control form-control-sm bg-light"
                                                        name="detalle[0][volumen_excavado]"
                                                        data-campo="volumen_excavado" readonly>
                                                </td>

                                                <!-- [13] Longitud x Ancho x Espesor base -->
                                                <td data-casilla="13">
                                                    <input type="number" class="form-control form-control-sm bg-light"
                                                        name="detalle[0][volumen_base]" data-campo="volumen_base"
                                                        readonly>
                                                </td>

                                                <!-- [14] Imprimación: Longitud x Ancho x número de capas -->
                                                <td data-casilla="14">

                                                    <div class="input-group input-group-sm">

                                                        <input type="number" class="form-control calcular-detalle"
                                                            name="detalle[0][capas_imprimacion]"
                                                            data-campo="capas_imprimacion" min="1" placeholder="#C"
                                                            title="Número de capas de imprimación">

                                                        <input type="number" class="form-control bg-light"
                                                            name="detalle[0][imprimacion]" data-campo="imprimacion"
                                                            readonly>

                                                    </div>

                                                </td>

                                                <!-- [15] Temperatura de aplicación -->
                                                <td data-casilla="15">
                                                    <input type="number" class="form-control form-control-sm"
                                                        name="detalle[0][temperatura_aplicacion]"
                                                        data-campo="temperatura_aplicacion" min="0" step="0.1">
                                                </td>

                                                <!-- [16] Volumen de mezcla -->
                                                <td data-casilla="16">
                                                    <input type="number" class="form-control form-control-sm"
                                                        name="detalle[0][volumen_mezcla]" data-campo="volumen_mezcla"
                                                        min="0" step="0.001">
                                                </td>

                                                <!-- Eliminar fila -->
                                                <td class="align-middle">
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm btn-eliminar-detalle"
                                                        title="Eliminar registro">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                        <!-- Control de llegada de mezcla -->
                        <div class="card card-primary card-outline">

                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-truck-loading mr-2"></i>
                                    Control de llegada de mezcla
                                </h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" id="btn_agregar_llegada">
                                        <i class="fas fa-plus mr-1"></i>
                                        Agregar registro
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <!-- Registros de llegada -->
                                    <div class="col-12">

                                        <div class="table-responsive">

                                            <table class="table table-bordered table-hover table-sm text-center"
                                                id="tabla_llegada_mezcla">
                                                <thead class="thead-light">
                                                    <tr>

                                                        <!-- [1] Placa -->
                                                        <th class="align-middle">
                                                            <span class="badge badge-primary">[1]</span>
                                                            <br>
                                                            Placa
                                                        </th>

                                                        <!-- [16] Temperatura de llegada -->
                                                        <th class="align-middle">
                                                            <span class="badge badge-primary">[16]</span>
                                                            <br>
                                                            Temperatura de llegada
                                                            <br>
                                                            °C
                                                        </th>

                                                        <!-- [17] Volumen de llegada -->
                                                        <th class="align-middle">
                                                            <span class="badge badge-primary">[17]</span>
                                                            <br>
                                                            Volumen de llegada
                                                        </th>

                                                        <!-- [18] Volumen aplicado -->
                                                        <th class="align-middle">
                                                            <span class="badge badge-primary">[18]</span>
                                                            <br>
                                                            Volumen aplicado
                                                        </th>

                                                        <!-- [19] Factor de compactación -->
                                                        <th class="align-middle">
                                                            <span class="badge badge-success">[19]</span>
                                                            <br>
                                                            FC
                                                        </th>

                                                        <th class="align-middle">
                                                            Acción
                                                        </th>

                                                    </tr>
                                                </thead>

                                                <tbody id="tbody_llegada_mezcla">

                                                    <!-- Primera fila -->
                                                    <tr class="fila-llegada" data-fila="0">

                                                        <!-- [1] Volqueta -->
                                                        <td>
                                                            <select
                                                                class="form-control form-control-sm select2-volqueta-llegada"
                                                                name="llegada[0][vehi_id]" data-campo="vehi_id"
                                                                style="width: 100%;">
                                                                <option value="">
                                                                    Seleccione...
                                                                </option>
                                                            </select>
                                                        </td>

                                                        <!-- [16] Temperatura de llegada -->
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm"
                                                                name="llegada[0][temperatura_llegada]"
                                                                data-campo="temperatura_llegada" min="0" step="0.1">
                                                        </td>

                                                        <!-- [17] Volumen de llegada -->
                                                        <td>
                                                            <input type="number"
                                                                class="form-control form-control-sm calcular-fc"
                                                                name="llegada[0][volumen_llegada]"
                                                                data-campo="volumen_llegada" min="0" step="0.001">
                                                        </td>

                                                        <!-- [18] Volumen aplicado -->
                                                        <td>
                                                            <input type="number"
                                                                class="form-control form-control-sm calcular-fc"
                                                                name="llegada[0][volumen_aplicado]"
                                                                data-campo="volumen_aplicado" min="0" step="0.001">
                                                        </td>

                                                        <!-- [19] FC = [17] / [18] -->
                                                        <td>
                                                            <input type="number"
                                                                class="form-control form-control-sm bg-light"
                                                                name="llegada[0][factor_compactacion]"
                                                                data-campo="factor_compactacion" readonly>
                                                        </td>

                                                        <!-- Eliminar registro -->
                                                        <td class="align-middle">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-eliminar-llegada"
                                                                title="Eliminar registro">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>

                                                    </tr>

                                                </tbody>
                                            </table>

                                        </div>

                                        <small class="text-muted">
                                            El factor de compactación se calcula automáticamente:
                                            FC = Volumen de llegada / Volumen aplicado.
                                        </small>

                                    </div>

                                    <!-- Observaciones -->
                                    <div class="col-12">

                                        <div class="form-group mb-0">
                                            <label for="observaciones">
                                                Observaciones
                                            </label>

                                            <textarea class="form-control" id="observaciones" name="observaciones"
                                                rows="7" maxlength="1000"
                                                placeholder="Ingrese las observaciones del formato..."></textarea>

                                            <small class="form-text text-muted">
                                                Registre novedades o condiciones relevantes durante la aplicación.
                                            </small>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Acciones del formulario -->
                        <div class="card-footer">

                            <button type="button" class="btn btn-secondary" id="btn_volver">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Volver
                            </button>


                            <div class="float-right">

                                <button type="button" class="btn btn-danger mr-2" id="btn_rechazar">
                                    <i class="fas fa-times mr-1"></i>
                                    Rechazar
                                </button>


                                <button type="button" class="btn btn-success" id="btn_aprobar">
                                    <i class="fas fa-check mr-1"></i>
                                    Aprobar
                                </button>

                            </div>

                        </div>

                    </form>

                </div>
            </section>

        </div>

        <?php require_once ('../MainFooter/footer.php'); ?>

    </div>

    <?php require_once ('../MainJS/JS.php'); ?>

    <!-- Select2 -->
    <script src="../../public/plugins/select2/js/select2.full.min.js"></script>

    <!-- Moment y Daterangepicker -->
    <script src="../../public/plugins/moment/moment.min.js"></script>
    <script src="../../public/plugins/daterangepicker/daterangepicker.js"></script>

    <!-- SweetAlert -->
    <script src="../../public/plugins/sweetalert2/sweetalert2.js"></script>

    <!-- JS exclusivo del módulo -->
    <script src="aprobarTrazabilidad.js"></script>

</body>

</html>

<?php
} else {
    header('Location:' . Conectar::ruta() . 'index.php');
}
?>
<?php
require_once '../../config/conexion.php';
require_once '../../models/Rol.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . Conectar::ruta() . 'index.php');
    exit;
}
$rol = new Rol();
$acceso = $rol->validacion_acceso($_SESSION['user_id'], 'inventarioEquipos');
if (!is_array($acceso) || count($acceso) === 0) {
    header('Location: ' . Conectar::ruta() . 'index.php');
    exit;
}
if (empty($_SESSION['csrf_inventario_equipos'])) {
    $_SESSION['csrf_inventario_equipos'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once '../MainHead/head.php'; ?>
<title>INVENTARIO EQUIPOS DE COMPUTO</title>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php require_once '../MainNav/nav.php'; ?>
        <?php require_once '../MainMenu/menu.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-7">
                            <h1 class="m-0"><i class="fas fa-laptop mr-2"></i>INVENTARIO EQUIPOS DE COMPUTO</h1>
                        </div>
                        <div class="col-sm-5">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Sistemas</li>
                                <li class="breadcrumb-item active">Inventario</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <input type="hidden" id="csrfInventario"
                        value="<?= htmlspecialchars($_SESSION['csrf_inventario_equipos'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="card card-primary card-outline">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills" id="inventarioTabs">
                                <li class="nav-item"><a class="nav-link active" href="#tabInventario"
                                        data-toggle="tab">Inventario</a></li>
                                <li class="nav-item"><a class="nav-link" href="#tabAsignacion"
                                        data-toggle="tab">Asignación</a></li>
                                <li class="nav-item"><a class="nav-link" href="#tabDevolucion"
                                        data-toggle="tab">Devolución</a></li>
                                <li class="nav-item"><a class="nav-link" href="#tabTrazabilidad"
                                        data-toggle="tab">Trazabilidad</a></li>
                                <li class="nav-item"><a class="nav-link" href="#tabPendientes"
                                        data-toggle="tab">Pendientes</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="tabInventario">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="card-title">Bandeja de equipos</h3>
                                        <a class="btn btn-primary btn-sm" href="registrar_equipo.php">
                                            <i class="fas fa-plus mr-1"></i>Registrar equipo
                                        </a>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-2"><label>Estado</label><select id="filtroEstado"
                                                class="form-control filtro-catalogo"></select></div>
                                        <div class="form-group col-md-2"><label>Tipo</label><select id="filtroTipo"
                                                class="form-control filtro-catalogo"></select></div>
                                        <div class="form-group col-md-2"><label>Ubicación</label><select
                                                id="filtroUbicacion" class="form-control filtro-catalogo"></select>
                                        </div>
                                        <div class="form-group col-md-2"><label>Responsable</label><select
                                                id="filtroResponsable" class="form-control filtro-catalogo"></select>
                                        </div>
                                        <div class="form-group col-md-2"><label>Marca</label><input id="filtroMarca"
                                                class="form-control" maxlength="80"></div>
                                        <div class="form-group col-md-2"><label>Serial</label><input id="filtroSerial"
                                                class="form-control" maxlength="120"></div>
                                        <div class="form-group col-md-2"><label>Código SIESA</label><input
                                                id="filtroSiesa" class="form-control" maxlength="60"></div>
                                        <div class="form-group col-md-2"><label>Activo fijo</label><input
                                                id="filtroActivo" class="form-control" maxlength="60"></div>
                                        <div class="form-group col-md-3"><label>Fecha de adquisición</label><input
                                                id="filtroAdquisicion" class="form-control rango-fecha" readonly></div>
                                        <div class="form-group col-md-3"><label>Fecha de mantenimiento</label><input
                                                id="filtroMantenimiento" class="form-control rango-fecha" readonly>
                                        </div>
                                        <div class="form-group col-md-2 d-flex align-items-end">
                                            <button id="btnFiltrarInventario"
                                                class="btn btn-outline-primary btn-block"><i
                                                    class="fas fa-search mr-1"></i>Filtrar</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tablaInventario"
                                            class="table table-bordered table-striped table-hover w-100">
                                            <thead>
                                                <tr>
                                                    <th>Equipo</th>
                                                    <th>Tipo</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Serial</th>
                                                    <th>SIESA</th>
                                                    <th>Activo fijo</th>
                                                    <th>Estado</th>
                                                    <th>Ubicación</th>
                                                    <th>Responsable</th>
                                                    <th>Mantenimiento</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tabAsignacion">
                                    <form id="formAsignacion" autocomplete="off">
                                        <h4>Entrega de equipo</h4>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Equipo disponible <span class="text-danger">*</span></label>
                                                <select id="asignacionEquipo" name="equipo_id"
                                                    class="form-control selector-equipo" required></select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Empleado activo <span class="text-danger">*</span></label>
                                                <select id="asignacionEmpleado" class="form-control selector-empleado"
                                                    required></select>
                                                <input type="hidden" name="empleado_documento" id="asignacionDocumento">
                                            </div>
                                        </div>
                                        <div id="resumenAsignacion" class="callout callout-info d-none"></div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Jefe inmediato activo <span class="text-danger">*</span></label>
                                                <select id="asignacionJefeEmpleado"
                                                    class="form-control selector-empleado" required></select>
                                                <input type="hidden" name="jefe_documento" id="asignacionJefeDocumento">
                                            </div>
                                            <div class="form-group col-md-6"><label>Cargo del jefe</label><input
                                                    id="asignacionJefeCargo" class="form-control" maxlength="150"
                                                    readonly></div>
                                            <div class="form-group col-md-4"><label>Fecha de entrega <span
                                                        class="text-danger">*</span></label><input type="date"
                                                    name="fecha_entrega" class="form-control" required></div>
                                            <div class="form-group col-md-8"><label>Funcionario que entrega <span
                                                        class="text-danger">*</span></label><select
                                                    name="funcionario_entrega_id" id="asignacionFuncionario"
                                                    class="form-control responsable-select" required></select></div>
                                        </div>
                                        <h5>Componentes entregados</h5>
                                        <div id="asignacionComponentes" class="mb-3 text-muted">Seleccione un equipo.
                                        </div>
                                        <div class="card card-light">
                                            <div class="card-header"><strong>Software instalado</strong><button
                                                    type="button" class="btn btn-xs btn-outline-primary float-right"
                                                    id="btnAgregarSoftware"><i class="fas fa-plus"></i> Agregar</button>
                                            </div>
                                            <div class="card-body p-2">
                                                <div id="listaSoftware"></div>
                                                <div class="custom-control custom-checkbox mt-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="softwareNoAplica" name="software_no_aplica" value="1">
                                                    <label class="custom-control-label" for="softwareNoAplica">No
                                                        aplica</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group"><label>Diagnóstico de entrega <span
                                                    class="text-danger">*</span></label><textarea
                                                name="diagnostico_entrega" class="form-control" rows="4"
                                                maxlength="4000" required></textarea></div>
                                        <div class="row">
                                            <?php
                                            $checksEntrega = array(
                                                'datos_empleado_verificados' => 'Datos del empleado verificados',
                                                'equipo_fisico_verificado' => 'Equipo físico verificado',
                                                'seriales_verificados' => 'Seriales verificados',
                                                'componentes_entregados_verificados' => 'Componentes seleccionados entregados',
                                                'diagnostico_diligenciado' => 'Diagnóstico diligenciado',
                                                'software_verificado' => 'Software registrado o marcado no aplica'
                                            );
                                            foreach ($checksEntrega as $id => $texto): ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input check-entrega"
                                                            id="<?= $id ?>" name="<?= $id ?>" value="1">
                                                        <label class="custom-control-label"
                                                            for="<?= $id ?>"><?= $texto ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button class="btn btn-primary mt-3" type="submit" id="btnGuardarAsignacion">
                                            <i class="fas fa-file-export mr-1"></i>Asignar y generar acta
                                        </button>
                                    </form>
                                </div>

                                <div class="tab-pane" id="tabDevolucion">
                                    <h4>Recepción de equipo</h4>
                                    <div class="row">
                                        <div class="form-group col-md-9">
                                            <label>Equipo con asignación activa <span
                                                    class="text-danger">*</span></label>
                                            <select id="devolucionEquipo" class="form-control"></select>
                                            <small class="text-muted">Busque por equipo, serial, códigos o datos del
                                                colaborador.</small>
                                        </div>
                                        <div class="form-group col-md-3 d-flex align-items-end">
                                            <button id="btnCargarAsignacion" class="btn btn-info btn-block"><i
                                                    class="fas fa-download mr-1"></i>Cargar asignación</button>
                                        </div>
                                    </div>
                                    <form id="formDevolucion" class="d-none" autocomplete="off">
                                        <input type="hidden" name="asignacion_id" id="devolucionAsignacionId">
                                        <div id="resumenDevolucion" class="callout callout-info"></div>
                                        <h5>Verificación individual</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered" id="tablaElementosDevolucion">
                                                <thead>
                                                    <tr>
                                                        <th>Elemento</th>
                                                        <th>Serial registrado</th>
                                                        <th>Recepción</th>
                                                        <th>Serial</th>
                                                        <th>Serial recibido</th>
                                                        <th>Observación</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <h5 class="mt-3">Revisión física y funcional</h5>
                                        <div class="row" id="revisionFisica"></div>
                                        <div class="row">
                                            <div class="form-group col-md-4"><label>Daños visibles</label><textarea
                                                    id="revisionDanos" class="form-control revision-texto" rows="2"
                                                    maxlength="3000"></textarea></div>
                                            <div class="form-group col-md-4"><label>Elementos faltantes</label><textarea
                                                    id="revisionFaltantes" class="form-control revision-texto" rows="2"
                                                    maxlength="3000"></textarea></div>
                                            <div class="form-group col-md-4"><label>Observación de
                                                    revisión</label><textarea id="revisionObservacion"
                                                    class="form-control revision-texto" rows="2"
                                                    maxlength="3000"></textarea></div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-4"><label>Motivo <span
                                                        class="text-danger">*</span></label><select
                                                    name="motivo_devolucion_id" id="devolucionMotivo"
                                                    class="form-control" required></select></div>
                                            <div class="form-group col-md-4"><label>Otro motivo</label><input
                                                    name="motivo_otro" class="form-control" maxlength="250"></div>
                                            <div class="form-group col-md-4"><label>Fecha <span
                                                        class="text-danger">*</span></label><input type="date"
                                                    name="fecha_devolucion" class="form-control" required></div>
                                            <div class="form-group col-md-6"><label>Funcionario que recibe <span
                                                        class="text-danger">*</span></label><select
                                                    name="funcionario_recibe_id" id="devolucionFuncionario"
                                                    class="form-control responsable-select" required></select></div>
                                            <div class="form-group col-md-6"><label>Custodio posterior <span
                                                        class="text-danger">*</span></label><select
                                                    name="custodio_posterior_id" id="devolucionCustodio"
                                                    class="form-control responsable-select" required></select></div>
                                            <div class="form-group col-md-6"><label>Ubicación posterior <span
                                                        class="text-danger">*</span></label><select
                                                    name="ubicacion_posterior_id" id="devolucionUbicacion"
                                                    class="form-control" required></select></div>
                                            <div class="form-group col-md-6"><label>Estado resultante <span
                                                        class="text-danger">*</span></label><select
                                                    name="estado_resultante_id" id="devolucionEstado"
                                                    class="form-control" required></select></div>
                                        </div>
                                        <div class="form-group"><label>Diagnóstico de devolución <span
                                                    class="text-danger">*</span></label><textarea
                                                name="diagnostico_devolucion" class="form-control" rows="3"
                                                maxlength="4000" required></textarea></div>
                                        <div class="form-group"><label>Novedades</label><textarea name="novedades"
                                                class="form-control" rows="3" maxlength="4000"></textarea></div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="confirmacionGeneral"
                                                name="confirmacion_general" value="1">
                                            <label class="custom-control-label" for="confirmacionGeneral">Confirmo que
                                                los elementos seleccionados fueron recibidos físicamente y que su estado
                                                fue verificado por el área de Sistemas.</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-3" id="btnGuardarDevolucion"><i
                                                class="fas fa-file-import mr-1"></i>Registrar devolución y generar
                                            acta</button>
                                    </form>
                                </div>

                                <div class="tab-pane" id="tabTrazabilidad">
                                    <h4>Trazabilidad individual</h4>
                                    <div class="row">
                                        <div class="form-group col-md-9"><label>Equipo</label><select
                                                id="trazabilidadEquipo" class="form-control"></select></div>
                                        <div class="form-group col-md-3 d-flex align-items-end"><button
                                                id="btnConsultarTrazabilidad" class="btn btn-info btn-block"><i
                                                    class="fas fa-history mr-1"></i>Consultar trazabilidad</button>
                                        </div>
                                    </div>
                                    <div id="lineaTrazabilidad" class="timeline"></div>
                                </div>

                                <div class="tab-pane" id="tabPendientes">
                                    <div class="table-responsive">
                                        <table id="tablaPendientes" class="table table-bordered table-striped w-100">
                                            <thead>
                                                <tr>
                                                    <th>Equipo</th>
                                                    <th>Serial</th>
                                                    <th>Tipo</th>
                                                    <th>Estado</th>
                                                    <th>Ubicación</th>
                                                    <th>Responsable</th>
                                                    <th>Detalle</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once '../MainFooter/footer.php'; ?>
    </div>

    <div class="modal fade" id="modalDetalleEquipo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">Detalle del equipo</h5><button type="button" class="close text-white"
                        data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="detalleEquipoContenido"></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Cerrar</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalComponente" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title">Agregar componente</h5><button type="button" class="close text-white"
                        data-dismiss="modal">&times;</button>
                </div>
                <form id="formComponente">
                    <div class="modal-body">
                        <input type="hidden" name="equipo_id" id="componenteEquipoId">
                        <div class="form-group"><label>Tipo</label><select name="tipo_componente_id" id="componenteTipo"
                                class="form-control" required></select></div>
                        <div class="form-group"><label>Estado</label><select name="estado_componente_id"
                                id="componenteEstado" class="form-control" required></select></div>
                        <div class="form-group"><label>Marca</label><input name="marca" class="form-control"
                                maxlength="80"></div>
                        <div class="form-group"><label>Modelo</label><input name="modelo" class="form-control"
                                maxlength="100"></div>
                        <div class="form-group"><label>Serial</label><input name="serial" class="form-control"
                                maxlength="120"></div>
                        <div class="form-group"><label>Observación</label><textarea name="observacion"
                                class="form-control" maxlength="2000"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMantenimiento" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Mantenimiento del equipo</h5><button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formMantenimiento">
                        <input type="hidden" name="equipo_id" id="mantenimientoEquipoId">
                        <div class="row">
                            <div class="form-group col-md-3"><label>Tipo <span
                                        class="text-danger">*</span></label><select name="tipo_mantenimiento_id"
                                    id="mantenimientoTipo" class="form-control" required></select></div>
                            <div class="form-group col-md-3"><label>Fecha <span
                                        class="text-danger">*</span></label><input type="date" name="fecha"
                                    class="form-control" required></div>
                            <div class="form-group col-md-6"><label>Responsable <span
                                        class="text-danger">*</span></label><select name="responsable_id"
                                    id="mantenimientoResponsable" class="form-control responsable-select"
                                    required></select></div>
                            <div class="form-group col-md-6"><label>Diagnóstico <span
                                        class="text-danger">*</span></label><textarea name="diagnostico"
                                    class="form-control" rows="3" maxlength="4000" required></textarea></div>
                            <div class="form-group col-md-6"><label>Actividad realizada <span
                                        class="text-danger">*</span></label><textarea name="actividad_realizada"
                                    class="form-control" rows="3" maxlength="4000" required></textarea></div>
                            <div class="form-group col-md-4"><label>Estado resultante <span
                                        class="text-danger">*</span></label><select name="estado_resultante_id"
                                    id="mantenimientoEstado" class="form-control" required></select></div>
                            <div class="form-group col-md-4"><label>Próxima fecha</label><input type="date"
                                    name="proxima_fecha" class="form-control"></div>
                            <div class="form-group col-md-4"><label>Observaciones</label><textarea name="observaciones"
                                    class="form-control" rows="2" maxlength="4000"></textarea></div>
                        </div>
                        <h6>Repuestos <button type="button" class="btn btn-xs btn-outline-primary"
                                id="btnAgregarRepuesto"><i class="fas fa-plus"></i> Agregar</button></h6>
                        <div id="listaRepuestos"></div>
                        <button type="submit" class="btn btn-warning mt-2"><i class="fas fa-tools mr-1"></i>Registrar
                            mantenimiento</button>
                    </form>
                    <hr>
                    <h5>Historial</h5>
                    <div id="historialMantenimiento" class="table-responsive"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Cerrar</button></div>
            </div>
        </div>
    </div>

    <?php require_once '../MainJS/JS.php'; ?>
    <script src="../../public/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="inventario.js"></script>
</body>

</html>
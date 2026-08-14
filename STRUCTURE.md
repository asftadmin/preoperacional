# Estructura del proyecto

## Arquitectura general

El proyecto utiliza PHP con un patrón MVC:

- `models/`: consultas SQL y transacciones.
- `controller/`: endpoints AJAX, sesión, permisos, validación y respuestas JSON.
- `view/`: vistas AdminLTE 3, JavaScript y documentos PDF.
- `database/`: scripts PostgreSQL versionados.
- `public/`: dependencias y recursos estáticos.
- `storage/`: archivos generados en tiempo de ejecución.

## Módulo de inventario de equipos de cómputo

El módulo es independiente de `vehiculos` y de todos sus catálogos. Sus objetos de base de datos utilizan `snake_case`.

### Archivos

- `database/inventario_equipos.sql`: tablas, índices, catálogos, menú y permisos iniciales.
- `models/InventarioEquipos.php`: inventario, componentes, asignaciones, devoluciones, custodia, trazabilidad, pendientes y actas.
- `controller/InventarioEquipos.php`: endpoint JSON y consulta PHP de la API institucional.
- `view/InventarioEquipos/inventario.php`: interfaz principal con cinco secciones.
- `view/InventarioEquipos/inventario.js`: Select2, DataTables, Daterangepicker, SweetAlert2 y flujos AJAX.
- `view/InventarioEquipos/registrar_equipo.php`: página independiente para registrar y editar equipos.
- `view/InventarioEquipos/registrar_equipo.js`: carga de catálogos, componentes y guardado del formulario práctico.
- `view/PDF/ActaEquipoCOF16.php`: actas CO-F-16 V4 con espacios impresos de firma, sin captura ni estados de firma digital.
- `storage/inventario_actas/`: archivos PDF generados, excluidos del control de versiones.

### Rutas

- Vista: `view/InventarioEquipos/inventario.php`
- Registro: `view/InventarioEquipos/registrar_equipo.php`
- Edición: `view/InventarioEquipos/registrar_equipo.php?id={equipo_id}`
- API: `controller/InventarioEquipos.php?op={operacion}`
- PDF: `view/PDF/ActaEquipoCOF16.php?tipo={ENTREGA|DEVOLUCION}&id={asignacion_id}`

### Operaciones JSON

- `inicial`
- `listar`
- `detalle`
- `guardarEquipo`
- `agregarComponente`
- `buscarEmpleado`
- `buscarEquipos`
- `crearAsignacion`
- `cargarAsignacion`
- `crearDevolucion`
- `listarMantenimientos`
- `crearMantenimiento`
- `trazabilidad`
- `pendientes`

Las operaciones de escritura requieren sesión, permiso `inventarioEquipos` y token CSRF.

### Tablas de catálogo

- `inventario_tipos_equipo`
- `inventario_estados_equipo`
- `inventario_ubicaciones`
- `inventario_tipos_componente`
- `inventario_estados_componente`
- `inventario_motivos_devolucion`
- `inventario_estados_recepcion`
- `inventario_estados_verificacion_serial`
- `inventario_tipos_mantenimiento`

### Tablas operativas

- `inventario_equipos`
- `inventario_componentes`
- `inventario_asignaciones`
- `inventario_asignacion_componentes`
- `inventario_asignacion_software`
- `inventario_devoluciones`
- `inventario_devolucion_componentes`
- `inventario_revision_devolucion`
- `inventario_custodias`
- `inventario_movimientos`
- `inventario_actas`
- `inventario_mantenimientos`
- `inventario_mantenimiento_repuestos`

### Relaciones principales

- Un equipo tiene componentes, custodias, movimientos, asignaciones y mantenimientos.
- Una asignación conserva la instantánea del empleado y tiene elementos, software, acta de entrega y una devolución opcional.
- Una devolución verifica individualmente todos los elementos entregados, registra la revisión física y genera un acta independiente.
- Las actas de entrega y devolución se relacionan con la misma asignación.

### Reglas importantes

- `inventario_equipos` no referencia `vehiculos`.
- Serial, código SIESA y activo fijo tienen índices únicos cuando aplican.
- Un equipo solo puede tener una asignación activa.
- Asignaciones, devoluciones, movimientos y actas no se eliminan.
- Una devolución con novedades o faltantes puede recibirse con el estado definido por el técnico o coordinador y permanece visible en pendientes.
- La custodia posterior se registra como movimiento interno, no como asignación a empleado.
- La trazabilidad siempre se consulta por `equipo_id`.
- El empleado asignado y su jefe inmediato se seleccionan y validan como activos mediante la API institucional.

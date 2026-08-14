# Plan del módulo de inventario de cómputo

## Alcance aprobado

Módulo nuevo para inventario, componentes, asignación, entrega, devolución, custodia, trazabilidad y actas CO-F-16 V4. Se mantiene separado del inventario y mantenimiento de vehículos.

## Estado de implementación

### Etapa 1. Inventario

- [x] Migración independiente en `snake_case`.
- [x] Registro y edición de características.
- [x] Validación de serial, código SIESA y activo fijo.
- [x] DataTable y filtros.
- [x] Custodio seleccionable.
- [x] Registro inicial de custodia y trazabilidad.
- [x] Formulario práctico de registro y edición en página independiente.
- [x] Secciones desplegables para datos técnicos, periféricos y mantenimiento.

### Etapa 2. Componentes

- [x] Accesorios individualizados.
- [x] Relación con el equipo principal.
- [x] Validación de serial.
- [x] Registro inicial y adición posterior.

### Etapa 3. Asignación

- [x] Búsqueda de empleados desde PHP.
- [x] Instantánea de datos del empleado y jefe.
- [x] Selección de componentes.
- [x] Software y diagnóstico.
- [x] Confirmaciones obligatorias.
- [x] Bloqueo transaccional y asignación activa única.
- [x] Jefe inmediato seleccionado y validado desde empleados activos.

### Etapa 4. Acta de entrega

- [x] PDF CO-F-16 V4.
- [x] Opción Entrega.
- [x] Contenido institucional controlado.
- [x] Sin nombres fijos y con espacios de firma del formato institucional.

### Etapa 5. Devolución

- [x] Selector obligatorio de equipos con asignación activa.
- [x] Botón Cargar asignación.
- [x] Verificación individual de elementos y seriales.
- [x] Revisión física y funcional.
- [x] Novedades y confirmación general.
- [x] Custodio, ubicación y estado posterior.
- [x] Cierre transaccional de la asignación.
- [x] Recepción permitida con novedades según el criterio técnico, conservándola en pendientes.

### Etapa 6. Acta de devolución

- [x] PDF separado relacionado con la asignación.
- [x] Opción Devolución.
- [x] Diagnóstico y novedades.
- [x] Espacios de firma impresos, sin flujo de firma digital.

### Etapa 7. Trazabilidad

- [x] Selector previo de equipo.
- [x] Historial individual y cronológico.
- [x] Usuario, fecha y responsables anterior/nuevo.

### Etapa 8. Pendientes y mantenimiento

- [x] Bandeja derivada de asignaciones, devoluciones y estados.
- [x] Identificación de devoluciones incompletas y novedades.
- [x] Estructura de mantenimiento exclusiva para equipos de cómputo.
- [x] Formulario operativo, historial y repuestos de mantenimiento.

### Etapa 9. Pruebas

- [x] Validación de sintaxis PHP.
- [x] Validación de sintaxis JavaScript.
- [x] Verificación estática de separación respecto a `vehiculos`.
- [x] Verificación de respuestas JSON sin sesión.
- [x] Prueba de enlace booleano `false` con PDO PostgreSQL.
- [ ] Ejecutar la migración en una base de pruebas.
- [ ] Pruebas integrales con la API institucional.
- [ ] Verificación visual del CO-F-16 con datos reales.
- [ ] Pruebas de concurrencia sobre asignación y devolución.
- [ ] Pruebas de permisos por rol.

## Pendientes de entorno

- La configuración local referencia `preoperacional_pruebas`, base que no existe actualmente.
- Debe crearse o seleccionarse una base de pruebas antes de ejecutar `database/inventario_equipos.sql`.
- Conviene trasladar la configuración sensible de la API de empleados a variables de entorno.
- El wireframe aprobado no fue proporcionado; la interfaz implementada sigue las convenciones existentes de AdminLTE 3.

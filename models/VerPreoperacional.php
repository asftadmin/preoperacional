<?php
/* CLASE VER PREOPERACIONAL - ROL VERIFICADOR */
class  VerPreoperacional extends Conectar
{



    /* LISTAR FORMULARIOS */
    public function  listarPreoperacional()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_formulario, vehi_placa, pre_hora, pre_fecha_crea_form,conductor_nombre_completo,tipo_id,tipo_nombre,
            CASE
                WHEN pre_estado = 0 THEN 'No aprobado'
                WHEN pre_estado = 1 THEN 'Aprobado'
                ELSE NULL 
            END AS pre_estado,
            pre_fecha_revision
        FROM (
        SELECT pre_formulario, vehi_placa, pre_hora,tipo_id, pre_fecha_crea_form, pre_estado,tipo_nombre, pre_fecha_revision,CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,
                ROW_NUMBER() OVER (PARTITION BY pre_formulario ORDER BY pre_fecha_crea_form DESC) AS rn
        FROM preoperacional
        INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
        INNER JOIN usuarios ON usuarios.user_id = preoperacional.pre_user
        LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        GROUP BY pre_formulario, pre_estado, pre_fecha_revision, vehi_placa, pre_hora, pre_fecha_crea_form, conductor_nombre_completo,tipo_id,tipo_nombre
        ) AS ranke
        WHERE rn = 1 AND pre_fecha_crea_form > '2024-10-01' AND tipo_id <> 23
        ORDER BY pre_fecha_crea_form desc";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    // FILTRO DE BUSQUEDA DE PREOPERACIONALES
    public function  filtropreoperacional($pre_user, $pre_vehiculo, $fecha_inicio,$fecha_final)
    {
        // Convertir cadena vacía en NULL si es necesario
        if ($pre_vehiculo === '') {
            $pre_vehiculo = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($pre_user === '') {
            $pre_user = null;
        }
         // Convertir cadena vacía en NULL si es necesario
         if ($fecha_inicio === '') {
            $fecha_inicio = null;
        }
        // Convertir cadena vacía en NULL si es necesario
        if ($fecha_final === '') {
            $fecha_final = null;
        }

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM Preoperacional(?, ?, ?, ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $pre_vehiculo);
        $sql->bindValue(2, $pre_user);
        $sql->bindValue(3, $fecha_inicio);
        $sql->bindValue(4, $fecha_final);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR KILOMETRAJE */
    public function  listarKilometraje()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_formulario, vehi_placa, pre_hora, pre_fecha_crea_form, pre_kilometraje_inicial       
            FROM (
                SELECT pre_formulario, vehi_placa, pre_hora, pre_fecha_crea_form,  pre_kilometraje_inicial,
                        ROW_NUMBER() OVER (PARTITION BY pre_formulario ORDER BY pre_fecha_crea_form DESC) AS rn
                FROM preoperacional
                INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
                INNER JOIN conductores ON conductores.conductor_vehiculo = vehiculos.vehi_id
                GROUP BY pre_formulario, vehi_placa, pre_hora, pre_fecha_crea_form, pre_kilometraje_inicial
            ) AS ranked
            WHERE rn = 1
            ORDER BY pre_fecha_crea_form ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  FiltroFechas()
    {

        $fecha_inicio = $_POST['fecha_inicio']; // Obtener la fecha de inicio desde un formulario HTML
        $fecha_final = $_POST['fecha_final'];

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT  pre_formulario , pre_kilometraje_inicial, pre_fecha_crea_form, vehi_placa
            FROM preoperacional INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id 
            WHERE pre_fecha_crea_form BETWEEN '$fecha_inicio' AND '$fecha_final'
            GROUP BY pre_formulario, pre_kilometraje_inicial, pre_fecha_crea_form, vehi_placa";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR RESPUESTAS */
    public function detalle($pre_formulario)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            operaciones.oper_nombre,
            suboperaciones.suboper_nombre,
            preoperacional.pre_repuesta,
            usuarios.user_cedula,
            CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,
            vehiculos.vehi_placa,
            tipo_vehiculo.tipo_nombre,
            tipo_vehiculo.tipo_id,
            preoperacional.pre_observaciones,
            preoperacional.pre_kilometraje_inicial
        FROM preoperacional
        INNER JOIN suboperaciones ON preoperacional.pre_suboper = suboperaciones.suboper_id
        INNER JOIN operaciones ON suboperaciones.suboper_oper = operaciones.oper_id
        INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
        INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        INNER JOIN usuarios ON usuarios.user_id = preoperacional.pre_user
        WHERE preoperacional.pre_formulario = ?
        GROUP BY suboperaciones.suboper_nombre, preoperacional.pre_repuesta, operaciones.oper_nombre, 
        usuarios.user_cedula,  conductor_nombre_completo, vehiculos.vehi_placa, tipo_vehiculo.tipo_nombre,tipo_vehiculo.tipo_id,
        preoperacional.pre_observaciones, preoperacional.pre_kilometraje_inicial;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $pre_formulario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR CALIFICACION */
    public function calificar($pre_formulario, $pre_estado, $pre_observaciones_ver)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = 'UPDATE preoperacional SET pre_estado = ?, pre_fecha_revision = NOW(), pre_observaciones_ver = ?  WHERE pre_formulario = ?';
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $pre_estado);
        $sql->bindValue(2, $pre_observaciones_ver);
        $sql->bindValue(3, $pre_formulario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR AL EDITAR */
    public function  mostrarPreoperacional($pre_formulario)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                pre_formulario,
                CASE
                    WHEN pre_estado = 0 THEN 'No aprobado'
                    WHEN pre_estado = 1 THEN 'Aprobado'
                    ELSE NULL
                END AS pre_estado
            FROM preoperacional
            INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
            WHERE pre_formulario = ? 
            GROUP BY pre_formulario, pre_estado;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $pre_formulario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR FORMULARIOS  X CONDUCTOR*/
    public function  listarPreoCond($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_formulario,pre_fecha_crea_form,
            CASE
                WHEN pre_estado = 0 THEN 'No aprobado'
                WHEN pre_estado = 1 THEN 'Aprobado'
                ELSE NULL 
            END AS pre_estado,
            pre_fecha_revision,pre_observaciones_ver, vehi_placa
            FROM preoperacional
            INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
            WHERE preoperacional.pre_user = ?
            GROUP BY  vehi_placa, pre_estado, pre_fecha_revision,pre_observaciones_ver,pre_formulario,pre_fecha_crea_form
            ORDER BY pre_fecha_crea_form DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* FORMULARIO FALLAS*/
    public function  listarFormularioFallas()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_fallas, vehi_placa, fecha_form
            FROM (
                SELECT pre_fallas, vehi_placa,fecha_form, 
                        ROW_NUMBER() OVER (PARTITION BY pre_fallas ORDER BY fecha_form DESC) AS rn
                FROM formulario_fallas
                INNER JOIN vehiculos ON formulario_fallas.form_vehiculos = vehiculos.vehi_id
                GROUP BY pre_fallas, vehi_placa, fecha_form
            ) AS ranked
            WHERE rn = 1
            ORDER BY fecha_form ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR RESPUESTAS */
    public function detalleFallas($pre_fallas)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            operaciones.oper_nombre,
            fallas.fallas_nombre,
            formulario_fallas.form_respuesta,
            usuarios.user_cedula,
            CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,
            vehiculos.vehi_placa,
            tipo_vehiculo.tipo_nombre
            
        FROM formulario_fallas
        INNER JOIN fallas ON formulario_fallas.form_fallas = fallas.id_fallas
        INNER JOIN operaciones ON fallas.fallasid_oper = operaciones.oper_id
        INNER JOIN vehiculos ON formulario_fallas.form_vehiculos = vehiculos.vehi_id
        INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        INNER JOIN usuarios ON usuarios.user_id = formulario_fallas.pre_user
        WHERE formulario_fallas.pre_fallas = ? and form_respuesta ='F'
        GROUP BY fallas.fallas_nombre, formulario_fallas.form_respuesta, operaciones.oper_nombre, usuarios.user_cedula,  conductor_nombre_completo, vehiculos.vehi_placa, tipo_vehiculo.tipo_nombre;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $pre_fallas);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR AL EDITAR */
    public function  mostrarFormularioFallas($pre_fallas)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                pre_fallas, 
            FROM formulario_fallas
            INNER JOIN vehiculos ON formulario_fallas.form_vehiculos = vehiculos.vehi_id
            INNER JOIN conductores ON conductores.conductor_vehiculo = vehiculos.vehi_id
            WHERE pre_fallas = ? 
            GROUP BY pre_fallas;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $pre_fallas);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR FORMULARIOS  X CONDUCTOR*/
    public function  listarFallaCond($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_fallas,fecha_form,vehi_placa
            FROM formulario_fallas
            INNER JOIN vehiculos ON formulario_fallas.form_vehiculos = vehiculos.vehi_id
            INNER JOIN conductores ON conductores.conductor_vehiculo = vehiculos.vehi_id
            INNER JOIN usuarios ON usuarios.user_id = conductores.conductor_usuario
            WHERE usuarios.user_id = ?
            GROUP BY  vehi_placa, pre_fallas,fecha_form
            ORDER BY fecha_form ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function  listarPreoperacional_Calendario($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_formulario as id,
                conductor_nombre_completo as title,
                pre_fecha_crea_form as start
                FROM (
                SELECT pre_formulario, CONCAT(usuarios.user_nombre, ' - ',vehiculos.vehi_placa) AS conductor_nombre_completo,pre_fecha_crea_form,user_id, 
                        ROW_NUMBER() OVER (PARTITION BY pre_formulario ORDER BY pre_fecha_crea_form DESC) AS rn
                FROM preoperacional
                INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
                INNER JOIN usuarios ON preoperacional.pre_user = usuarios.user_id
                LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
                GROUP BY pre_formulario, conductor_nombre_completo, pre_fecha_crea_form, user_id
            ) AS ranked
            WHERE rn = 1 and user_id=?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR CHECKEOS */
    public function  listarCheckeos()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT pre_formulario, vehi_placa, vehi_modelo,pre_hora, pre_fecha_crea_form,conductor_nombre_completo,tipo_id,tipo_nombre,
            CASE
                WHEN pre_estado = 0 THEN 'No aprobado'
                WHEN pre_estado = 1 THEN 'Aprobado'
                ELSE NULL 
            END AS pre_estado,
            pre_fecha_revision
        FROM (
        SELECT pre_formulario, vehi_placa, pre_hora,tipo_id, pre_fecha_crea_form, vehi_modelo,pre_estado,tipo_nombre, pre_fecha_revision,CONCAT(usuarios.user_nombre, ' ', usuarios.user_apellidos) AS conductor_nombre_completo,
                ROW_NUMBER() OVER (PARTITION BY pre_formulario ORDER BY pre_fecha_crea_form DESC) AS rn
        FROM preoperacional
        INNER JOIN vehiculos ON preoperacional.pre_vehiculo = vehiculos.vehi_id
        INNER JOIN usuarios ON usuarios.user_id = preoperacional.pre_user
        LEFT JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id
        GROUP BY pre_formulario, pre_estado, pre_fecha_revision, vehi_placa, vehi_modelo, pre_hora, pre_fecha_crea_form, conductor_nombre_completo,tipo_id,tipo_nombre
        ) AS ranke
        WHERE rn = 1 AND pre_fecha_crea_form > '2025-01-01' AND tipo_id = 23
        ORDER BY pre_fecha_crea_form desc";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
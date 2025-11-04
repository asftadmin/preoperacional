<?php 
    /* CLASE VEHICULO */
    class Vehiculo extends Conectar {

        /* REGISTRAR */
        public function insert_car($vehi_marca, $vehi_placa, $vehi_modelo, $vehi_soat_vence, $vehi_tecnicomecanica, $vehi_tarjeta_propiedad, $vehi_poliza, $vehi_poliza_vence, $vehi_tipo){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="INSERT INTO vehiculos (vehi_marca, vehi_placa, vehi_modelo, vehi_soat_vence, vehi_tecnicomecanica, vehi_tarjeta_propiedad,vehi_poliza,vehi_poliza_vence, vehi_tipo, vehi_estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,'stock')";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $vehi_marca); 
            $sql->bindValue(2, $vehi_placa); 
            $sql->bindValue(3, $vehi_modelo); 
            $sql->bindValue(4, $vehi_soat_vence); 
            $sql->bindValue(5, $vehi_tecnicomecanica); 
            $sql->bindValue(6, $vehi_tarjeta_propiedad);
            $sql->bindValue(7, $vehi_poliza); 
            $sql->bindValue(8, $vehi_poliza_vence); 
            $sql->bindValue(9, $vehi_tipo);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
        
        /* VERIFICAR SI EXISTE EL VEHICULO */
        public function carExiste($vehi_placa){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) AS count FROM vehiculos WHERE vehi_placa = ? ";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$vehi_placa);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        }

        /*   SELECCIONAMOES EL CORREO ELECTRONICO DEL USUARIO */
        public function get_car_poliza_email($vehi_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM vehiculos where vehi_id=?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $vehi_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ACTUALIZAR */
        public function update_car($vehi_id, $vehi_marca, $vehi_placa, $vehi_modelo, $vehi_soat_vence, $vehi_tecnicomecanica, $vehi_tarjeta_propiedad,$vehi_poliza, $vehi_poliza_vence, $vehi_tipo, $vehi_costo ){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE vehiculos SET vehi_marca=?, vehi_placa = ?, vehi_modelo = ?, vehi_soat_vence = ?, vehi_tecnicomecanica = ?, vehi_tarjeta_propiedad = ?, vehi_poliza=?, vehi_poliza_vence=?, vehi_tipo = ?, vehi_costo = ? WHERE vehi_id = ? ";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $vehi_marca);
            $sql -> bindValue(2, $vehi_placa);
            $sql -> bindValue(3, $vehi_modelo);
            $sql -> bindValue(4, $vehi_soat_vence);
            $sql -> bindValue(5, $vehi_tecnicomecanica);
            $sql -> bindValue(6, $vehi_tarjeta_propiedad);
            $sql -> bindValue(7, $vehi_poliza); 
            $sql -> bindValue(8, $vehi_poliza_vence); 
            $sql -> bindValue(9, $vehi_tipo);
			$sql->bindValue(10, $vehi_costo);
			$sql->bindValue(11, $vehi_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* ELIMINAR */
        public function delete_car($vehi_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "DELETE FROM vehiculos WHERE vehi_id = ?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $vehi_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();

        }

        /* LISTAR */
        public function get_car(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id  order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR PREOPERACIONALES Y REPORTES DIARIOS */
        public function get_car_preo(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id WHERE tv.tipo_id NOT IN (18,23) order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR DESPACHOS EXTERNOS */
        public function get_equipo_desp(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id WHERE tv.tipo_id NOT IN (18,23) order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
        /* LISTAR EQUIPOS LABORATORIO */
        public function get_equipos_lab(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id WHERE tv.tipo_id = 23";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR VOLQUETAS */
        public function get_car_volqueta(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id where tipo_id =1  order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR MAQUINARIA */
        public function get_car_maquinaria(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id where tipo_id in (5,6,7,8,9,11,12,14,15,16)  order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR FRESADORA */
        public function get_car_fresadora(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id where tipo_id = 11 order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR HERRAMIENTA MENOR */
        public function get_herramienta_menor(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id where tipo_id =18  order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR HERRAMIENTA MENOR DISPONIBLE */
        public function listar_herramienta_menor() {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id  where tipo_id =18 and vehi_estado='stock' order by vehi_placa";
           $sql = $conectar->prepare($sql);
           $sql->execute();
            return $resultado = $sql->fetchAll();
        }

        /* LISTAR MAQUINARIA ASIGNADA */
        public function get_car_maquinaria_asignada($alista_codigo){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT *
            FROM vehiculos INNER JOIN tipo_vehiculo ON vehiculos.vehi_tipo = tipo_vehiculo.tipo_id INNER JOIN alistamiento ON alistamiento.alista_vehi = vehiculos.vehi_id
            where alista_codigo=? and alista_resp_vehi ='A'
            order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $alista_codigo);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR MIXER */
        public function get_car_mixer(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.*, tv.tipo_nombre, tv.tipo_id FROM vehiculos v INNER JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id where tipo_id =2 order by tipo_nombre";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* LISTAR */
        public function get_car_user($user_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT v.vehi_id, v.vehi_placa FROM preoperacional p INNER JOIN vehiculos v ON p.pre_vehiculo = v.vehi_id 
            WHERE p.pre_user = ? GROUP BY v.vehi_id, v.vehi_placa";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $user_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        /* MOSTRAR AL EDITAR */
        public function get_car_id($vehi_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM vehiculos WHERE vehi_id=?";
            $sql = $conectar->prepare($sql);
            $sql -> bindValue(1, $vehi_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
    
        /* DETALLE MAQUINARIA */
     public function detalle($alista_codigo)
     {
         $conectar = parent::conexion();
         parent::set_names();
         $sql = "SELECT 
         CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS residente_nombre_completo,
         CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS inspector_nombre_completo,
         alista_vehi,
         v.vehi_placa,
		 v.vehi_estado,
         alista_resp_vehi,
         tv.tipo_nombre,
         alista_fecha,
         alista_observaciones,
		 observaciones_inspe,
		 alista_fecha_recibe,
         alista_codigo,
         alista_id,
		 o.obras_nom,
		 CASE
            WHEN alista_estado = 0 THEN 'No Funcional'
            WHEN alista_estado = 1 THEN 'Alistado'
            WHEN alista_estado = 2 THEN 'Traslado'
            WHEN alista_estado = 3 THEN 'Operativo'
            WHEN alista_estado = 4 THEN 'Finalizado'
            ELSE NULL 
        END AS alista_estado
        FROM alistamiento 
        LEFT JOIN obras o ON alista_obras = o.obras_id
        LEFT JOIN vehiculos v ON alista_vehi = v.vehi_id
        LEFT JOIN tipo_vehiculo tv ON v.vehi_tipo = tv.tipo_id
        LEFT JOIN usuarios u1 ON u1.user_id = alista_residente
        LEFT JOIN usuarios u2 ON u2.user_id = alista_inspec
        WHERE v.vehi_id = ?
        GROUP BY residente_nombre_completo,inspector_nombre_completo, o.obras_nom,alista_codigo,alista_id,
        alista_vehi, v.vehi_placa,v.vehi_estado, alista_resp_vehi, tv.tipo_nombre, alista_observaciones,alista_fecha,alista_estado,observaciones_inspe,alista_fecha_recibe;";
         $sql = $conectar->prepare($sql);
         $sql->bindValue(1, $alista_codigo);
         $sql->execute();
         return $resultado = $sql->fetchAll();
     }
    }
?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
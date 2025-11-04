<?php
/* CLASE OBRAS */
class Obras extends Conectar
{

    /* INSERTAR */
    public function insert_obras($obras_codigo, $obras_nom, $obra_estado,$tipo_obra)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO obras(obras_codigo, obras_nom,obra_estado,tipo_obra) VALUES (?,?,?,?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $obras_codigo);
        $sql->bindValue(2, $obras_nom);
        $sql->bindValue(3, $obra_estado);
        $sql->bindValue(4, $tipo_obra);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* VERIFICAR SI LA OBRA EXISTE */
    public function obraExiste($obras_codigo)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS count FROM obras WHERE obras_codigo = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $obras_codigo);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }

    /* ACTUALIZAR  */
    public function update_obras($obras_id, $obras_codigo, $obras_nom,$obra_estado,$tipo_obra)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE obras SET obras_codigo = ?, obras_nom = ?, obra_estado=?, tipo_obra=? WHERE obras_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $obras_codigo);
        $sql->bindValue(2, $obras_nom);
        $sql->bindValue(3, $obra_estado);
        $sql->bindValue(4, $tipo_obra);
        $sql->bindValue(5, $obras_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ELIMINAR */
    public function delete_obras($obras_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "DELETE FROM obras WHERE obras_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $obras_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR OBRAS */
    public function get_obras()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT *, CASE
            WHEN obra_estado = 0 THEN 'No activa'
            WHEN obra_estado = 1 THEN 'Activa'
            ELSE NULL 
            END AS obra_estado, CASE
            WHEN tipo_obra = 1 THEN 'ASFALTO'
            WHEN tipo_obra = 2 THEN 'CONCRETO'
            ELSE NULL 
            END AS tipo_obra FROM obras ";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR OBRAS ACTIVAS */
    public function get_obras_activas()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM obras where obra_estado=1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* MOSTRAR DATOS AL EDITAR */
    public function get_obras_id($obras_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM obras WHERE obras_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $obras_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}

?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE:JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
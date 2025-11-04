<?php
/* DESAROOLLADO POR:
ESTUDIANTE: ESTEFANIA MORENO REYES
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2023 */
?>
<?php
session_start();
class Conectar{
    protected $dbh;
    protected function Conexion(){
        try {
            // Cambiar los valores según tu configuración de PostgreSQL   192.168.0.200  masterd_asft
            $host = "172.16.5.2";
            $dbname = "preoperacional_vehiculos";
            $usuario = "postgres";
            $contrasena = "masterd_asft";

            $conectar = $this->dbh = new PDO("pgsql:host=$host;port=5432;dbname=$dbname", $usuario, $contrasena);
            return $conectar;
        } catch (PDOException $e) {
            print "¡Error BD!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function set_names(){
        return $this ->dbh ->query("SET NAMES 'utf8'");
    }
	public function getConexion(){
        return $this ->Conexion();
    }
    public static function ruta(){
        return "http://181.204.219.154:3396/preoperacional/";
    }
}


?>
<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2024 */
?>
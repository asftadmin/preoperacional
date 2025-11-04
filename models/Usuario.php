<?php
/* CLASE USUARIO */
class Usuario extends Conectar
{

    // INICIO DE SESION
    public function login()
    {

        $conectar = parent::conexion();

        // Verifica si se envió el formulario de ingreso
        if (isset($_POST["ingreso"])) {
            // Obtiene los valores del formulario
            $usuario = $_POST["usuario"];
            $password = $_POST["password"];


            if (empty($usuario) && empty($password)) {
                // Redirige a la página de inicio con mensaje de error (m=2)
                header("Location:" . conectar::ruta() . "index.php?m=2");
                exit();
            } else {
                // Consulta SQL para verificar las credenciales en la base de datos
                $sql = "SELECT * FROM usuarios WHERE user_usuario = ? AND user_contrasena = MD5(?) ";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $usuario);
                $stmt->bindValue(2, $password);    // Primera ocurrencia de user_contrasena

                $stmt->execute();

                // Obtiene el resultado de la consulta
                $resultado = $stmt->fetch();

                // Si se encontraron registros con las credenciales proporcionadas
                if (is_array($resultado) && count($resultado) > 0) {
                    // Almacena información del usuario en variables de sesión
                    $_SESSION["user_id"] = $resultado["user_id"];
                    $_SESSION["user_cedula"] = $resultado["user_cedula"];
                    $_SESSION["user_nombre"] = $resultado["user_nombre"];
                    $_SESSION["user_apellidos"] = $resultado["user_apellidos"];
                    $_SESSION["user_email"] = $resultado["user_email"];
                    $_SESSION["user_rol_usuario"] = $resultado["user_rol_usuario"];
                    $_SESSION["tipo_id"] = $resultado["tipo_id"];
                    $_SESSION["vehi_placa"] = $resultado["vehi_placa"];
                    $_SESSION["suboper_id"] = $resultado["suboper_id"];

                    // Redirige al inicio después del inicio de sesión exitoso
                    header("Location:" . Conectar::ruta() . "view/inicio/inicio.php");
                    exit();
                } else {
                    // Redirige a la página de inicio con mensaje de error (m=1)
                    header("Location:" . Conectar::ruta() . "index.php?m=1");
                    exit();
                }
            }
            //siguiente rol                     
        }
    }

    /* CLASE USUARIOS */

    /* INSERTAR */
    public function insert_user($user_cedula, $user_nombre, $user_apellidos, $user_email, $user_usuario, $user_contrasena, $user_rol_usuario)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO usuarios (user_cedula, user_nombre, user_apellidos, user_email, user_usuario, user_contrasena, user_rol_usuario) VALUES ( ?, ?, ?, ?, ?,MD5(?), ?);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_cedula);
        $sql->bindValue(2, $user_nombre);
        $sql->bindValue(3, $user_apellidos);
        $sql->bindValue(4, $user_email);
        $sql->bindValue(5, $user_usuario);
        $sql->bindValue(6, $user_contrasena);
        $sql->bindValue(7, $user_rol_usuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* VERIFICAR SI EL USUARIO EXISTE EN LA BD */
    public function userExiste($user_cedula)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS count FROM usuarios WHERE user_cedula = ?  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_cedula);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }

    /* ACTUALIZAR */
    public function update_user($user_id, $user_cedula, $user_nombre, $user_apellidos, $user_email, $user_usuario, $user_rol_usuario)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_cedula = ?,
            user_nombre = ?,
            user_apellidos = ?,
            user_email = ?,
            user_usuario = ?,
            user_rol_usuario = ? 
            WHERE user_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_cedula);
        $sql->bindValue(2, $user_nombre);
        $sql->bindValue(3, $user_apellidos);
        $sql->bindValue(4, $user_email);
        $sql->bindValue(5, $user_usuario);
        $sql->bindValue(6, $user_rol_usuario);
        $sql->bindValue(7, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    /* ELIMINAR */
    public function delete_user($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "DELETE FROM usuarios WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* LISTAR TODOS LOS ROLES */
    public function get_user()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios
            LEFT JOIN roles r ON usuarios.user_rol_usuario= r.rol_id";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* SELECT - MUESTRA SOLO EL ROL CONDUCTOR */
    public function get_user_cond()
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios 
            JOIN roles r ON usuarios.user_rol_usuario= r.rol_id WHERE r.rol_id IN (1,10)";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* SELECT - MUESTRA SOLO EL ROL CONDUCTOR QUE MANEJAN VEHICULOS */
    public function get_cond_vehi()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios 
            JOIN roles ON usuarios.user_rol_usuario= roles.rol_id
			LEFT JOIN conductores ON usuarios.user_id = conductores.conductor_usuario
			LEFT JOIN roles_conductor ON roles_conductor.rolcond_id = conductores.rolcond ";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* SELECT - MUESTRA SOLO EL ROL INSPECTOR Y RESIDENTE */
    public function get_user_inspector()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios LEFT JOIN roles r ON usuarios.user_rol_usuario= r.rol_id WHERE r.rol_id in (7,8,10) ";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* SELECT - MUESTRA SOLO EL ROL CONDUCTOR */
    public function get_user_reporte()
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios 
         JOIN roles r ON usuarios.user_rol_usuario= r.rol_id WHERE r.rol_id IN (1,2)";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    /* MOSTRAR DATOS AL EDITAR */
    public function get_user_id($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* ACTUALIZAR LA CONTRASEÑA */
    public function update_user_pass($user_id, $user_contrasena)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_contrasena = MD5(?) WHERE user_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_contrasena);
        $sql->bindValue(2, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /*   SELECCIONAMOES EL CORREO ELECTRONICO DEL USUARIO */
    public function get_user_email($user_email)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM usuarios WHERE user_email=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_email);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_preoperacionales_xid($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT  COUNT(distinct pre_formulario)  as total
            FROM preoperacional INNER JOIN usuarios ON preoperacional.pre_user = usuarios.user_id  WHERE user_id =? and DATE_TRUNC('month', pre_fecha_crea_form) = DATE_TRUNC('month', CURRENT_DATE);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_porcentaje_repdia($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            ROUND((COUNT(DISTINCT repdia_recib) * 100.0 / 25), 2) AS porcentaje_cumplimiento
            FROM
            reportes_diarios
            INNER JOIN
            usuarios ON reportes_diarios.repdia_cond = usuarios.user_id
            WHERE
            user_id = ?
            AND DATE_TRUNC('month', repdia_fech) = DATE_TRUNC('month', CURRENT_DATE);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_porcentaje_preo($user_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            ROUND((COUNT(DISTINCT pre_formulario) * 100.0 / 25), 2) AS porcentaje_preoperacionales
            FROM
            preoperacional
            INNER JOIN
            usuarios ON preoperacional.pre_user = usuarios.user_id
            WHERE
            user_id = ?
            AND DATE_TRUNC('month', pre_fecha_crea_form) = DATE_TRUNC('month', CURRENT_DATE);";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /*  RECUPERAR LA CONTRASEÑA POR MEDIO DEL CORREO ELECTRONICO */
    public function get_user_cambiar_pass($user_email)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_contrasena = CONCAT(SUBSTRING(MD5(RANDOM()::text) FROM 1 FOR 3), LPAD(FLOOR(RANDOM() * 1000)::text, 3, '0')) 
            WHERE user_email= ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_email);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /* FUNCION PARA ENCRIPTAR LA NUEVA CONTRASEÑA */
    public function get_user_encriptar_nuevo_pass($user_id, $user_contrasena)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE usuarios SET user_contrasena = MD5(?)
            WHERE user_id= ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user_contrasena);
        $sql->bindValue(2, $user_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insertarEmplNuevo($data)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO usuarios (user_cedula, user_nombre, user_apellidos, user_email, user_usuario, user_contrasena) VALUES (:cedu, :nomb, :apelli, :email, :user, MD5('Asft123*'))";
        $sql = $conectar->prepare($sql);
        $sql->bindParam(":cedu", $data['user_cedula']);
        $sql->bindParam(":nomb", $data['user_nombre']);
        $sql->bindParam(":apelli", $data['user_apellidos']);
        $sql->bindParam(":email", $data['user_email']);
        $sql->bindParam(":user", $data['user_usuario']);

        return $sql->execute();
    }
}
?>

<?php
/* DESAROOLLADO POR:
ESTUDIANTE: JACKSON DANIEL BORJA RUEDA
UNIDADES TECNOLOGICAS DE SANTANDER
BUCARAMANGA-SANTANDER
2025 */
?>
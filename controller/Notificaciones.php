<?php
require '../vendor/autoload.php';

use Pusher\Pusher;

class Notificaciones {
    private $pusher;

    public function __construct() {
        $this->pusher = new Pusher("APP_KEY", "APP_SECRET", "APP_ID", [
            'cluster' => 'CLUSTER',
            'useTLS' => true
        ]);
    }

    public function enviarNotificacionTiempoReal($userId, $mensaje) {
        $this->pusher->trigger("notificaciones_$userId", 'nueva_notificacion', ['mensaje' => $mensaje]);
    }
}
?>

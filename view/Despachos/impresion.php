<?php
require_once('../../config/conexion.php');

require __DIR__ . '/../../vendor/autoload.php';
// Autoload para escpos-php
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

try {
    // Capturar el valor de desp_id desde la URL
    if (!isset($_GET['desp_id']) || empty($_GET['desp_id'])) {
        throw new Exception("ID del despacho no especificado.");
    }

    $desp_id = intval($_GET['desp_id']); // Asegurarse de que es un entero

    // Conexión a la base de datos
    $conexion = new Conectar();
    $pdo = $conexion->getConexion();

    // Consulta
    $stmt = $pdo->prepare("SELECT desp_fech,desp_hora,obras_nom,vehi_placa,desp_recibo,desp_km_hr,desp_observaciones,desp_galones_autorizados,desp_galones,
        CONCAT(u1.user_nombre, ' ', u1.user_apellidos) AS conductor,
        CONCAT(u2.user_nombre, ' ', u2.user_apellidos) AS usuario,
        CONCAT(u3.user_nombre, ' ', u3.user_apellidos) AS despachador
        FROM despachos_acpm INNER JOIN obras ON despachos_acpm.desp_obra = obras.obras_id
        INNER JOIN usuarios u1 ON u1.user_id = despachos_acpm.desp_cond
        INNER JOIN usuarios u2 ON u2.user_id = despachos_acpm.desp_user
        INNER JOIN vehiculos ON vehiculos.vehi_id = despachos_acpm.desp_vehi
        INNER JOIN usuarios u3 ON u3.user_id = despachos_acpm.desp_despachador
        WHERE desp_id= :desp_id
        ");
    $stmt->bindParam(':desp_id', $desp_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Variables desde la consulta
        $desp_fech = date_format(new DateTime($result['desp_fech']), 'd/m/Y');
        $hora = new DateTime($result['desp_hora']);
        $desp_hora = $hora->format('H:i:s');
        $obras_nom = $result['obras_nom'];
        $vehi_placa = $result['vehi_placa'];
        $desp_recibo = $result['desp_recibo'];
        $desp_km_hr = $result['desp_km_hr'];
        $desp_observaciones = $result['desp_observaciones'];
        $desp_galones_autorizados = $result['desp_galones_autorizados'];
        $desp_galones = $result['desp_galones'];
        $conductor = $result['conductor'];
        $usuario = $result['usuario'];
        $despachador = $result['despachador'];

        // Impresión del ticket
        $nombre_impresora = 'SAT-22TUE';
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);
        for ($i = 0; $i < 2; $i++) { // Imprimir dos copias
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setTextSize(2, 2);
            $printer->text("CONTROL COMBUSTIBLE\n");

            $printer->setTextSize(1, 1);
            $printer->feed(2);
            $printer->text("Fecha: $desp_fech\n");
            $printer->text("Hora: $desp_hora\n");
            $printer->text("Obra: $obras_nom\n");
            $printer->text("Placa: $vehi_placa\n");
            $printer->text("Recibo: $desp_recibo\n");
            $printer->text("KM/HR: $desp_km_hr\n");
            $printer->text("Observaciones: $desp_observaciones\n");
            $printer->text("Gal. Autorizados: $desp_galones_autorizados\n");
            $printer->text("Gal. Entregados: $desp_galones\n");
            $printer->text("Autorizador: $usuario\n");
            $printer->text("Despachador: $despachador\n");
            $printer->text("Conductor: $conductor\n");
            $printer->feed(1);
            $printer->text("Firma: \n");
            $printer->feed(2);
            $printer->text("____________________");

            $printer->feed(3);
            $printer->cut();

            // Añadir un pequeño retraso entre impresiones (opcional)
            if ($i == 0) {
                usleep(500000); // 0.5 segundos de pausa
            }
        }

        $printer->pulse(); // Enviar señal a la impresora
        $printer->close();
        echo 'Impresión enviada correctamente';
    } else {
        echo "No se encontraron resultados para el despacho con ID $desp_id.";
    }
} catch (Exception $e) {
    echo 'Error al imprimir: ' . $e->getMessage();
}
?>
<script>
  // Espera a que se complete el proceso de impresión
  window.onload = function () {
    
    
      window.close();
  };
</script>

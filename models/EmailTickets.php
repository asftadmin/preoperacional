<?php

require_once __DIR__ . '/class.phpmailer.php';
require_once __DIR__ . '/class.smtp.php';
require_once __DIR__ . '/../config/conexion.php';

class EmailTickets extends PHPMailer
{
    private $configCorreo;

    public function __construct()
    {
        parent::__construct();

        $this->configCorreo =
            require __DIR__ . '/../config/correo.php';
    }

    /*
     * =====================================================
     * CONFIGURAR SMTP
     * =====================================================
     */
    private function configurarSmtp()
    {
        /*
         * No generar salida de depuración porque
         * posteriormente el envío se ejecutará desde
         * un controller que responde JSON.
         */
        $this->SMTPDebug = 0;

        $this->isSMTP();

        $this->Host =
            $this->configCorreo['host'];

        $this->Port =
            $this->configCorreo['port'];

        $this->SMTPAuth = true;

        $this->SMTPSecure =
            $this->configCorreo['encryption'];

        $this->Username =
            $this->configCorreo['username'];

        $this->Password =
            $this->configCorreo['password'];

        $this->CharSet = 'UTF-8';

        /*
         * Remitente.
         */
        $this->setFrom(
            $this->configCorreo['username'],
            'Mesa de Servicio - Sistemas'
        );

        $this->isHTML(true);
    }

    /*
     * =====================================================
     * ESCAPAR TEXTO HTML
     * =====================================================
     */
    private function escaparTexto(
        $valor,
        $valorDefecto = 'No especificado'
    ) {
        $valor = trim(
            (string) $valor
        );

        if ($valor === '') {
            $valor = $valorDefecto;
        }

        return htmlspecialchars(
            $valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    /*
     * =====================================================
     * FORMATEAR FECHA
     * =====================================================
     */
    private function formatearFecha($fecha)
    {
        if (
            empty($fecha) ||
            strtotime($fecha) === false
        ) {
            return 'No disponible';
        }

        return date(
            'd/m/Y h:i A',
            strtotime($fecha)
        );
    }

    /*
     * =====================================================
     * ENVIAR CORREO NUEVO TICKET
     * =====================================================
     */
    public function enviarNuevoTicket($ticket)
    {
        /*
         * Limpiar destinatarios anteriores si la
         * instancia llegara a reutilizarse.
         */
        $this->clearAddresses();

        $this->clearAttachments();

        /*
         * Configurar servidor SMTP.
         */
        $this->configurarSmtp();

        /*
         * =================================================
         * DESTINATARIO
         * =================================================
         */

        $correoAdministrador =
            trim(
                (string) $this->configCorreo[
                    'tickets_admin_email'
                ]
            );

        if (
            $correoAdministrador === '' ||
            !filter_var(
                $correoAdministrador,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new RuntimeException(
                'El correo del administrador de Tickets '
                . 'no está configurado correctamente.'
            );
        }

        $this->addAddress(
            $correoAdministrador,
            $this->configCorreo[
                'tickets_admin_name'
            ]
        );

        /*
         * =================================================
         * VALIDAR TICKET
         * =================================================
         */

        if (
            empty($ticket) ||
            empty($ticket['ticket_id']) ||
            empty($ticket['ticket_numero'])
        ) {
            throw new RuntimeException(
                'Los datos del ticket no son válidos '
                . 'para generar el correo.'
            );
        }

        /*
         * =================================================
         * CARGAR PLANTILLA HTML
         * =================================================
         */

        $rutaPlantilla =
            __DIR__
            . '/../public/CorreoNuevoTicket.html';

        if (
            !file_exists(
                $rutaPlantilla
            )
        ) {
            throw new RuntimeException(
                'No se encontró la plantilla '
                . 'CorreoNuevoTicket.html.'
            );
        }

        $cuerpo =
            file_get_contents(
                $rutaPlantilla
            );

        if ($cuerpo === false) {
            throw new RuntimeException(
                'No fue posible cargar la plantilla '
                . 'del correo.'
            );
        }

        /*
         * =================================================
         * CONSTRUIR URL DE GESTIÓN
         * =================================================
         */

        $urlGestion =
            Conectar::ruta()
            . 'view/TicketsSistemas/gestion.php?id='
            . rawurlencode(
                (string) $ticket['ticket_id']
            );

        /*
         * Botón HTML.
         */
        $enlace =
            '<a href="'
            . htmlspecialchars(
                $urlGestion,
                ENT_QUOTES,
                'UTF-8'
            )
            . '" '
            . 'style="'
            . 'display:inline-block;'
            . 'padding:12px 24px;'
            . 'background-color:#007bff;'
            . 'color:#ffffff;'
            . 'text-decoration:none;'
            . 'border-radius:4px;'
            . 'font-size:14px;'
            . 'font-weight:bold;'
            . '">'
            . 'Gestionar ticket'
            . '</a>';

        /*
         * =================================================
         * PREPARAR DATOS
         * =================================================
         */

        $numero =
            $this->escaparTexto(
                $ticket['ticket_numero']
            );

        $empleado =
            $this->escaparTexto(
                $ticket['empleado_nombre']
            );

        $area =
            $this->escaparTexto(
                $ticket['empleado_area']
            );

        $tipo =
            $this->escaparTexto(
                $ticket['tipo']
            );

        $categoria =
            $this->escaparTexto(
                isset($ticket['categoria'])
                    ? $ticket['categoria']
                    : ''
            );

        $prioridad =
            $this->escaparTexto(
                $ticket['prioridad']
            );

        $estado =
            $this->escaparTexto(
                $ticket['estado']
            );

        $fecha =
            $this->formatearFecha(
                $ticket['fecha_creacion']
            );

        $ubicacion =
            $this->escaparTexto(
                isset($ticket['ubicacion'])
                    ? $ticket['ubicacion']
                    : ''
            );

        $equipo =
            $this->escaparTexto(
                isset($ticket['equipo'])
                    ? $ticket['equipo']
                    : ''
            );

        $asunto =
            $this->escaparTexto(
                $ticket['asunto']
            );

        /*
         * La descripción puede contener saltos
         * de línea escritos por el usuario.
         */
        $descripcion =
            nl2br(
                $this->escaparTexto(
                    $ticket['descripcion']
                )
            );

        /*
         * =================================================
         * REEMPLAZAR MARCADORES
         * =================================================
         */

        $buscar = array(
            'xticket',
            'xempleado',
            'xarea',
            'xtipo',
            'xcategoria',
            'xprioridad',
            'xestado',
            'xfecha',
            'xubicacion',
            'xequipo',
            'xasunto',
            'xdescripcion',
            'xlink'
        );

        $reemplazar = array(
            $numero,
            $empleado,
            $area,
            $tipo,
            $categoria,
            $prioridad,
            $estado,
            $fecha,
            $ubicacion,
            $equipo,
            $asunto,
            $descripcion,
            $enlace
        );

        $cuerpo =
            str_replace(
                $buscar,
                $reemplazar,
                $cuerpo
            );

        /*
         * =================================================
         * ASUNTO DEL CORREO
         * =================================================
         */

        $this->Subject =
            '[Nuevo Ticket] '
            . $ticket['ticket_numero']
            . ' - '
            . $ticket['asunto'];

        /*
         * =================================================
         * CUERPO HTML
         * =================================================
         */

        $this->Body =
            $cuerpo;

        /*
         * =================================================
         * VERSIÓN TEXTO PLANO
         * =================================================
         */

        $this->AltBody =
            'Nuevo ticket registrado'
            . PHP_EOL
            . PHP_EOL
            . 'Ticket: '
            . $ticket['ticket_numero']
            . PHP_EOL
            . 'Empleado: '
            . $ticket['empleado_nombre']
            . PHP_EOL
            . 'Área: '
            . $ticket['empleado_area']
            . PHP_EOL
            . 'Tipo: '
            . $ticket['tipo']
            . PHP_EOL
            . 'Categoría: '
            . (
                isset($ticket['categoria'])
                    ? $ticket['categoria']
                    : ''
            )
            . PHP_EOL
            . 'Prioridad: '
            . $ticket['prioridad']
            . PHP_EOL
            . 'Estado: '
            . $ticket['estado']
            . PHP_EOL
            . 'Asunto: '
            . $ticket['asunto']
            . PHP_EOL
            . PHP_EOL
            . 'Gestionar ticket: '
            . $urlGestion;

        /*
         * =================================================
         * ENVIAR
         * =================================================
         */

        $enviado =
            $this->send();

        if (!$enviado) {
            throw new RuntimeException(
                'Error PHPMailer: '
                . $this->ErrorInfo
            );
        }

        return true;
    }
}

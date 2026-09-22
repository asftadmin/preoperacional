<?php

$config = array(
    'host' => getenv('MAIL_HOST'),
    'port' => getenv('MAIL_PORT'),
    'username' => getenv('MAIL_USERNAME'),
    'password' => getenv('MAIL_PASSWORD'),
    'encryption' => getenv('MAIL_ENCRYPTION'),
    'tickets_admin_email' => getenv('TICKETS_ADMIN_EMAIL'),
    'tickets_admin_name' => getenv('TICKETS_ADMIN_NAME')
);

/*
 * Validar variables obligatorias.
 */
$obligatorias = array(
    'host',
    'port',
    'username',
    'password',
    'tickets_admin_email'
);

foreach ($obligatorias as $campo) {
    if (
        !isset($config[$campo]) ||
        trim((string) $config[$campo]) === ''
    ) {
        throw new RuntimeException(
            'Configuración de correo incompleta: '
            . $campo
        );
    }
}

/*
 * Valores por defecto.
 */
$config['port'] =
    (int) $config['port'];

if (
    empty($config['encryption'])
) {
    $config['encryption'] = 'tls';
}

if (
    empty($config['tickets_admin_name'])
) {
    $config['tickets_admin_name'] =
        'Administrador Mesa de Servicio';
}

return $config;

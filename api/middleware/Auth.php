<?php

require_once __DIR__ . '/../config/ApiConfig.php';
require_once __DIR__ . '/../helpers/Response.php';

class ApiAuth
{
    public static function validar(): void
    {
        $headers = getallheaders();

        $authorization = '';

        if (isset($headers['Authorization'])) {
            $authorization = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authorization = $headers['authorization'];
        }

        if (empty($authorization)) {
            ApiResponse::json(
                false,
                'No se proporcionó autorización.',
                null,
                401
            );
        }

        if (!preg_match('/Bearer\s+(\S+)/', $authorization, $matches)) {
            ApiResponse::json(
                false,
                'Formato de autorización no válido.',
                null,
                401
            );
        }

        $tokenRecibido = $matches[1];
        $tokenConfigurado = ApiConfig::getToken();

        if (empty($tokenConfigurado)) {
            ApiResponse::json(
                false,
                'La API no tiene configurado el token de acceso.',
                null,
                500
            );
        }

        if (!hash_equals($tokenConfigurado, $tokenRecibido)) {
            ApiResponse::json(
                false,
                'Token de acceso no válido.',
                null,
                401
            );
        }
    }
}

<?php

class ApiConfig
{
    public static function getToken(): string
    {
        return getenv('TICKETS_API_TOKEN') ?: '';
    }
}

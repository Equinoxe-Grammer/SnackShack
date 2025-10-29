<?php
namespace App\Config;

class AppConfig
{
    public static function database(): array
    {
        return [
            'host' => getenv('SNACKSHOP_DB_HOST') ?: '127.0.0.1',
            'name' => getenv('SNACKSHOP_DB_NAME') ?: 'snackshop',
            'user' => getenv('SNACKSHOP_DB_USER') ?: 'root',
            'pass' => getenv('SNACKSHOP_DB_PASS') ?: '',
            'charset' => getenv('SNACKSHOP_DB_CHARSET') ?: 'utf8mb4'
        ];
    }
}

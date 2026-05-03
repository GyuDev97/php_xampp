<?php
declare(strict_types=1);

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

function app_config(): array
{
    $default = require __DIR__ . '/../config.example.php';
    $local = __DIR__ . '/../config.php';
    if (is_file($local)) {
        $config = array_merge($default, require $local);
    } else {
        $config = $default;
    }
    if (!empty($config['timezone'])) {
        date_default_timezone_set($config['timezone']);
    }
    return $config;
}

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function fail_json(string $message, int $status = 400): void
{
    json_response(['detail' => $message], $status);
}

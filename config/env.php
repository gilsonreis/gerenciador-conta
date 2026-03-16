<?php

$env = [
    'DB_HOST' => '',
    'DB_NAME' => '',
    'DB_USER' => '',
    'DB_PASS' => '',
    'SMTP_HOST' => '',
    'SMTP_PORT' => '',
    'SMTP_USER' => '',
    'SMTP_PASS' => '',
];

// Lógica de Sobrescrita de Ambiente
if (file_exists(__DIR__ . '/env.local.php')) {
    $override = require __DIR__ . '/env.local.php';
    $env = array_merge($env, $override);
} elseif (file_exists(__DIR__ . '/env.prod.php')) {
    $override = require __DIR__ . '/env.prod.php';
    $env = array_merge($env, $override);
} else {
    throw new Exception('Arquivo de ambiente não encontrado.');
}

return $env;

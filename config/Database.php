<?php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $env = require __DIR__ . '/env.php';

            $host = $env['DB_HOST'];
            $dbName = $env['DB_NAME'];
            $user = $env['DB_USER'];
            $password = $env['DB_PASS'];
            
            try {
                $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, $user, $password, $options);
            } catch (PDOException $e) {
                // Return explicitly the exception or log
                die(json_encode(['erro' => 'Erro de conexão com o banco de dados.', 'detalhe' => $e->getMessage()]));
            }
        }
        return self::$instance;
    }
}

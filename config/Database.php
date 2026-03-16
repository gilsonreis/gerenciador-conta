<?php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: '';
            $dbName = getenv('DB_NAME') ?: 'controle_contas';
            $user = getenv('DB_USER') ?: 'user_contas';
            // Em docker ou local, ajustar a senha conforme ambiente
            $password = getenv('DB_PASS') ?: 'senha_contas';
            
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

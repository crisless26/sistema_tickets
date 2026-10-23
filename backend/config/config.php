<?php

class Db {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Lee las variables de entorno utilizando getenv() o $_ENV
        $this->host     = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name  = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? '';
        $this->username = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? '';
        $this->password = getenv('DB_PASS') ?: $_ENV['DB_PASS'] ?? '';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host .
                ";dbname=" . $this->db_name .
                ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC   
                ]
            );
        } catch (PDOException $exception) {
            echo json_encode(["error" => "Error de conexión: " . $exception->getMessage()]);
            exit;
        }
        
        return $this->conn;
    }
}
?>
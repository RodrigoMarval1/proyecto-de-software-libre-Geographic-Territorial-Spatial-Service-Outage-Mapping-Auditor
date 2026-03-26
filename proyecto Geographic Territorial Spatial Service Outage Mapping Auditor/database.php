<?php
// database.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Database {
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = ""; // Contraseña por defecto en Laragon
    private $db_name = "outage_mapping";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Conectar a MySQL genérico primero para poder crear la DB si no existe
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 1. Crear Base de datos si no existe
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            
            // 2. Usar la base de datos
            $this->conn->exec("USE `" . $this->db_name . "`");
            $this->conn->exec("set names utf8mb4");

            // 3. Crear tablas automáticamente para que el usuario no tenga que importar schema.sql
            $table1 = "CREATE TABLE IF NOT EXISTS reports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                category ENUM('agua', 'electricidad', 'vialidad', 'otros') NOT NULL,
                lat DECIMAL(10, 8) NOT NULL,
                lng DECIMAL(11, 8) NOT NULL,
                image_path VARCHAR(255),
                current_state ENUM('Reportado', 'En Revisión', 'Resuelto') DEFAULT 'Reportado',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (category),
                INDEX (current_state)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->conn->exec($table1);

            $table2 = "CREATE TABLE IF NOT EXISTS report_status_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                report_id INT NOT NULL,
                previous_state ENUM('Reportado', 'En Revisión', 'Resuelto'),
                new_state ENUM('Reportado', 'En Revisión', 'Resuelto') NOT NULL,
                comments TEXT,
                changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->conn->exec($table2);

        } catch(PDOException $exception) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                "message" => "Error crítico de Base de Datos MySQL: " . $exception->getMessage(),
                "error" => true
            ]);
            exit;
        }

        return $this->conn;
    }
}
?>

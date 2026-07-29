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

        // Probar puertos de MySQL (3307, 3306)
        $ports = [3307, 3306];
        foreach ($ports as $port) {
            try {
                $testConn = new PDO("mysql:host=" . $this->host . ";port=" . $port, $this->username, $this->password, [
                    PDO::ATTR_TIMEOUT => 2,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                
                // 1. Crear Base de datos si no existe
                $testConn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $testConn->exec("USE `" . $this->db_name . "`");
                $testConn->exec("set names utf8mb4");

                // 2. Crear tabla reports si no existe
                $table1 = "CREATE TABLE IF NOT EXISTS reports (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    category VARCHAR(50) NOT NULL,
                    lat DECIMAL(10, 8) NOT NULL,
                    lng DECIMAL(11, 8) NOT NULL,
                    image_path VARCHAR(255),
                    current_state ENUM('Reportado', 'En Revisión', 'Resuelto') DEFAULT 'Reportado',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX (category),
                    INDEX (current_state)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                $testConn->exec($table1);

                // Aquí actualiza la columna category a VARCHAR(50) para poder guardar los nuevos tipos de incidentes sin errores
                try {
                    $testConn->exec("ALTER TABLE reports MODIFY COLUMN category VARCHAR(50) NOT NULL;");
                } catch (Exception $e) {}

                $this->conn = $testConn;
                return $this->conn;
            } catch (Exception $e) {
                // Intentar el siguiente puerto o continuar al fallback
            }
        }

        // Fallback a SQLite para asegurar cero errores si MySQL no está activo
        try {
            $sqliteFile = __DIR__ . '/outage_mapping.sqlite';
            $sqliteConn = new PDO("sqlite:" . $sqliteFile);
            $sqliteConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sqliteConn->exec("CREATE TABLE IF NOT EXISTS reports (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                category TEXT NOT NULL,
                lat REAL NOT NULL,
                lng REAL NOT NULL,
                image_path TEXT,
                current_state TEXT DEFAULT 'Reportado',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            $this->conn = $sqliteConn;
            return $this->conn;
        } catch (Exception $e) {
            return null;
        }
    }
}
?>

CREATE DATABASE IF NOT EXISTS outage_mapping;
USE outage_mapping;

CREATE TABLE IF NOT EXISTS reports (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS report_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    previous_state ENUM('Reportado', 'En Revisión', 'Resuelto'),
    new_state ENUM('Reportado', 'En Revisión', 'Resuelto') NOT NULL,
    comments TEXT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Script temporal para crear el TRIGGER de cambio de estado
DELIMITER //
CREATE TRIGGER after_report_status_update
AFTER UPDATE ON reports
FOR EACH ROW
BEGIN
    IF OLD.current_state != NEW.current_state THEN
        INSERT INTO report_status_history (report_id, previous_state, new_state, comments)
        VALUES (NEW.id, OLD.current_state, NEW.current_state, 'Cambio de estado automático o administrativo');
    END IF;
END;
//
DELIMITER ;

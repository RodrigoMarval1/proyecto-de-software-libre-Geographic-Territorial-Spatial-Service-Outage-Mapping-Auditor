<?php
// api/endpoints/save_report.php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$category = $_POST['category'] ?? '';
$lat = $_POST['lat'] ?? 0;
$lng = $_POST['lng'] ?? 0;
$image_path = null;

// Validación mínima
if (empty($title) || empty($category) || empty($lat) || empty($lng)) {
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos. Título, categoría, y coordenadas son requeridos."]);
    exit;
}

// Lógica de carga de archivo (foto referencia)
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "uploads/";
    
    // Crear el directorio si no existe
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Generar un nombre único para evitar sobreescritura
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid("report_") . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_path = "uploads/" . $new_filename; // Ruta relativa para el Frontend
    }
}

// Sanitización de texto para mitigar XSS o Inyecciones visuales
$title = htmlspecialchars(strip_tags($title));
$description = htmlspecialchars(strip_tags($description));
$category = htmlspecialchars(strip_tags($category));

$saved = false;
$report_id = time();

if ($db) {
    try {
        $query = "INSERT INTO reports (title, description, category, lat, lng, image_path, current_state) 
                  VALUES (:title, :description, :category, :lat, :lng, :image_path, 'Reportado')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':image_path', $image_path);
        if ($stmt->execute()) {
            $report_id = $db->lastInsertId();
            $saved = true;
        }
    } catch (Exception $e) {
        $saved = false;
    }
}

// RESPALDO EN ARCHIVO JSON (Garantiza funcionamiento sin importar la configuración)
$backup_file = 'reportes_guardados.json';
$current_data = file_exists($backup_file) ? json_decode(file_get_contents($backup_file), true) : [];
if (!is_array($current_data)) $current_data = [];
$current_data[] = [
    "id" => $report_id,
    "title" => $title,
    "description" => $description,
    "category" => $category,
    "lat" => (float)$lat,
    "lng" => (float)$lng,
    "image_path" => $image_path,
    "current_state" => 'Reportado',
    "created_at" => date('Y-m-d H:i:s')
];
file_put_contents($backup_file, json_encode($current_data, JSON_PRETTY_PRINT));

http_response_code(201);
echo json_encode(["message" => "Reporte de incidente guardado exitosamente.", "id" => $report_id]);
?>

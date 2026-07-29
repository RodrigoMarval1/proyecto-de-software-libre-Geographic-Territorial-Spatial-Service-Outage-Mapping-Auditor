<?php
// api/endpoints/get_reports.php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

// Filtro opcional por categoría
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;

$features = [];

if ($db) {
    try {
        $query = "SELECT id, title, description, category, lat, lng, image_path, current_state, created_at FROM reports";
        if ($category_filter && $category_filter !== 'todos') {
            $query .= " WHERE category = :category";
        }
        $stmt = $db->prepare($query);
        if ($category_filter && $category_filter !== 'todos') {
            $stmt->bindParam(':category', $category_filter);
        }
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $feature = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [(float)$row['lng'], (float)$row['lat']]
                ],
                "properties" => [
                    "id" => $row['id'],
                    "title" => $row['title'],
                    "description" => $row['description'],
                    "category" => $row['category'],
                    "image_path" => $row['image_path'],
                    "current_state" => $row['current_state'],
                    "created_at" => $row['created_at']
                ]
            ];
            array_push($features, $feature);
        }
    } catch (Exception $e) {
        $features = [];
    }
}

// Si la DB no retornó registros o no estuvo disponible, usar el respaldo JSON
if (empty($features)) {
    $backup_file = 'reportes_guardados.json';
    if (file_exists($backup_file)) {
        $json_data = json_decode(file_get_contents($backup_file), true);
        if (is_array($json_data)) {
            foreach ($json_data as $row) {
                if ($category_filter && $category_filter !== 'todos' && ($row['category'] ?? '') !== $category_filter) {
                    continue;
                }
                $feature = [
                    "type" => "Feature",
                    "geometry" => [
                        "type" => "Point",
                        "coordinates" => [(float)($row['lng'] ?? 0), (float)($row['lat'] ?? 0)]
                    ],
                    "properties" => [
                        "id" => $row['id'] ?? 1,
                        "title" => $row['title'] ?? 'Sin Título',
                        "description" => $row['description'] ?? '',
                        "category" => $row['category'] ?? 'otros',
                        "image_path" => $row['image_path'] ?? null,
                        "current_state" => $row['current_state'] ?? 'Reportado',
                        "created_at" => $row['created_at'] ?? date('Y-m-d H:i:s')
                    ]
                ];
                array_push($features, $feature);
            }
        }
    }
}

$geojson = [
    "type" => "FeatureCollection",
    "features" => $features
];

header('Content-Type: application/json');
echo json_encode($geojson);
?>

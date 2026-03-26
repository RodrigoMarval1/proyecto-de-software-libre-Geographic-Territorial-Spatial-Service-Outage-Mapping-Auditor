<?php
// api/endpoints/get_reports.php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

// Filtro opcional por categoría
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;

$query = "SELECT id, title, description, category, lat, lng, image_path, current_state, created_at 
          FROM reports";
          
if ($category_filter) {
    if($category_filter !== 'todos'){
        $query .= " WHERE category = :category";
    }
}

$stmt = $db->prepare($query);

if ($category_filter && $category_filter !== 'todos') {
    $stmt->bindParam(':category', $category_filter);
}

$stmt->execute();

$features = [];

// Formatear salida ESTRICTAMENTE en GeoJSON
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $feature = [
        "type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [
                (float)$row['lng'], // GeoJSON requiere longitud primero
                (float)$row['lat']  // luego latitud
            ]
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

$geojson = [
    "type" => "FeatureCollection",
    "features" => $features
];

header('Content-Type: application/json');
echo json_encode($geojson);
?>

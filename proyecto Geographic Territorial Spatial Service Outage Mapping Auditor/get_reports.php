<?php
// api/endpoints/get_reports.php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

// Filtro opcional por categoría
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;

// Filtros opcionales por límites geográficos de zona
$min_lat = (isset($_GET['min_lat']) && is_numeric($_GET['min_lat'])) ? (float) $_GET['min_lat'] : null;
$max_lat = (isset($_GET['max_lat']) && is_numeric($_GET['max_lat'])) ? (float) $_GET['max_lat'] : null;
$min_lng = (isset($_GET['min_lng']) && is_numeric($_GET['min_lng'])) ? (float) $_GET['min_lng'] : null;
$max_lng = (isset($_GET['max_lng']) && is_numeric($_GET['max_lng'])) ? (float) $_GET['max_lng'] : null;

$where_clauses = [];
$params = [];

if ($category_filter && $category_filter !== 'todos') {
    $where_clauses[] = "category = :category";
    $params[':category'] = $category_filter;
}

if ($min_lat !== null && $max_lat !== null && $min_lng !== null && $max_lng !== null) {
    $where_clauses[] = "lat BETWEEN :min_lat AND :max_lat AND lng BETWEEN :min_lng AND :max_lng";
    $params[':min_lat'] = $min_lat;
    $params[':max_lat'] = $max_lat;
    $params[':min_lng'] = $min_lng;
    $params[':max_lng'] = $max_lng;
}

$query = "SELECT id, title, description, category, lat, lng, image_path, current_state, created_at FROM reports";
if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt = $db->prepare($query);
foreach ($params as $param_key => $param_val) {
    $stmt->bindValue($param_key, $param_val);
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
                (float) $row['lng'], // GeoJSON requiere longitud primero
                (float) $row['lat']  // luego latitud
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
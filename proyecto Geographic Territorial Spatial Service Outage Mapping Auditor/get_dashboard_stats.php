<?php
// api/endpoints/get_dashboard_stats.php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$stats = [];

// 1. Total de reportes por categoría
$query_cat = "SELECT category, COUNT(*) as total FROM reports GROUP BY category";
$stmt_cat = $db->prepare($query_cat);
$stmt_cat->execute();
$stats['by_category'] = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// 2. Conteo de Estados
$query_status = "SELECT current_state, COUNT(*) as total FROM reports GROUP BY current_state";
$stmt_status = $db->prepare($query_status);
$stmt_status->execute();
$stats['by_status'] = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

// 3. Top 5 sectores con más fallas
// Como manejamos lat/lng, aproximamos sectores truncando coordenadas a ~1 km (3 decimales) 
// o simplemente agrupando por coordenada exacta/sector si tuviéramos tabla geométrica.
$query_sectors = "SELECT ROUND(lat, 3) as sector_lat, ROUND(lng, 3) as sector_lng, COUNT(*) as count 
                  FROM reports 
                  GROUP BY ROUND(lat, 3), ROUND(lng, 3) 
                  ORDER BY count DESC 
                  LIMIT 5";
$stmt_sectors = $db->prepare($query_sectors);
$stmt_sectors->execute();
$stats['top_sectors'] = $stmt_sectors->fetchAll(PDO::FETCH_ASSOC);

// 4. Reportes Recientes
$query_recent = "SELECT id, title, category, created_at, current_state FROM reports ORDER BY created_at DESC LIMIT 5";
$stmt_recent = $db->prepare($query_recent);
$stmt_recent->execute();
$stats['recent_reports'] = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($stats);
?>

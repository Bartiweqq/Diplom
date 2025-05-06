<?php
global $pdo;
require_once 'db.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';

$sql = "SELECT e.id, 
               e.title, 
               e.description, 
               e.latitude, 
               e.longitude,
               ROUND(AVG(r.rating),1) as avg_rating
        FROM events e
        LEFT JOIN event_reviews r ON e.id = r.event_id
        WHERE 1=1";
$params = [];

if (!empty($category)) {
    $sql .= " AND e.category = ?";
    $params[] = $category;
}

if (!empty($city)) {
    $sql .= " AND e.city = ?";
    $params[] = $city;
}

$sql .= " GROUP BY e.id ORDER BY e.event_date LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Подставляем "Нет оценок" если рейтинг NULL
foreach ($events as &$event) {
    if ($event['avg_rating'] === null) {
        $event['avg_rating'] = 'Нет оценок';
    }
}

header('Content-Type: application/json');
echo json_encode($events);

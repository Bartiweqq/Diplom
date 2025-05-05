<?php
global $pdo;
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../frontend/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $event_date = $_POST['event_date'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $city = $_POST['city'];
    $max_participants = !empty($_POST['max_participants']) ? $_POST['max_participants'] : null;

    // Узнаем ID пользователя по никнейму
    $stmt = $pdo->prepare("SELECT id FROM users WHERE nickname = ?");
    $stmt->execute([$_SESSION['user']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $organizer_id = $user['id'];

        try {
            $stmt = $pdo->prepare("INSERT INTO events 
                (title, description, category, event_date, latitude, longitude, city, organizer_id, max_participants) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $category, $event_date, $latitude, $longitude, $city, $organizer_id, $max_participants]);

            header("Location: ../frontend/index.php");

        } catch (PDOException $e) {
            echo "Ошибка при создании мероприятия: " . $e->getMessage();
        }

    } else {
        echo "Пользователь не найден.";
    }

} else {
    echo "Неверный метод запроса.";
}
?>

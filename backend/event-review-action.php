<?php
global $pdo;
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Только авторизованные пользователи могут оставлять отзывы.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = (int)$_POST['event_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        echo "Оценка должна быть от 1 до 5.";
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO event_reviews (event_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$eventId, $_SESSION['user_id'], $rating, $comment]);

    header("Location: ../frontend/event.php?id=" . $eventId);
    exit();
} else {
    echo "Неверный метод запроса.";
}
?>

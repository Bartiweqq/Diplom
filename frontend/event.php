<?php
session_start();
require_once '../backend/db.php';

if (!isset($_GET['id'])) {
    echo "Мероприятие не указано.";
    exit();
}

$eventId = (int)$_GET['id'];

// Получаем данные мероприятия
$stmt = $pdo->prepare("SELECT e.*, u.nickname AS organizer_nickname 
                       FROM events e 
                       JOIN users u ON e.organizer_id = u.id 
                       WHERE e.id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Мероприятие не найдено.";
    exit();
}

// Получаем средний рейтинг мероприятия
$stmt = $pdo->prepare("SELECT ROUND(AVG(rating),1) AS avg_rating FROM event_reviews WHERE event_id = ?");
$stmt->execute([$eventId]);
$ratingData = $stmt->fetch(PDO::FETCH_ASSOC);
$avgRating = $ratingData['avg_rating'] ? $ratingData['avg_rating'] : 'Нет оценок';

// Получаем отзывы
$stmt = $pdo->prepare("SELECT r.rating, r.comment, u.nickname 
                       FROM event_reviews r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.event_id = ?");
$stmt->execute([$eventId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['title']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2><?php echo htmlspecialchars($event['title']); ?></h2>
<p><strong>Описание:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
<p><strong>Категория:</strong> <?php echo htmlspecialchars($event['category']); ?></p>
<p><strong>Город:</strong> <?php echo htmlspecialchars($event['city']); ?></p>
<p><strong>Организатор:</strong> <?php echo htmlspecialchars($event['organizer_nickname']); ?></p>
<p><strong>Средний рейтинг:</strong> <?php echo $avgRating; ?></p>

<hr>

<h3>Отзывы участников</h3>
<?php if ($reviews): ?>
    <?php foreach ($reviews as $review): ?>
        <div class="event-item">
            <strong><?php echo htmlspecialchars($review['nickname']); ?></strong><br>
            Оценка: <?php echo htmlspecialchars($review['rating']); ?><br>
            <?php echo htmlspecialchars($review['comment']); ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Пока нет отзывов.</p>
<?php endif; ?>

<hr>

<?php if (isset($_SESSION['user_id'])): ?>
    <h3>Оценить мероприятие</h3>
    <form action="../backend/event-review-action.php" method="POST">
        <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
        <label>Оценка (1-5):
            <input type="number" name="rating" min="1" max="5" required>
        </label><br>
        <label>Комментарий:<br>
            <textarea name="comment"></textarea>
        </label><br>
        <button type="submit">Отправить</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Войдите</a>, чтобы оставить отзыв.</p>
<?php endif; ?>

<p><a href="index.php">← Вернуться на главную</a></p>

</body>
</html>

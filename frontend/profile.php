<?php
global $pdo;
session_start();
require_once '../backend/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$nickname = $_SESSION['user'];

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT id FROM users WHERE nickname = ?");
$stmt->execute([$nickname]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Пользователь не найден.";
    exit();
}

$user_id = $user['id'];

// Получаем все интересы
$all_interests = $pdo->query("SELECT * FROM interests")->fetchAll(PDO::FETCH_ASSOC);

// Получаем интересы пользователя
$stmt = $pdo->prepare("SELECT interest_id FROM user_interests WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_interests = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Обработка сохранения интересов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['interests'])) {
    // Удаляем старые интересы
    $pdo->prepare("DELETE FROM user_interests WHERE user_id = ?")->execute([$user_id]);

    // Вставляем новые интересы
    foreach ($_POST['interests'] as $interest_id) {
        $stmt = $pdo->prepare("INSERT INTO user_interests (user_id, interest_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $interest_id]);
    }

    header("Location: profile.php");
    exit();
}

// Получаем мероприятия пользователя
$stmt = $pdo->prepare("SELECT * FROM events WHERE organizer_id = ?");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой профиль</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Привет, <?php echo htmlspecialchars($nickname); ?>!</h2>
<p><a href="create-event.php">Создать новое мероприятие</a> | <a href="index.php">На главную</a> | <a href="../backend/logout.php">Выйти</a></p>

<h3>Мои мероприятия:</h3>

<?php if ($events): ?>
    <?php foreach ($events as $event): ?>
        <div class="event">
            <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
            <?php echo htmlspecialchars($event['description']); ?><br>
            Дата: <?php echo htmlspecialchars($event['event_date']); ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>У вас пока нет мероприятий.</p>
<?php endif; ?>

<h3>Мои интересы:</h3>

<form method="POST">
    <?php foreach ($all_interests as $interest): ?>
        <label>
            <input type="checkbox" name="interests[]" value="<?php echo $interest['id']; ?>"
                <?php if (in_array($interest['id'], $user_interests)) echo 'checked'; ?>>
            <?php echo htmlspecialchars($interest['name']); ?>
        </label><br>
    <?php endforeach; ?>
    <button type="submit">Сохранить интересы</button>
</form>

</body>
</html>

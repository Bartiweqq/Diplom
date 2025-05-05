<?php
session_start();
global $pdo;
require_once '../backend/db.php';

$userInterests = [];

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT i.name FROM user_interests ui
        JOIN interests i ON ui.interest_id = i.id
        WHERE ui.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userInterests = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Получаем категории и города для фильтра
$categories = $pdo->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$cities = $pdo->query("SELECT DISTINCT city FROM events WHERE city IS NOT NULL ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <script>
        var userInterests = <?php echo json_encode($userInterests); ?>;
    </script>
    <meta charset="UTF-8">
    <title>Local Events</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

<header>
    <h1>Local Events</h1>
    <nav>
        <?php if (isset($_SESSION['user'])): ?>
            <span>Привет, <?php echo htmlspecialchars($_SESSION['user']); ?>!</span>
            <a href="profile.php">Профиль</a>
            <a href="create-event.php">Создать событие</a>
            <a href="../backend/logout.php">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
            <a href="register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>

<div class="filter-form" style="padding: 20px;">
    <form id="filter-form">
        <label>Категория:
            <select name="category" id="filter-category">
                <option value="">Все</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Город:
            <select name="city" id="filter-city">
                <option value="">Все</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Показать</button>
    </form>
</div>

<div class="content">
    <div class="info">
        <p class="welcome">
            Найдите мероприятия по интересам или создайте своё!
        </p>
        <p>
            Используйте карту, чтобы найти события рядом с вами.
        </p>
        <div class="event-list" id="event-list">
            <!-- Здесь появятся мероприятия -->
        </div>
    </div>

    <div id="map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="scripts.js"></script>

</body>
</html>

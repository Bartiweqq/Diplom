<?php
global $pdo;
session_start();
require_once '../backend/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Получаем категории (интересы) из базы
$categories = $pdo->query("SELECT * FROM interests ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать мероприятие</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Select2 стили -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="create-event-page">


<div class="form-container">
    <h2>Создать мероприятие</h2>
    <form action="../backend/create-event-action.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Название" required>
        <textarea name="description" placeholder="Описание" required></textarea>

        <select name="category" id="category" required>
            <option value="">Выберите категорию</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="datetime-local" name="event_date" required>
        <input type="text" name="latitude" placeholder="Широта" required>
        <input type="text" name="longitude" placeholder="Долгота" required>
        <input type="text" name="city" placeholder="Город" required>
        <input type="number" name="max_participants" placeholder="Максимальное количество участников (необязательно)">
        <button type="submit">Создать</button>
    </form>
</div>

<!-- Подключаем jQuery и Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#category').select2({
            placeholder: "Выберите категорию",
            allowClear: true
        });
    });
</script>

</body>
</html>

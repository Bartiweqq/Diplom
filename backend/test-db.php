<?php
global $pdo;
require_once 'db.php';

try {
    // Просто тестовый запрос
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Успешное подключение к базе данных.<br>";
    echo "Список таблиц в базе:<br>";
    foreach ($tables as $table) {
        echo "- " . htmlspecialchars($table) . "<br>";
    }

} catch (PDOException $e) {
    echo "Ошибка выполнения запроса: " . $e->getMessage();
}
?>

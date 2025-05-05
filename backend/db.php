<?php
$host = 'localhost';
$dbname = 'local_events_db';  // ВАЖНО: это должно совпадать!
$user = 'postgres'; // например postgres
$password = '1234';    // ваш пароль к postgres

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>

<?php
global $pdo;
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $city = $_POST['city'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users 
            (first_name, last_name, nickname, email, password, city) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $nickname, $email, $password, $city]);

        $_SESSION['user'] = $nickname;
        header("Location: ../frontend/index.php");
    } catch (PDOException $e) {
        echo "Ошибка регистрации: " . $e->getMessage();
    }
} else {
    echo "Неверный метод запроса.";
}
?>

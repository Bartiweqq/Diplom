<?php
global $pdo;
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE nickname = ?");
        $stmt->execute([$nickname]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['nickname'];
            header("Location: ../frontend/index.php");
        } else {
            echo "Неверный никнейм или пароль.";
        }

    } catch (PDOException $e) {
        echo "Ошибка входа: " . $e->getMessage();
    }
} else {
    echo "Неверный метод запроса.";
}
?>

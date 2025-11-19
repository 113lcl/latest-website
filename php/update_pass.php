<?php
require 'db.php'; // Подключение к базе данных

// Проверяем, что получены токен и новый пароль из формы
if (!empty($_POST['token']) && !empty($_POST['password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];

    // Ищем пользователя с этим токеном и не истекшим сроком действия
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Хешируем новый пароль
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Обновляем пароль и сбрасываем токен восстановления
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);
        echo "Password updated! You can now <a href='login.php'>login</a>.";
    } else {
        // Токен не найден или истёк
        echo "Invalid or expired token.";
    }
} else {
    // Не переданы необходимые данные
    echo "Invalid request.";
}
?>
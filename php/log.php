<?php
session_start(); // Запуск сессии для хранения состояния пользователя
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php'; // Подключение к базе данных
require_once __DIR__ . '/../vendor/autoload.php'; // Автозагрузка зависимостей Composer
use RobThree\Auth\TwoFactorAuth; // Подключение библиотеки 2FA

$username = $_POST['username']; // Получаем имя пользователя из формы
$password = $_POST['password']; // Получаем пароль из формы

// Получаем пользователя по имени из базы
$stmt = $conn->prepare("SELECT id, username, password, ga_secret FROM users WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверяем, найден ли пользователь и совпадает ли пароль
if ($user && password_verify($password, $user['password'])) {
    // Если у пользователя включена 2FA
    if (!empty($user['ga_secret'])) {
        $_SESSION['tmp_user_id'] = $user['id']; // Временный ID для 2FA
        $_SESSION['username'] = $user['username'];
        // Если выбран "Запомнить меня"
        if (!empty($_POST['remember'])) {
            $token = bin2hex(random_bytes(32)); // Генерируем токен
            setcookie('rememberme', $token, time() + 60*60*24*30, "/"); // Сохраняем токен в cookie
            $stmt = $conn->prepare("UPDATE users SET remember_token = :token WHERE id = :id"); // Сохраняем токен в БД
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
        }
        header('Location: 2fa.php'); // Переход на страницу 2FA
        exit;
    } else {
        // Если 2FA не включена — обычная авторизация
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        if (!empty($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
            setcookie('rememberme', $token, time() + 60*60*24*30, "/");
            $stmt = $conn->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
        }
        header('Location: profile.php'); // Переход в профиль
        exit;
    }
} else {
    header('Location: login.php?error=1'); // Ошибка авторизации
    exit;
}
?>
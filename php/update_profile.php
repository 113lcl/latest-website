<?php
session_start(); // Запуск сессии для хранения состояния пользователя
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php'; // Подключение к базе данных

// Восстановление сессии по cookie "remember me", если пользователь не авторизован
if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE remember_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }
}

// Если пользователь не авторизован — редирект на страницу входа
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Обработка отправки формы обновления профиля
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);   // Новое имя пользователя
    $email = trim($_POST['email']); // Новый email пользователя
    $phone = trim($_POST['phone']); // Новый телефон пользователя

    require 'db.php'; // Подключение к базе данных через PDO (повторно, если нужно)

    try {
        // Подготовка запроса для обновления данных пользователя
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        // Выполнение запроса
        $stmt->execute();

        // Перенаправление на страницу аккаунта после успешного обновления
        header("Location: account.php");
        exit();
    } catch (PDOException $e) {
        // Обработка ошибок при обновлении профиля
        echo "Ошибка: " . $e->getMessage();
    }
}
?>
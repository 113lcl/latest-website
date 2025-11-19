<?php
session_start(); // Запуск сессии для работы с пользователем
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка авторизации пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Редирект на логин, если не авторизован
    exit();
}

// Если форма отправлена методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password']; // Старый пароль из формы
    $new_password = $_POST['new_password']; // Новый пароль из формы

    require __DIR__ . '/db.php'; // Подключение к базе данных через PDO

    try {
        // Получаем текущий хеш пароля пользователя из базы
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $db_password = $stmt->fetchColumn();

        // Проверяем правильность старого пароля
        if ($db_password && password_verify($old_password, $db_password)) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT); // Хешируем новый пароль
            // Обновляем пароль в базе данных
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->bindParam(':password', $new_hashed, PDO::PARAM_STR);
            $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            header("Location: account.php"); // Редирект на аккаунт после смены пароля
            exit();
        } else {
            echo "Старый пароль неверен."; // Сообщение об ошибке, если старый пароль не совпал
        }
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage(); // Вывод ошибки при работе с БД
    }
}
?>
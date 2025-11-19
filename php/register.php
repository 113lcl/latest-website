<?php
session_start(); // Запуск сессии для хранения состояния пользователя
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/db.php'; // Подключение к базе данных (создается $conn для PDO)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name']; // Имя пользователя из формы
    $username = $_POST['username']; // Логин из формы
    $phone = $_POST['phone']; // Телефон из формы
    $email = $_POST['email']; // Email из формы
    $password = $_POST['password']; // Пароль из формы
    $confirm_password = $_POST['confirm_password']; // Подтверждение пароля

    if ($password !== $confirm_password) {
        die("Passwords do not match."); // Проверка совпадения паролей
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Хеширование пароля

    try {
        // Вставка нового пользователя в базу данных
        $stmt = $conn->prepare("INSERT INTO users (name, username, phone, email, password) VALUES (:name, :username, :phone, :email, :password)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->lastInsertId(); // Сохраняем id нового пользователя в сессию
            $_SESSION['username'] = $username; // Сохраняем username в сессию
            header("Location: cards.php"); // Перенаправление на выбор карты
            exit();
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // Код ошибки для дублирующегося значения (username/email)
            echo "A user with this username or email already exists.";
        } else {
            echo "Error: " . $e->getMessage(); // Вывод других ошибок
        }
    }
}
?>
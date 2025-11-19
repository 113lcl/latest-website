<?php
session_start(); // Запуск сессии для хранения данных пользователя между запросами

// Включение отображения ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка: если временный ID пользователя не установлен, перенаправить на страницу входа
if (!isset($_SESSION['tmp_user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

require __DIR__ . '/db.php'; // Подключение к базе данных
require_once __DIR__ . '/../vendor/autoload.php'; // Автозагрузка зависимостей Composer

use RobThree\Auth\TwoFactorAuth; // Использование библиотеки для 2FA

$message = ''; // Сообщение для вывода ошибок или статуса

// Если форма отправлена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tfa = new TwoFactorAuth('MyWebsite'); // Создание объекта для работы с 2FA

    // Получение секретного ключа пользователя из базы данных
    $stmt = $conn->prepare("SELECT ga_secret FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['tmp_user_id']);
    $stmt->execute();
    $secret = $stmt->fetchColumn();

    // Проверка введённого кода с помощью библиотеки 2FA
    if ($tfa->verifyCode($secret, $_POST['code'])) {
        $_SESSION['user_id'] = $_SESSION['tmp_user_id']; // Авторизация пользователя
        unset($_SESSION['tmp_user_id']); // Удаление временного ID
        header('Location: profile.php'); // Перенаправление в профиль
        exit;
    } else {
        $message = "Invalid code!"; // Сообщение об ошибке, если код неверный
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Two-Factor Authentication</title>
  <link rel="stylesheet" href="../css/2fa.css"> <!-- Подключение стилей -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Иконки -->
</head>
<body>
  <div class="wrapper">
    <form method="post">
      <h1>2FA: Enter the code</h1>
      <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div> <!-- Вывод сообщения об ошибке -->
      <?php endif; ?>
      <div class="input-box">
        <input type="text" name="code" placeholder="Code from the app" required maxlength="6" pattern="\d{6}"> <!-- Поле для ввода кода -->
        <i class='bx bxs-key'></i> <!-- Иконка -->
      </div>
      <button type="submit" class="btn">Verify</button> <!-- Кнопка отправки формы -->
      <div class="register-link">
        <p><a href="login.php">Back to login</a></p> <!-- Ссылка на страницу входа -->
      </div>
    </form>
  </div>
</body>
</html>
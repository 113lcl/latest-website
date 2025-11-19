<?php
session_start(); // Запуск сессии для хранения состояния пользователя
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register Page</title>
  <link rel="stylesheet" href="../css/reg_style.css" /> <!-- Стили для формы регистрации -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Иконки -->
</head>

<body>
  <div class="wrapper">
    <!-- Форма регистрации пользователя -->
    <form id="registerForm" action="register.php" method="post">
      <h1>Register</h1>

      <div class="input-box">
        <input type="text" name="name" id="name" placeholder="Name" required /> <!-- Имя пользователя -->
      </div>

      <div class="input-box">
        <input type="text" name="username" id="username" placeholder="Username" required /> <!-- Логин -->
      </div>

      <div class="input-box">
        <input type="text" name="phone" id="phone" placeholder="Phone number" required /> <!-- Телефон -->
        <i class='bx bxs-phone'></i>
      </div>

      <div class="input-box">
        <input type="email" name="email" id="email" placeholder="Email" required /> <!-- Email -->
        <i class='bx bx-envelope'></i>
      </div>

      <div class="input-box">
        <input type="password" name="password" id="password" placeholder="Password" required /> <!-- Пароль -->
        <i class='bx bx-show' id="togglePassword"></i> <!-- Иконка показать/скрыть пароль -->
      </div>
      
      <div class="input-box">
        <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm password" required /> <!-- Подтверждение пароля -->
        <i class='bx bx-show' id="toggleConfirmPassword"></i>
      </div>

      <button type="submit" class="btn">Register</button> <!-- Кнопка регистрации -->

      <div class="register-link">
        <p>Already have an account? <a href="login.php">Login</a></p> <!-- Ссылка на вход -->
      </div>

      <div class="register-link">
        <p><a href="index.php">Return to menu</a></p> <!-- Ссылка на главную -->
      </div>
    </form>
  </div>
  
  <script src="../js/script.js"></script> <!-- JS для показа/скрытия пароля и др. -->
</body>
</html>

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
  <title>Login Page</title>
  <link rel="stylesheet" href="../css/login_style.css"> <!-- Стили для формы входа -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Иконки -->
</head>
<body>
  <div class="wrapper">
    <!-- Форма авторизации пользователя -->
    <form action="log.php" method="post">
      <h1>Login</h1>

      <div class="input-box">
        <input type="text" name="username" placeholder="Username" required />
        <i class='bx bxs-user'></i> <!-- Иконка пользователя -->
      </div>

      <div class="input-box">
        <input type="password" name="password" id="password" placeholder="Password" required />
        <i class='bx bxs-lock-alt'></i> <!-- Иконка замка -->
        <i class='bx bx-show' id="togglePassword"></i> <!-- Иконка показать/скрыть пароль -->
      </div>

      <div class="remember-forgot">
        <label><input type="checkbox" name="remember" />Remember me</label> <!-- Чекбокс "Запомнить меня" -->
        <a href="forgot_pass.php">Forgot password?</a> <!-- Ссылка на восстановление пароля -->
      </div>

      <button type="submit" class="btn">Login</button> <!-- Кнопка входа -->

      <div class="register-link">
        <p>Don't have an account? <a href="reg.php">Register</a></p> <!-- Ссылка на регистрацию -->
      </div>

      <div class="register-link">
        <p><a href="index.php">Return to menu</a></p> <!-- Ссылка на главную -->
      </div>
    </form>
  </div>
  <script src="../js/script.js"></script> <!-- JS для показа/скрытия пароля и др. -->
</body>
</html>

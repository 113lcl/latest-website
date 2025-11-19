<?php
// Эта страница выводит форму для восстановления пароля пользователя
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/login_style.css"> <!-- Стили для формы -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Иконки -->
</head>
<body>
    <div class="wrapper">
        <!-- Форма для ввода email для восстановления пароля -->
        <form action="send_reset.php" method="post">
            <h1>Forgot Password</h1>
            <div class="input-box">
                <input type="email" name="email" required placeholder="Enter your email"> <!-- Поле для email -->
                <i class='bx bxs-envelope'></i> <!-- Иконка email -->
            </div>
            <button type="submit" class="btn">Send reset link</button> <!-- Кнопка отправки -->
            <div class="register-link">
                <p><a href="login.php">Back to login</a></p> <!-- Ссылка на вход -->
            </div>
        </form>
    </div>
</body>
</html>
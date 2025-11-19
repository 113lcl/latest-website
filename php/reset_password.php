<?php
require __DIR__ . '/db.php'; // Подключение к базе данных

$token = $_GET['token'] ?? ''; // Получаем токен из URL
if (!$token) {
    die('Invalid link.'); // Если токена нет — ошибка
}

// Проверяем, существует ли пользователь с таким токеном и не истёк ли срок действия
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die('Invalid link.'); // Если пользователь не найден или токен просрочен — ошибка
}

// Показываем форму для ввода нового пароля
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Set a new password</h2>
    <form action="update_pass.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>"> <!-- Скрытое поле с токеном -->
        <input type="password" name="password" required placeholder="New password"> <!-- Новй пароль -->
        <button type="submit">Update password</button> <!-- Кнопка отправки -->
    </form>
</body>
</html>
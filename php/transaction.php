<?php
session_start(); // Запуск сессии для работы с пользователем
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/db.php'; // Подключение к базе данных

// Восстановление сессии по cookie "remember me", если пользователь не авторизован
if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE remember_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }
}

// Получаем текущий баланс пользователя
$user_id = $_SESSION['user_id'] ?? 0;
$balance = 0.0;
$message = '';

try {
    // Получаем баланс пользователя из базы
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $balance = $stmt->fetchColumn();
    $balance = floatval(preg_replace('/[^\d.]/', '', $balance)); // Приведение к числу
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}

// Обработка отправки формы перевода
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0); // Сумма перевода
    $username = trim($_POST['username'] ?? ''); // Имя получателя

    if ($amount <= 0) {
        $message = "Enter a valid amount."; // Проверка суммы
    } else {
        // Проверяем, существует ли получатель
        $stmt = $pdo->prepare("SELECT id, balance FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recipient) {
            $message = "User with this name not found."; // Нет такого пользователя
        } elseif ($recipient['id'] == $user_id) {
            $message = "You cannot send money to yourself."; // Нельзя отправить самому себе
        } elseif (isset($_POST['send'])) {
            // Отправить деньги
            if ($balance >= $amount) {
                try {
                    $pdo->beginTransaction(); // Начинаем транзакцию

                    // Списываем у отправителя
                    $newSenderBalance = $balance - $amount;
                    $stmt = $pdo->prepare("UPDATE users SET balance = :balance WHERE id = :id");
                    $stmt->bindParam(':balance', $newSenderBalance);
                    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();

                    // Зачисляем получателю
                    $newRecipientBalance = floatval($recipient['balance']) + $amount;
                    $stmt = $pdo->prepare("UPDATE users SET balance = :balance WHERE id = :id");
                    $stmt->bindParam(':balance', $newRecipientBalance);
                    $stmt->bindParam(':id', $recipient['id'], PDO::PARAM_INT);
                    $stmt->execute();

                    // Транзакция для отправителя (расход)
                    $descSender = "Transfer to user " . htmlspecialchars($username);
                    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, description, amount) VALUES (:user_id, :description, :amount)");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':description', $descSender, PDO::PARAM_STR);
                    $negAmount = -$amount;
                    $stmt->bindParam(':amount', $negAmount, PDO::PARAM_STR);
                    $stmt->execute();

                    // Транзакция для получателя (пополнение)
                    $descRecipient = "Received from user " . htmlspecialchars($_SESSION['username'] ?? $user_id);
                    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, description, amount) VALUES (:user_id, :description, :amount)");
                    $stmt->bindParam(':user_id', $recipient['id'], PDO::PARAM_INT);
                    $stmt->bindParam(':description', $descRecipient, PDO::PARAM_STR);
                    $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
                    $stmt->execute();

                    $pdo->commit(); // Фиксируем транзакцию
                    $balance = $newSenderBalance;
                    $message = "Amount sent successfully!";
                } catch (PDOException $e) {
                    $pdo->rollBack(); // Откат в случае ошибки
                    $message = "Error: " . $e->getMessage();
                }
            } else {
                $message = "Insufficient funds!"; // Недостаточно средств
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Money Transfer</title>
  <link rel="stylesheet" href="../css/transaction.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="wrapper">
    <form method="post">
      <h1>Money Transfer</h1>
      <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div> <!-- Сообщение об ошибке или успехе -->
      <?php endif; ?>
      <div class="input-box">
        <input type="text" name="username" placeholder="Recipient's username" required />
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
        <input type="number" name="amount" placeholder="Amount" min="1" step="0.01" required />
        <i class='bx bx-money'></i>
      </div>
      <div class="button-row">
        <button type="submit" name="send" class="btn">Send</button>
      </div>
      <div class="register-link">
        <p><a href="profile.php">Back to profile</a></p>
      </div>
    </form>
  </div>
</body>
</html>
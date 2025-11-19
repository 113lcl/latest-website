<?php
session_start(); // Запуск сессии для хранения состояния пользователя
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

$user_id = $_SESSION['user_id'] ?? 0; // Получаем ID пользователя из сессии

// Значения по умолчанию для пользователя
$user = [
    'name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'balance' => 0,
    'card_img' => 'css/img/11.png' // Картинка карты по умолчанию (без ../)
];

try {
    // Получаем данные пользователя из базы
    $stmt = $pdo->prepare("SELECT name, username, email, phone, balance, card_img FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        $user = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'balance' => $data['balance'],
            'card_img' => $data['card_img'] ? $data['card_img'] : 'css/img/11.png'
        ];
    }
} catch (PDOException $e) {
    // Можно добавить обработку ошибок или логирование
}

$transactions = [];
try {
    // Получаем последние 4 транзакции пользователя
    $stmt = $pdo->prepare("SELECT date, description, amount FROM transactions WHERE user_id = :user_id ORDER BY date DESC LIMIT 4");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Можно добавить обработку ошибок или логирование
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <h1>Profile</h1>
    <div class="profile-grid">
        <!-- Окно 1: Информация о пользователе -->
        <div class="profile-box user-info">
            <h2>User Info</h2>
            <div class="info-row"><i class='bx bxs-user'></i> <span><?php echo htmlspecialchars($user['username']); ?></span></div>
            <div class="info-row"><i class='bx bxs-envelope'></i> <span><?php echo htmlspecialchars($user['email']); ?></span></div>
            <div class="info-row"><i class='bx bxs-phone'></i> <span><?php echo htmlspecialchars($user['phone']); ?></span></div>
            <a href="account.php" class="btn">Profile Settings</a>
        </div>
        <!-- Окно 2: Состояние счета -->
        <div class="profile-box balance-info">
            <h2>Balance</h2>
            <div class="balance"><?php echo number_format($user['balance'], 2, '.', ' ') . ' zł'; ?></div>
            <a href="transaction.php" class="btn">Send Money</a>
        </div>
        <!-- Окно 3: Банковская карта -->
        <div class="profile-box card-info">
            <h2>Your Card</h2>
            <img src="../css<?php echo htmlspecialchars($user['card_img']); ?>" alt="Bank Card" class="bank-card">
        </div>
        <!-- Окно 4: История транзакций -->
        <div class="profile-box transactions-info">
            <h2>Transaction History</h2>
            <ul class="transactions-list">
                <?php if (count($transactions) === 0): ?>
                    <li><span>No transactions</span></li>
                <?php else: ?>
                    <?php foreach ($transactions as $tr): ?>
                        <li>
                            <span class="tr-date"><?= htmlspecialchars(date('Y-m-d', strtotime($tr['date']))) ?></span>
                            <span class="tr-desc"><?= htmlspecialchars($tr['description']) ?></span>
                            <span class="tr-sum <?= $tr['amount'] >= 0 ? 'plus' : 'minus' ?>">
                                <?= ($tr['amount'] >= 0 ? '+' : '') . number_format($tr['amount'], 2, '.', ' ') . ' zł' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="register-link">
        <p><a href="index.php">Back to menu</a></p>
    </div>
</div>
</body>
</html>
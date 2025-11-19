<?php
session_start(); // Запуск сессии для хранения данных пользователя

// Включение отображения ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php'; // Подключение к базе данных

// Проверка авторизации через сессию или cookie "remember me"
if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE remember_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = $user['id']; // Восстановление сессии пользователя
        $_SESSION['username'] = $user['username'];
    }
}

// Если пользователь не авторизован — редирект на логин
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Обработка выхода пользователя (logout)
if (isset($_POST['logout'])) {
    if (isset($_SESSION['user_id'])) {
        // Очистка токена "remember me" в базе
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
    }
    setcookie('rememberme', '', time() - 3600, "/"); // Удаление cookie
    session_unset(); // Очистка сессии
    session_destroy(); // Завершение сессии
    header("Location: ../php/index.php"); // Редирект на главную
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php'; // Автозагрузка зависимостей Composer
use RobThree\Auth\TwoFactorAuth; // Подключение библиотеки 2FA

// Значения по умолчанию для профиля
$userData = [
    'name' => 'User',
    'username' => 'username',
    'email' => 'user@example.com',
    'phone' => '',
    'created_at' => '2025'
];

try {
    // Получение данных пользователя из базы
    $stmt = $pdo->prepare("SELECT name, username, email, phone, created_at, ga_secret FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC) ?: $userData;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$tfa = new TwoFactorAuth('MyWebsite'); // Создание объекта для работы с 2FA

// Включение 2FA по кнопке (генерация секрета и сохранение в БД)
if (isset($_POST['enable_2fa'])) {
    $secret = $tfa->createSecret();
    $stmt = $pdo->prepare("UPDATE users SET ga_secret = :secret WHERE id = :id");
    $stmt->bindParam(':secret', $secret);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $userData['ga_secret'] = $secret;
}

// Если секрет 2FA уже есть — показываем QR-код для приложения
if (!empty($userData['ga_secret'])) {
    $qrCodeUrl = $tfa->getQRCodeImageAsDataUri($userData['username'], $userData['ga_secret']);
    $qrCodeHtml = '<img src="' . $qrCodeUrl . '" alt="QR for Google Authenticator">';
    $qrCodeHtml .= '<p>Scan this QR code in Google Authenticator</p>';
} else {
    // Если секрета нет — кнопка для подключения 2FA
    $qrCodeHtml = '
    <form method="post" style="text-align:center; margin-top:20px;">
      <button name="enable_2fa" class="btn btn-2fa">
        <i class="bx bxs-shield"></i> Connect 2FA
      </button>
    </form>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Account</title>
  <link rel="stylesheet" href="../css/account.css" > <!-- Стили для страницы аккаунта -->
  <script src="../js/script.js" defer></script> <!-- JS для работы с формой -->
</head>
<body>
  <div class="wrapper">
    <!-- Форма профиля пользователя -->
    <form id="profileForm" action="update_profile.php" method="post" autocomplete="off">
      <div class="profile-header">
        <h1>
          Welcome,
          <span style="color:#fff;"><?= htmlspecialchars($userData['username']) ?></span>
          !
        </h1>
      </div>
      <!-- Имя -->
      <div class="input-box">
        <span class="input-label">Name</span>
        <input class="edit-field" type="text" name="name" value="<?= htmlspecialchars($userData['name']) ?>" readonly required>
      </div>
      <!-- Логин (только для чтения) -->
      <div class="input-box readonly-box" style="margin-top:60px;">
        <span class="input-label">Username</span>
        <input class="edit-field" type="text" name="username" value="<?= htmlspecialchars($userData['username']) ?>" readonly disabled required>
      </div>
      <!-- Email -->
      <div class="input-box" style="margin-top:100px;">
        <span class="input-label">Email</span>
        <input class="edit-field" type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" readonly required>
        <?php if (empty($userData['email_verified'])): ?>
          <div class="email-verify-row">
            <button type="button" class="btn" id="sendCodeBtn" onclick="sendVerificationCode()">Confirm</button>
            <form id="verifyForm" style="display:none;" onsubmit="return verifyEmailCode(event)">
              <input type="text" name="code" placeholder="Code" required style="width:110px; margin-left:10px;">
              <button type="submit" class="btn" style="width:auto; padding: 0 16px; margin-left:6px;">Check</button>
            </form>
          </div>
          <div id="verifyMsg" style="margin-top:7px; min-height:18px;"></div>
        <?php else: ?>
          <span style="color:green; margin-left:10px;">&#10003; Подтверждена</span>
        <?php endif; ?>
      </div>
      <!-- Телефон -->
      <div class="input-box" style="margin-top:70px;">
        <span class="input-label">Phone</span>
        <input class="edit-field" type="text" name="phone" value="<?= htmlspecialchars($userData['phone']) ?>" readonly required>
      </div>
      <!-- Кнопки действий -->
      <div class="action-buttons">
        <button type="button" class="btn" id="editBtn" onclick="enableEdit()">Edit Profile</button>
        <button type="submit" class="btn" id="saveBtn" style="display:none;">Save</button>
        <button type="button" class="btn" id="cancelBtn" style="display:none;" onclick="cancelEdit()">Cancel</button>
        <button type="button" class="btn" onclick="togglePasswordForm()">Change Password</button>
      </div>
    </form>
    <!-- Кнопка выхода -->
    <form method="post" style="margin-top:10px;">
      <button type="submit" name="logout" class="btn logout">Logout</button>
    </form>
    <!-- Форма смены пароля -->
    <form class="password-form" id="passwordForm" action="change_password.php" method="post">
      <h3>Change Password</h3>
      <input type="password" name="old_password" placeholder="Current Password" required>
      <input type="password" name="new_password" placeholder="New Password" required>
      <button type="submit" class="btn">Update Password</button>
    </form>
    <div class="register-link">
      <p><a href="profile.php">Return</a></p>
    </div>
    <!-- Блок двухфакторной аутентификации -->
    <div class="two-factor-auth">
      <h3>Two-Factor Authentication</h3>
      <?= $qrCodeHtml ?>
    </div>
  </div>
  <script>
    // JS: Включение режима редактирования профиля
    let originalValues = {};
    function enableEdit() {
      document.querySelectorAll('.edit-field').forEach(function(input) {
        if (!input.disabled) {
          originalValues[input.name] = input.value;
          input.removeAttribute('readonly');
        }
      });
      // Скрыть поля только для чтения
      document.querySelectorAll('.readonly-box').forEach(function(box) {
        box.style.display = 'none';
      });
      document.getElementById('editBtn').style.display = 'none';
      document.getElementById('saveBtn').style.display = '';
      document.getElementById('cancelBtn').style.display = '';
    }
    // JS: Отмена редактирования профиля
    function cancelEdit() {
      document.querySelectorAll('.edit-field').forEach(function(input) {
        if (originalValues[input.name] !== undefined) {
          input.value = originalValues[input.name];
          input.setAttribute('readonly', true);
        }
      });
      // Показать поля только для чтения обратно
      document.querySelectorAll('.readonly-box').forEach(function(box) {
        box.style.display = '';
      });
      document.getElementById('editBtn').style.display = '';
      document.getElementById('saveBtn').style.display = 'none';
      document.getElementById('cancelBtn').style.display = 'none';
    }
    // JS: Показать/скрыть форму смены пароля
    function togglePasswordForm() {
      const pf = document.getElementById('passwordForm');
      pf.style.display = pf.style.display === 'block' ? 'none' : 'block';
    }
    function sendVerificationCode() {
      fetch('send_verification.php', {method: 'POST'})
        .then(r => r.json())
        .then(data => {
          document.getElementById('verifyMsg').innerText = data.message;
          document.getElementById('verifyForm').style.display = 'block';
        });
    }
  </script>
  <style>
  .email-verify-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
  }
  #verifyForm input[type="text"] {
    min-width: 90px;
    padding: 6px 10px;
    border-radius: 20px;
    border: 1px solid #DC7000;
    background: #222;
    color: #fff;
  }
  </style>
</body>
</html>
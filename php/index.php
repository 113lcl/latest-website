<?php
session_start(); // Запуск сессии для хранения стану користувача
ini_set('display_errors', 1); // Включення відображення помилок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Підтримувані мови
$supported_langs = ['ua', 'en', 'pl']; // Список підтримуваних мов

// Встановлення мови з параметру або сесії
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_langs)) {
    $_SESSION['lang'] = $_GET['lang']; // Встановлення мови з GET-параметра
}

$lang = $_SESSION['lang'] ?? 'en'; // Мова за замовчуванням — англійська
$lang_file = __DIR__ . "/../lang/$lang.php"; // Шлях до мовного файлу
$tr = file_exists($lang_file) ? include($lang_file) : include(__DIR__ . "/../lang/en.php"); // Завантаження перекладів
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZXC Bank</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Основні стилі -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Іконки -->
</head>
<body>
    <div class="wrapper">
        <header class="container">
            <span class="logo">ZXC Bank</span>
            <div class="language-switcher">
                <!-- Переключатель мов -->
                <a href="?lang=ua" class="lang-option <?= $lang === 'ua' ? 'active' : '' ?>">UA</a>
                <a href="?lang=en" class="lang-option <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
                <a href="?lang=pl" class="lang-option <?= $lang === 'pl' ? 'active' : '' ?>">PL</a>
            </div>
            <nav>
                <ul>
                    <!-- Головне меню з випадаючими списками -->
                    <li>
                        <a href="#"><?= $tr['individual_clients'] ?></a>
                        <ul class="dropdown">
                            <li><a href="#"><?= $tr['personal_accounts'] ?></a></li>
                            <li><a href="#"><?= $tr['debit_credit_cards'] ?></a></li>
                            <li><a href="#"><?= $tr['savings_deposits'] ?></a></li>
                            <li><a href="#"><?= $tr['digital_banking'] ?></a></li>
                            <li><a href="#"><?= $tr['personal_loans'] ?></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><?= $tr['small_business'] ?></a>
                        <ul class="dropdown">
                            <li><a href="#"><?= $tr['business_accounts'] ?></a></li>
                            <li><a href="#"><?= $tr['merchant_services'] ?></a></li>
                            <li><a href="#"><?= $tr['business_loans'] ?></a></li>
                            <li><a href="#"><?= $tr['payroll_hr'] ?></a></li>
                            <li><a href="#"><?= $tr['pos_solutions'] ?></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><?= $tr['corporate_investment'] ?></a>
                        <ul class="dropdown">
                            <li><a href="#"><?= $tr['corporate_banking'] ?></a></li>
                            <li><a href="#"><?= $tr['investment_solutions'] ?></a></li>
                            <li><a href="#"><?= $tr['treasury_management'] ?></a></li>
                            <li><a href="#"><?= $tr['trade_finance'] ?></a></li>
                            <li><a href="#"><?= $tr['asset_management'] ?></a></li>
                        </ul>
                    </li>
                    <li class="btn">
                        <!-- Кнопки входу/реєстрації або переходу в акаунт -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a id="accountButton" href="profile.php"><?= $tr['account'] ?></a>
                        <?php else: ?>
                            <a id="loginButton" href="login.php" class="btn-login"><?= $tr['login'] ?></a>
                            <a id="signupButton" href="reg.php" class="btn-signup"><?= $tr['sign_up'] ?></a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </header>

        <div style="margin-top: 50px;" class="hero container">
            <div class="hero--info">
                <!-- Блок привітання та опису -->
                <h2><?= $tr['welcome'] ?></h2>
                <h1>
                    <?= $tr['smart_banking'] ?>
                </h1>
                <p>
                    <?= $tr['description'] ?>
                </p>
                <button style="margin-bottom: 400px;" class="btn" onclick="window.location.href='reg.php'">Open an Account</button>
            </div>
            <img src="../css/img/19.png" alt="ZXC Bank" class="parallax-img">
        </div>

        <div class="container trending">
            <h3><?= $tr['our_products'] ?></h3>
            <div class="cards">
                <!-- Картинки продуктів -->
                <img src="../css/img/11.png" alt="Mobile Banking">
                <img src="../css/img/12.png" alt="Business Solutions">
                <img src="../css/img/13.png" alt="Premium Cards">
                <img src="../css/img/14.png" alt="Investment Tools">
            </div>
        </div>

        <div class="container big-text">
            <p style="margin-bottom: 100px;">
                <?= $tr['footer_note'] ?>
            </p>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <a href="support.php" class="floating-button">💬</a>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 ZXC Bank. All rights reserved.</p>
                <p>
                    License №123456 |
                    <a style="color: white;" href="privacy.php"><?= $tr['privacy_policy'] ?></a> |
                    <a style="color: white;" href="terms.php"><?= $tr['terms_of_use'] ?></a>
                </p>
            </div>
            <div class="footer-links">
                <a href="#"><?= $tr['about_us'] ?></a>
                <a style="margin-left: 30px;" href="#"><?= $tr['contact'] ?></a>
                <a style="margin-left: 30px;" href="#"><?= $tr['support'] ?></a>
            </div>
            <div class="social-links">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
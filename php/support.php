<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Support</title>
  <link rel="stylesheet" href="../css/support.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="wrapper">
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 350px;">
      <h1>Feedback</h1>
      <button class="tg-modern-btn" type="button" onclick="window.open('https://t.me/Banking_SupportBot', '_blank')">
        <span class="tg-icon">
          <svg width="28" height="28" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="120" cy="120" r="120" fill="#229ED9"/>
            <path d="M180 72L156 180C154.5 186.5 150.5 188 145 185.5L116 164.5L102 177.5C100 179.5 98.5 181 95.5 181L98 151L162 90.5C164 88.5 161.5 87.5 159 89L83 132.5L54 123.5C47.5 121.5 47.5 117.5 55.5 114.5L172 70.5C177.5 68.5 182 71.5 180 72Z" fill="white"/>
          </svg>
        </span>
        <span class="tg-text">Contact via Telegram</span>
      </button>
      <div class="register-link">
        <p><a href="index.php">Back to menu</a></p>
      </div>
    </div>
  </div>
</body>
</html>
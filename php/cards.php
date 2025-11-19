<?php
session_start(); // Запуск сессии для работы с пользователем
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// var_dump($_SESSION); // Для отладки, можно раскомментировать для просмотра сессии
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name</title>
    <link rel="stylesheet" href="../css/cards.css"> <!-- Стили для карточек -->
    <script src="../js/script.js" defer></script> <!-- JS для страницы -->
</head>

<body>
    <div class="wrapper">
        <a href="../index.php" class="circle-back-button">⟵</a> <!-- Кнопка "назад" -->
        <div class="cards">
    <!-- Карточка "Regular" -->
    <a href="#" class="block sticky card-link" data-card="../img/11.png" style="margin-bottom: 300px;">
        <img src="../css/img/11.png" alt="">
        <h3>Regular</h3>
        <div class="list">
            <p class="dot-before">Credit limit up to 200 thousand and up to 55 days without interest</p>
            <p class="dot-before">Personal cashbacks in the loyalty program</p>
            <p class="dot-before">Payment by installments" and "Instant installments": buy now - pay later</p>
        </div>
    </a>
    <!-- Карточка "Junior" -->
    <a href="#" class="block sticky card-link" data-card="../img/12.png">
        <img src="../css/img/12.png" alt="">
        <h3>Junior</h3>
        <div class="list">
            <p class="dot-before">Credit limit up to 200 thousand and up to 55 days without interest</p>
            <p class="dot-before">Personal cashbacks in the loyalty program</p>
            <p class="dot-before">Payment by installments" and "Instant installments": buy now - pay later</p>
        </div>
    </a>
    <!-- Карточка "Silver" -->
    <a href="#" class="block sticky card-link" data-card="../img/13.png">
        <img src="../css/img/13.png" alt="">
        <h3>Silver</h3>
        <div class="list">
            <p class="dot-before">Credit limit up to 200 thousand and up to 55 days without interest</p>
            <p class="dot-before">Personal cashbacks in the loyalty program</p>
            <p class="dot-before">Payment by installments" and "Instant installments": buy now - pay later</p>
        </div>
    </a>
    <!-- Карточка "Business" -->
    <a href="#" class="block sticky card-link" data-card="../img/14.png">
        <img src="../css/img/14.png" alt="">
        <h3>Business</h3>
        <div class="list">
            <p class="dot-before">Credit limit up to 200 thousand and up to 55 days without interest</p>
            <p class="dot-before">Personal cashbacks in the loyalty program</p>
            <p class="dot-before">Payment by installments" and "Instant installments": buy now - pay later</p>
        </div>
    </a>
</div>
    </div>
    <!-- Модальное окно подтверждения выбора карты -->
<div id="cardModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-icon">
      <i class='bx bx-help-circle'></i>
    </div>
    <h2>Confirm card selection</h2>
    <p>Are you sure you want to select this card?</p>
    <div class="modal-actions">
      <button id="confirmCardBtn" class="btn">Yes, select</button>
      <button id="cancelCardBtn" class="btn btn-cancel">Cancel</button>
    </div>
  </div>
</div>
<script>
// JS: обработка выбора карты и подтверждения через модальное окно
document.addEventListener('DOMContentLoaded', function() {
  let selectedCard = '';
  document.querySelectorAll('.card-link').forEach(link => {
    link.addEventListener('click', function(e) {
      // Только если есть data-card (чтобы не срабатывало на других ссылках)
      if (!this.getAttribute('data-card')) return;
      e.preventDefault();
      selectedCard = this.getAttribute('data-card');
      document.getElementById('cardModal').style.display = 'flex';
    });
  });
  // Кнопка отмены в модальном окне
  document.getElementById('cancelCardBtn').onclick = function() {
    document.getElementById('cardModal').style.display = 'none';
  };
  // Кнопка подтверждения выбора карты
  document.getElementById('confirmCardBtn').onclick = function() {
    // Отправка выбранной карты на сервер
    fetch('save_card.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'card_img=' + encodeURIComponent(selectedCard)
    }).then(res => res.json()).then(data => {
      if(data.success) {
        window.location.href = 'profile.php'; // Переход в профиль при успехе
      } else {
        alert('Card saving error'); // Ошибка сохранения
      }
    });
  };
});
</script>
</body>
</html>

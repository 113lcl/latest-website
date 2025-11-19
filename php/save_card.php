<?php
session_start(); // Запуск сессии для работы с пользователем
ini_set('display_errors', 1); // Включение отображения ошибок
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/db.php'; // Подключение к базе данных

// Проверка авторизации пользователя
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']); // Если не авторизован — ошибка
    exit;
}

$user_id = $_SESSION['user_id']; // Получаем ID пользователя из сессии
$card_img = $_POST['card_img'] ?? ''; // Получаем путь к выбранной карте из POST

if ($card_img) {
    try {
        // Подготовка запроса для обновления изображения карты пользователя
        $stmt = $conn->prepare("UPDATE users SET card_img = :card_img WHERE id = :id");
        $stmt->bindParam(':card_img', $card_img, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        // Выполнение запроса
        if ($stmt->execute()) {
            echo json_encode(['success' => true]); // Успешно — возвращаем success
        } else {
            echo json_encode(['success' => false, 'error' => 'update_failed']); // Ошибка обновления
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]); // Ошибка запроса
    }
} else {
    echo json_encode(['success' => false, 'error' => 'no_card']); // Если карта не выбрана
}
?>
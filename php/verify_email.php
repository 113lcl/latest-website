<?php
session_start();
require __DIR__ . '/db.php';

header('Content-Type: application/json');

$code = $_POST['code'] ?? '';
$stmt = $pdo->prepare("SELECT email_verification_code FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['email_verification_code'] == $code) {
    $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, email_verification_code = NULL WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Почта успешно подтверждена!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный код!']);
}
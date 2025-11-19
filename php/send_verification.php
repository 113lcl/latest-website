<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/../vendor/autoload.php'; // Подключаем автозагрузку Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$stmt = $pdo->prepare("SELECT email FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $code = rand(100000, 999999);
    $stmt = $pdo->prepare("UPDATE users SET email_verification_code = :code WHERE id = :id");
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zxcbank322@gmail.com';
        $mail->Password = 'paagowodmpzomqyx'; // Новый пароль приложения без пробелов!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Отключаем дебаг для пользователя, включайте только для диагностики
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'error_log';

        $mail->setFrom('zxcbank322@gmail.com', 'Site');
        $mail->addAddress($user['email']);

        $mail->isHTML(false);
        $mail->Subject = 'Email Verification';
        $mail->Body    = "Your code: $code";

        $mail->send();
        echo json_encode(['message' => 'Код отправлен на почту!']);
    } catch (Exception $e) {
        echo json_encode(['message' => 'Ошибка отправки: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['message' => 'Ошибка!']);
}
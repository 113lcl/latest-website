<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    $to = "example-mail@gmail.com";
    $subject = "New message from the website";
    $body = "Name: $name\nEmail: $email\nMessage:\n$message";

    // Отправка письма
    mail($to, $subject, $body);

    // Сообщение пользователю
    echo "Thank you! Your message has been sent.";
}
?>

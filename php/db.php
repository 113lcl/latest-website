<?php
$host = 'localhost';      // Адрес сервера базы данных
$db   = 'users_db';       // Имя базы данных
$user = 'root';           // Имя пользователя БД
$pass = '';               // Пароль пользователя БД
$charset = 'utf8mb4';     // Кодировка соединения

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; // DSN-строка для PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Режим ошибок: выбрасывать исключения
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Режим выборки: ассоциативный массив
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Отключить эмуляцию подготовленных запросов
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); // Создание подключения к базе данных через PDO
    // Для совместимости с кодом, где используется $conn вместо $pdo:
    $conn = $pdo; // $conn — алиас для $pdo
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()); // Вывод ошибки при неудачном подключении
}
?>
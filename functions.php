<?php
session_start();

// Функция для получения всех пользователей
function getUsers() {
    $file = 'users.json';
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// Функция для сохранения пользователей
function saveUsers($users) {
    $file = 'users.json';
    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
}

// Функция для поиска пользователя по email
function findUserByEmail($email) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}

// Функция для поиска пользователя по имени
function findUserByUsername($username) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

// Функция для отправки email с кодом
function sendVerificationEmail($email, $code) {
    $to = $email;
    $subject = "Код подтверждения регистрации";
    $message = "
    <html>
    <head>
        <title>Подтверждение регистрации</title>
    </head>
    <body>
        <h2>Здравствуйте!</h2>
        <p>Ваш код подтверждения: <strong>$code</strong></p>
        <p>Введите этот код на сайте для завершения регистрации.</p>
        <p>Код действителен в течение 15 минут.</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: noreply@yoursite.com\r\n";
    
    return mail($to, $subject, $message, $headers);
}
?>

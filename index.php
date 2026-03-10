<?php
require_once 'functions.php';

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="welcome-box">
            <h1>Добро пожаловать!</h1>
            <p>Система регистрации и входа с подтверждением по email</p>
            <p>Для продолжения войдите или зарегистрируйтесь</p>
            <div class="buttons">
                <a href="login.php" class="btn">Вход</a>
                <a href="register.php" class="btn btn-secondary">Регистрация</a>
            </div>
        </div>
    </div>
</body>
</html>

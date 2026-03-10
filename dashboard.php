<?php
require_once 'functions.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получаем данные пользователя
$users = getUsers();
$current_user = null;

foreach($users as $user) {
    if($user['id'] == $_SESSION['user_id']) {
        $current_user = $user;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="welcome-box">
            <h1>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            
            <div class="alert alert-success">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($current_user['email']); ?></p>
                <p><strong>Дата регистрации:</strong> <?php echo $current_user['registered']; ?></p>
                <p><strong>Статус:</strong> ✓ Email подтвержден</p>
            </div>
            
            <p>Вы успешно вошли в систему!</p>
            
            <div class="buttons">
                <a href="logout.php" class="btn btn-danger">Выйти</a>
            </div>
        </div>
    </div>
</body>
</html>

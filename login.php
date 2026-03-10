<?php
require_once 'functions.php';

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $users = getUsers();
        $found = false;
        
        foreach($users as $user) {
            if(($user['username'] === $username || $user['email'] === $username) && 
               password_verify($password, $user['password'])) {
                
                if($user['verified']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $found = true;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = 'Email не подтвержден';
                    $found = true;
                    break;
                }
            }
        }
        
        if(!$found) {
            $error = 'Неверное имя пользователя или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Вход в систему</h2>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Имя пользователя или Email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Войти</button>
                
                <div class="links">
                    <a href="register.php">Нет аккаунта? Зарегистрироваться</a><br>
                    <a href="index.php">На главную</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

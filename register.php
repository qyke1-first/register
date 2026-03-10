<?php
require_once 'functions.php';

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    if(empty($username) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif(strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email адрес';
    } else {
        // Проверка существования пользователя
        $existingUser = findUserByEmail($email);
        $existingUsername = findUserByUsername($username);
        
        if($existingUser) {
            $error = 'Пользователь с таким email уже существует';
        } elseif($existingUsername) {
            $error = 'Пользователь с таким именем уже существует';
        } else {
            // Генерация кода подтверждения
            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            
            // Хеширование пароля
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Сохраняем данные в сессию для подтверждения
            $_SESSION['temp_user'] = [
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'code' => $verification_code,
                'time' => time()
            ];
            
            // Отправка email с кодом
            if(sendVerificationEmail($email, $verification_code)) {
                $_SESSION['verification_email'] = $email;
                header("Location: verify.php");
                exit();
            } else {
                $error = 'Ошибка при отправке кода подтверждения';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Регистрация</h2>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Подтвердите пароль</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit">Зарегистрироваться</button>
                
                <div class="links">
                    <a href="login.php">Уже есть аккаунт? Войти</a><br>
                    <a href="index.php">На главную</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

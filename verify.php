<?php
require_once 'functions.php';

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Проверяем, есть ли временные данные пользователя
if(!isset($_SESSION['temp_user'])) {
    header("Location: register.php");
    exit();
}

$error = '';
$success = '';
$temp_user = $_SESSION['temp_user'];

// Проверка времени (код действителен 15 минут)
if(time() - $temp_user['time'] > 900) {
    unset($_SESSION['temp_user']);
    $error = 'Время действия кода истекло. Зарегистрируйтесь снова.';
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['resend'])) {
        // Отправка нового кода
        $new_code = sprintf("%06d", mt_rand(1, 999999));
        $_SESSION['temp_user']['code'] = $new_code;
        $_SESSION['temp_user']['time'] = time();
        
        if(sendVerificationEmail($temp_user['email'], $new_code)) {
            $success = 'Новый код отправлен на ваш email';
        } else {
            $error = 'Ошибка при отправке кода';
        }
    } else {
        $entered_code = trim($_POST['verification_code']);
        
        if($entered_code == $temp_user['code']) {
            // Сохраняем пользователя в файл
            $users = getUsers();
            
            $new_user = [
                'id' => count($users) + 1,
                'username' => $temp_user['username'],
                'email' => $temp_user['email'],
                'password' => $temp_user['password'],
                'verified' => true,
                'registered' => date('Y-m-d H:i:s')
            ];
            
            $users[] = $new_user;
            saveUsers($users);
            
            // Автоматический вход
            $_SESSION['user_id'] = $new_user['id'];
            $_SESSION['username'] = $new_user['username'];
            
            // Очищаем временные данные
            unset($_SESSION['temp_user']);
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Неверный код подтверждения';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение email</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Таймер обратного отсчета
        let timeLeft = 900 - (Math.floor(Date.now() / 1000) - <?php echo $temp_user['time']; ?>);
        
        function updateTimer() {
            if(timeLeft <= 0) {
                document.getElementById('timer').innerHTML = 'Время истекло';
                document.getElementById('resend-btn').style.display = 'block';
                return;
            }
            
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            document.getElementById('timer').innerHTML = 
                minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }
        
        window.onload = function() {
            if(timeLeft > 0) {
                updateTimer();
            }
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Подтверждение email</h2>
            
            <div class="alert alert-info">
                Код подтверждения отправлен на email: <strong><?php echo $temp_user['email']; ?></strong>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="timer" id="timer"></div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="verification_code">Введите код подтверждения</label>
                    <input type="text" id="verification_code" name="verification_code" 
                           class="code-input" maxlength="6" required 
                           placeholder="XXXXXX">
                </div>
                
                <button type="submit">Подтвердить</button>
            </form>
            
            <form method="POST" action="" style="margin-top: 10px;">
                <button type="submit" name="resend" id="resend-btn" 
                        style="background: #48bb78; display: <?php echo (time() - $temp_user['time'] > 900) ? 'block' : 'none'; ?>;">
                    Отправить код повторно
                </button>
            </form>
            
            <div class="links">
                <a href="register.php">Вернуться к регистрации</a>
            </div>
        </div>
    </div>
</body>
</html>

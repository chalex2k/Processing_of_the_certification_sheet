<!DOCTYPE html>
<html>
<head>
    <title> <?php echo "$title $userstr"  ?> </title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body class="auth">
<div class="auth">
    <form method="post" action="authentication.php" onsubmit="return validate(this)">
        <div class="str-input-auth">
            <label for="email">Введите свой логин</label>
            <input type="text" name="email" value="<?php echo $email_temp?>">
        </div>
        <div class="str-input-auth">
            <label for="password">Введите пароль</label>
            <input type="password" name="password" value="<?php echo $password_temp ?>">
        </div>
        <div class="errors">
            <?php echo $error ?>
        </div>
        <div class="btn-auth" id="at-auth">
            <button type="submit">Войти</button>
            <a href="registration.php" class="to-reg">Зарегистрироваться</a>
        </div>
    </form>
</div>
</body>
</html>

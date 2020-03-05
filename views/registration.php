<!DOCTYPE html>
<html>
<head>
    <title> <?php echo " $title $userstr " ?> </title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<script>
    function showSelectorsForStudent(value) {
        if (value == 'student') {
            document.getElementById('semester').style.display = 'block';
            document.getElementById('group').style.display = 'block';
        }
        else {
            document.getElementById('semester').style.display = 'none';
            document.getElementById('group').style.display = 'none';
        }
    }
</script>
<body class="auth">
    <div class="reg">
        <form method="post" action="registration.php" onsubmit="return validateReg(this)">
            <div class="errors">
                <?php echo $error ?>
            </div>
            <div class="str-input-reg">
                <label for="surname">Фамилия</label>
                <input type="text" name="surname" maxlength="32" required value=" <?php echo $surname ?> ">
            </div>
            <div class="str-input-reg">
                <label for="name">Имя</label>
                <input type="text" name="name" maxlength="32" required value="<?php echo $name ?>">
            </div>
            <div class="str-input-reg">
                <label for="midname">Отчество</label>
                <input type="text" name="midname" maxlength="32" required value="<?php echo $midname ?>">
            </div>
            <div class="str-input-reg">
                <label for="email">E-mail</label>
                <input type="email" name="email" maxlength="50" required value="<?php echo $email ?>">
            </div>
            <div class="str-input-reg">
                <label for="password">Пароль</label>
                <input type="password" name="password" maxlength="50" required>
            </div>
            <div class="list" id="role-list">
                <select name="role" size="1" required onchange="showSelectorsForStudent(this.value)">
                    <option selected>Выберите...</option>
                    <option value="lecturer">Преподаватель</option>
                    <option value="student">Студент</option>
                </select>
            </div>
            <div class="list" id="semester">
                <select name="semester" size="1" required>
                    <option selected>Курс...</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="list" id="group">
                <select name="group" size="1" required>
                    <option selected>Группа...</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="btn-reg" id="at-reg">
                <button type="submit">Зарегистрироваться</button>
                <a href="authentication.php" class="to-auth">Войти</a>
            </div>
        </form>
    </div>
</body>
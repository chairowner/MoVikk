<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <div id="login-form-overlay">
        <div id="login-form" class="shadowBox">
            <form id="login-form-auth" class="item active">
                <input class="field shadowBox w-100 phoneNumberField" type="tel" name="phone" placeholder="Номер телефона" pattern="[0-9]{3} [0-9]{3}-[0-9]{2}-[0-9]{2}" data-pattern="(999) 999-99-99" title="Номер телефона" required>
                <input class="field shadowBox w-100" type="password" name="password" placeholder="Пароль" title="Пароль" required>
                <input class="button shadowBox w-100" type="submit" value="Войти">
            </form>
            <form id="login-from-reg" class="item">
                <input class="field shadowBox w-100 phoneNumberField" type="tel" name="phone" placeholder="Номер телефона" pattern="[0-9]{3} [0-9]{3}-[0-9]{2}-[0-9]{2}" data-pattern="(999) 999-99-99" title="Номер телефона" required>
                <input class="field shadowBox w-100" type="password" name="password" placeholder="Пароль" title="Пароль" required>
                <input class="field shadowBox w-100" type="password" name="passwordRepeat" placeholder="Повторите пароль" title="Повторите пароль" required>
                <input class="button shadowBox" type="submit" value="Зарегистрироваться">
            </form>
        </div>
    </div>
    <?php include_once('includes/scripts.php');?>
</body>
</html>
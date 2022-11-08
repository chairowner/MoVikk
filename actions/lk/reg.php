<?php
set_include_path('../../');
require_once('functions/getCaptcha.php');
require_once('includes/autoload.php');
$COMPANY = new Company($conn);
$USER = new User($conn);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'classes/PHPMailer/Exception.php';
require_once 'classes/PHPMailer/PHPMailer.php';
require_once 'classes/PHPMailer/SMTP.php';

if (!$USER->isGuest() && !$USER->isAdmin()) {
    require_once('404.php');
}

$response = [
    'status' => false,
    'info' => []
];

$next = true;

if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
    $_POST['g-recaptcha-response'] = null;
}
    
$recaptcha = getCaptcha($_POST['g-recaptcha-response'], reCAPTCHA_SECRET_KEY);

if ($recaptcha->success == true && $recaptcha->score > 0.5 || true) {
    if (!isset($_POST['terms'])) {
        $response['info'][] = "Для регистрации необходимо согласиться с правилами";
        $next = false;
    }
    if (!isset($_POST['name']) || trim($_POST['name']) === "") {
        $response['info'][] = "Укажите ваше имя";
        $next = false;
    }
    if (!isset($_POST['surname']) || trim($_POST['surname']) === "") {
        $response['info'][] = "Укажите вашу фамиилю";
        $next = false;
    }
    if (!isset($_POST['email']) || trim($_POST['email']) === "") {
        $response['info'][] = "Заполните E-mail";
        $next = false;
    } else if (filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) === false) {
        $response['info'][] = "Введите корректный E-mail";
        $next = false;
    } else if (filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) !== false) {
        $emailRes = $conn->prepare("SELECT `id` FROM `users` WHERE `email` = ?");
        $emailRes->execute([trim($_POST['email'])]);
        $emailRes = $emailRes->fetch(PDO::FETCH_ASSOC);
        if ($emailRes !== false) {
            $response['info'][] = "E-mail уже занят";
            $next = false;
        }
    }
    if (!isset($_POST['password'])) {
        $response['info'][] = "Заполните пароль";
        $next = false;
    }
    if (!isset($_POST['passwordRepeat'])) {
        $response['info'][] = "Поле повтора пароля не заполнено";
        $next = false;
    }

    if ($next) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $passwordRepeat = trim($_POST['passwordRepeat']);
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $patronymic = isset($_POST['patronymic']) && trim($_POST['patronymic']) !== "" ? trim($_POST['patronymic']) : null;
        
        $reg = $USER->registration($email,$password,$passwordRepeat,$name,$surname,$patronymic);

        if (!$reg['status']) exit(json_encode($reg, JSON_UNESCAPED_UNICODE));
        
        # ссылка на сайт
        $siteUrl = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        $siteUrl .= "://{$_SERVER['HTTP_HOST']}";
        # ссылка подтверждения
        $verifyUrl = "{$siteUrl}/confirmEmail/{$hash}";

        $companyName = $COMPANY->name;

        $userName = $name;
        if (isset($patronymic)) $userName .= $patronymic;

        $mailSubject = "Регистрация в интернет-магазине MoVikk";
        $mailBody =
        "<div style=\"min-height:200px;\">".
            "<p style=\"margin:0 0 40px 0;\">Для завершения регистрации в интернет-магазине <a href=\"{$siteUrl}\"><strong>{$COMPANY->name}</strong></a> подтвердите свою почту:</p>".
            "<div style=\"margin:0 0 40px 0;\"><a href=\"{$verifyUrl}\" style=\"padding:15px 20px;font-weight:bold;color:#ffffff;background-color:#333333;border-radius:8px;\">Подтвердить E-mail</a></div>".
            "<span style=\"font-size:12px;\">Если кнопка не работает, перейдите по этой ссылке: <a href=\"{$verifyUrl}\">{$verifyUrl}</a></span>";
        "</div>";
        $mailFrom = "no-reply@movikk.ru";
        $mailPass = "wa57x3Zm";
        $cipher = "ssl";
        $host = "smtp.timeweb.ru";
        $port = 465;

        $mailTo = $email;

        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";
        # Настройки SMTP
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        // $mail->SMTPDebug = 2;
        # Настройки отправки
        $mail->Host = "{$cipher}://{$host}";
        $mail->Port = $port;
        $mail->Username = $mailFrom;
        $mail->Password = $mailPass;
        $mail->setFrom($mailFrom, $companyName); # От кого	
        $mail->addAddress($mailTo, $userName); # Кому
        $mail->Subject = $mailSubject; # Тема письма
        $mail->msgHTML($mailBody); # Тело письма

        if ($mail->send()) {
            $response['status'] = true;
            $response['info'][] = "Сообщение с ссылкой на подтверждение регистрации выслано на почту \"{$mailTo}\".\nЕсли вы не видите письмо, проверьте папку \"Спам\"";
        } else {
            $response['info'][] = "К сожалению, нам не удалось отправить код регистрации.\nПопробуйте указать другую почту.";
        }
    }
} else {
    $response['info'][] = 'Ошибка заполнения капчи';
}

echo(json_encode($response, JSON_UNESCAPED_UNICODE));
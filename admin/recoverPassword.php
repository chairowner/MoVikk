<?php
set_include_path("../");
require_once('includes/autoload.php');
require_once('functions/insertIntoTemplate.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('classes/PHPMailer/Exception.php');
require_once('classes/PHPMailer/PHPMailer.php');
require_once('classes/PHPMailer/SMTP.php');

$_PAGE = new Page($conn);
$_PAGE->description = "Страница смены пароля";
$_PAGE->title = "Смена пароля";
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_EMAIL = new Email($conn);

$account = null;

if (isset($_GET['hash'])) /* проверка ссылки на восстановление */ {
    $_GET['hash'] = trim($_GET['hash']);
    if ($_GET['hash'] === "") {
        $_PAGE->Redirect();
    } else {
        // $account = $conn->prepare("SELECT `id` FROM `{$_USER->GetTable()}` WHERE `recoveryHash` = :recoveryHash AND (`recoveryHashDate` + INTERVAL '1' HOUR) < CURRENT_TIMESTAMP");
        $account = $conn->prepare("SELECT `id` FROM `{$_USER->GetTable()}` WHERE `recoveryHash` = :recoveryHash");
        $account->execute(['recoveryHash' => $_GET['hash']]);
        $account = $account->fetch(PDO::FETCH_ASSOC);
    }

    if (isset($account) && !empty($account)) /* обновление пароля */ {
        if (isset($_POST['action'])) {
            $response = [
                'status' =>  false,
                'info' => []
            ];
            
            $account['id'] = (int) $account['id'];
            
            if (!$_USER->isGuest() && $account['id'] > 0) {
                if ($_USER->GetId() === $account['id']) {
                    $_USER->RemoveRecoveryHash($account['id']);
                    $_PAGE->Redirect();
                }
            } 
    
            $password = null;
            $passwordRepeat = null;
        
            if (isset($_POST['password'])) {
                $password = trim($_POST['password']);
                if ($password === "") {
                    $password = null;
                    $response['info'][] = "Заполните пароль";
                }
            } else {
                $response['info'][] = "Заполните пароль";
            }
        
            if (isset($_POST['passwordRepeat'])) {
                $passwordRepeat = trim($_POST['passwordRepeat']);
                if ($passwordRepeat === "") {
                    $passwordRepeat = null;
                    $response['info'][] = "Повторите пароль";
                }
            } else {
                $response['info'][] = "Повторите пароль";
            }
        
            if (count($response['info']) === 0) {
                $response = $_USER->ValidatePassword($password, $passwordRepeat);
                if ($response['status']) {
                    $response = $_USER->ChangePassword($account['id'], $password);
                }
            }
        }
    } else {
        $_PAGE->Redirect();
    }
} elseif (isset($_POST['email'])) /* отправка сообщения */ {
    $response = [
        'status' =>  false,
        'info' => []
    ];
    $mailTo = trim($_POST['email']);
    if (filter_var($mailTo, FILTER_VALIDATE_EMAIL) !== false) {
        // поиск почты в БД
        $query = $conn->prepare("SELECT `id`, `recoveryHash`, `recoveryHashDate` FROM `{$_USER->GetTable()}` WHERE `email` = :email");
        $query->execute(['email' => $mailTo]);
        $query = $query->fetch(PDO::FETCH_ASSOC);

        if (isset($query) && !empty($query)) /* почта есть в системе */ {
            // проверяем, отсылалось ли сообщение на смену пароля (если да - проверяем, прошёл ли час после отправки)
            $canSendEmail = false;
            if (isset($query['recoveryHashDate'])) {
                $query['recoveryHashDate'] = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($query['recoveryHashDate'])));
                if ($query['recoveryHashDate'] < date("Y-m-d H:i:s")) {
                    $canSendEmail = true;
                } else {
                    $canSendEmail = false;
                }
            } else {
                $canSendEmail = true;
            }
            if ($canSendEmail) {
                $hash = md5($mailTo.time());
        
                $query = $conn->prepare("UPDATE `{$_USER->GetTable()}` SET `recoveryHash` = :recoveryHash, `recoveryHashDate` = :recoveryHashDate WHERE `email` = :email");
                $query->execute(['recoveryHash' => $hash, 'recoveryHashDate' => date("Y-m-d H:i:s"), 'email' => $mailTo]);
                
                # ссылка на сайт
                $site_link = "https://{$_SERVER['HTTP_HOST']}";
                # ссылка подтверждения
                $recovery_link = "$site_link/recoverPassword/$hash";
            
                $companyName = $_COMPANY->name;
            
                $mailSubject = "Смена пароля | $companyName";
                $mailBody = file_get_contents("assets/templates/email/movikk_recover_password_template.html", true);
                $mailBody = insertIntoTemplate(['company_name' => $companyName,'site_link' => $site_link, 'recovery_link' => $recovery_link, 'year' => date("Y"), 'vk_link' => $_COMPANY->socials['vk']['href'], 'instagram_link' => $_COMPANY->socials['instagram']['href']], $mailBody);
        
                $smtpSettings = $_EMAIL->Get();
            
                $mailFrom = trim($smtpSettings['email']);
                $mailPass = trim($smtpSettings['password']);
                $cipher = trim($smtpSettings['cipher']);
                $host = trim($smtpSettings['host']);
                $port = (int) $smtpSettings['port'];
            
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
                $mail->addAddress($mailTo); # Кому
                $mail->Subject = $mailSubject; # Тема письма
                $mail->msgHTML($mailBody); # Тело письма
            
                if ($mail->send()) {
                    $response['status'] = true;
                    $response['info'][] = "Сообщение с ссылкой на сброс пароля выслано на почту \"{$mailTo}\"";
                    $response['info'][] = "Если вы не видите письмо, проверьте папку \"Спам\"";
                } else {
                    $response['info'][] = "К сожалению, нам не удалось отправить письмо для сброса пароля";
                    $response['info'][] = "Попробуйте указать другую почту";
                }
            } else {
                $response['info'][] = "Сообщение с ссылкой на восстановление уже отправлено на почту!";
                $response['info'][] = "Делать запрос на смену пароля можно раз в час.";
            }
        } else {
            $response['info'][] = "E-mail не найден";
        }
    } else {
        $response['info'][] = "Введённый E-mail некорректен";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest())?>
    <link rel="stylesheet" href="/assets/common/css/recoverPassword.css">
    <script defer src="/assets/common/js/recoverPassword.js"></script>
</head>
<body>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <main class="w-100 h-100" style="min-height:35vh;">
        <div class="container">
            <div class="d-flex flex-column align-items-center gap-10" style="border-radius:8px;padding:20px;background-color:white;">
                <?php $response_info_count = 0;
                if(isset($account) && !empty($account)):?>
                    <?php if(isset($_POST['action'])):?>
                        <?php
                        $response_info_count = count($response['info']);
                        if($response_info_count > 0):?>
                            <div class="d-flex flex-column justify-content-center align-items-center gap-10">
                                <?php for($i = 0; $i < $response_info_count; $i++):?>
                                    <p class="text-center"><?=$response['info'][$i]?></p>
                                <?php endfor;?>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
                    <?php if(!isset($_POST['action']) || (isset($_POST['action']) && !$response['status'])):?>
                        <form method="POST" id="sendRecoveryLink" class="d-flex flex-column gap-10 w-100" style="max-width:500px;">
                            <input type="hidden" name="action" value="change">
                            <div class="d-flex flex-column gap-5 w-100">
                                <label for="password"><b>Пароль</b></label>
                                <div class="passwordBlock w-100">
                                    <input class="field w-100" id="password" type="password" name="password" placeholder="Пароль" title="Пароль" required>
                                    <div class="h-100 d-flex align-items-center">
                                        <div class="passwordView close" title="Показать пароль">
                                            <span class="passwordView-eyelid"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-5 w-100">
                                <label for="passwordRepeat"><b>Повторите пароль</b></label>
                                <input type="password" id="passwordRepeat" name="passwordRepeat" class="field w-100" placeholder="Повторите пароль" title="Повторите пароль" required>
                            </div>
                            <input type="submit" class="button w-100" style="white-space: unset;min-height:45px;height:auto;" value="Обновить пароль">
                        </form>
                    <?php endif;?>
                <?php else:?>
                    <?php if(isset($mailTo)):?>
                        <?php
                        $response_info_count = count($response['info']);
                        if($response_info_count > 0):?>
                            <?php for($i = 0; $i < $response_info_count; $i++):?>
                                <p><?=$response['info'][$i]?></p>
                            <?php endfor;?>
                        <?php endif;?>
                    <?php else:?>
                        <form method="POST" class="d-flex flex-column gap-10 w-100" style="max-width:500px;">
                            <div class="d-flex flex-column gap-5 w-100">
                                <label for="email"><b>E-mail</b></label>
                                <input type="email" id="email" name="email" class="field w-100" placeholder="E-mail" title="E-mail" required>
                            </div>
                            <input type="submit" class="button w-100" style="white-space: unset;min-height:45px;height:auto;" value="Отправить ссылку на восстановление пароля">
                        </form>
                    <?php endif;?>
                <?php endif;?>
            </div>
        </div>
    </main>
</body>
</html>
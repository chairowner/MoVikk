<?php
set_include_path('../../');
require_once('functions/getCaptcha.php');
require_once('includes/autoload.php');
$PAGE = new Page($conn);
$COMPANY = new Company($conn);
$USER = new User($conn);

if (!$USER->isGuest()) require_once('404.php');

if (isset($_GET['hash'])) {
    $hash = trim($_GET['hash']);
    $query = $conn->prepare("SELECT `isEmailConfirmed` FROM `users` WHERE `hash` = ?");
    $query->execute([$hash]);
    $query = $query->fetch(PDO::FETCH_ASSOC);
    if (isset($query) && !empty($query)) {
        if ((bool) $query['isEmailConfirmed'] === false) {
            $query = $conn->prepare("UPDATE `users` SET `isEmailConfirmed` = 1 WHERE `hash` = ? AND isEmailConfirmed = 0");
            if ($query->execute([$hash])) {
                $confirmInfo = "E-mail подтверждён!";
            } else {
                $confirmInfo = "E-mail уже подтверждён";
            }
        }
    }
}

if (!isset($confirmInfo)):
    $response = [
        'status' => false,
        'info' => null
    ];
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        $_POST['g-recaptcha-response'] = null;
    }
        
    $recaptcha = getCaptcha($_POST['g-recaptcha-response'], reCAPTCHA_SECRET_KEY);
    
    if ($recaptcha->success == true && $recaptcha->score > 0.5 || true) {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $response = $USER->login($email, $password);
        } else {
            $response['info'][] = json_encode($_POST);
            $response['info'][] = 'Заполните все поля';
        }
    } else {
        $response['info'][] = 'Ошибка заполнения капчи';
    }
    
    echo(json_encode($response, JSON_UNESCAPED_UNICODE));
else:?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <?=$PAGE->getHead($USER->isGuest(), $confirmInfo)?>
    </head>
    <body>
        <?php include_once('includes/header.php');?>
        <main style="height: 27vh;">
            <section class="h-100 w-100">
                <div class="container h-100 w-100 d-flex justify-content-center align-items-center">
                    <h2 class="text-uppercase text-center m-0"><?=$confirmInfo?></h2>
                </div>
            </section>
        </main>
        <?php include_once('includes/footer.php');?>
    </body>
    </html>
<?php endif;
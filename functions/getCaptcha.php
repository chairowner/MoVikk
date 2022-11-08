<?php
function getCaptcha($captchaData, $secretKey) {
    $response = new stdClass();
    $response->success = false;
    $response->score = 0.0;
    if ((isset($captchaData) && !empty($captchaData)) && (isset($secretKey) && !empty($secretKey))) {
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captchaData}");
        $response = json_decode($response);
    }
    return $response;
}
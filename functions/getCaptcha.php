<?php
function getCaptcha($captchaData, $secretKey) {
    $response = new stdClass();
    $response->success = false;
    $response->score = 0.0;
    $url = null;
    try {
        if ((isset($captchaData) && !empty($captchaData)) && (isset($secretKey) && !empty($secretKey))) {
            $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captchaData}";
            $response = file_get_contents($url);
            $response = json_decode($response);
            $response->url = $url;
        }
    } catch (Exception $e) {
        $response->success = false;
        $response->score = 0.0;
        $response->error = $e->getMessage();
        $response->url = $url;
    }
    return $response;
}
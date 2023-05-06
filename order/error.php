<?php
$response = [
    'file_get_contents(\'php://input\')' => file_get_contents("php://input"),
    '$_POST' => $_POST,
    '$_GET' => $_GET,
];
echo("<pre>");
echo(json_encode($response,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
exit("</pre>");
//develop.movikk/order/error?Success=false&ErrorCode=9999&Message=%D0%9F%D0%BE%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D1%82%D0%B5+%D0%BF%D0%BE%D0%BF%D1%8B%D1%82%D0%BA%D1%83+%D0%BF%D0%BE%D0%B7%D0%B6%D0%B5.&Details=&Amount=0&MerchantEmail=Obrazchaik%40gmail.com&MerchantName=MoVikk&OrderId=22&PaymentId=2521093507&TranDate=&BackUrl=https%3A%2F%2Fmovikk.ru&CompanyName=%D0%98%D0%9F+%D0%A1%D0%90%D0%93%D0%90%D0%99%D0%94%D0%90%D0%A7%D0%9D%D0%90%D0%AF+%D0%90%D0%9D%D0%90%D0%A1%D0%A2%D0%90%D0%A1%D0%98%D0%AF+%D0%90%D0%9B%D0%95%D0%9A%D0%A1%D0%90%D0%9D%D0%94%D0%A0%D0%9E%D0%92%D0%9D%D0%90&EmailReq=Obrazchaik%40gmail.com&PhonesReq=9223127607
?>
<!DOCTYPE html>
<html lang="ru" class="w-100 h-100">
<head>
    <title>Оплата не прошла</title>
    <link rel="stylesheet" href="/assets/common/css/main.css">
</head>
<body class="w-100 h-100" style="position: relative;">
    <div style="position: absolute; text-align:center;top:20px;" class="w-100">
        <a href="/" class="text-uppercase" title="MoVikk">
            <img src="/assets/icons/logo.svg" alt="MoVikk" class="logo">
        </a>
    </div>
    <main class="w-100 h-100 d-flex justify-content-center align-items-center">
        <div>
            <h1 style="text-align: center;">Оплата не прошла</h1>
            <p style="text-align: center;" id="js-counter"></p>
        </div>
    </main>
    <script>
        let index = 3;
        const href = "/cart";
        const interval = 1000;
        const counter_text = "Переадресация будет через: ";
        const counter = document.getElementById("js-counter");
        counter.innerHTML = counter_text + index;
        setInterval(() => {
            index--;
            if (index > 0) {
                counter.innerHTML = counter_text + index;
            } else if (index === 0) {
                counter.innerHTML = "Переадресация...";
            } else {
                location.href = href;
            }
        }, interval);
    </script>
</body>
</html>
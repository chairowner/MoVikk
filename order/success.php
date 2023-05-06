<?php
$response = [
    'file_get_contents(\'php://input\')' => file_get_contents("php://input"),
    '$_POST' => $_POST,
    '$_GET' => $_GET,
];
echo("<pre>");
echo(json_encode($response,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
echo("</pre>");
?>
<!-- <!DOCTYPE html>
<html lang="ru" class="w-100 h-100">
<head>
    <title>Оплата прошла успешно</title>
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
            <h1 style="text-align: center;">Оплата прошла успешно</h1>
            <p style="text-align: center;" id="js-counter"></p>
        </div>
    </main>
    <script>
        let index = 3;
        const href = "/orders";
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
</html> -->
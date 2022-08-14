<?php
set_include_path('includes');
$company = new stdClass();
$company->name = 'MoVikk';
$company->description = 'Оборудование для салонов красоты и расходники по приятным ценам';
$company->vk = 'vk.com/movikk';
$company->inst = 'movikk';
$company->tel = '+79223127607';
$company->tel_format = sprintf(
    "%s (%s) %s-%s-%s",
    intval(substr($company->tel, 1, 1)) + 1,
    substr($company->tel, 2, 3),
    substr($company->tel, 5, 3),
    substr($company->tel, 8, 2),
    substr($company->tel, 10)
);

$this_page = basename($_SERVER['PHP_SELF']);
$pages = [
    ['href' => '/', 'title' => 'Главная'],
    ['href' => '/shop', 'title' => 'Каталог'],
    ['href' => '/study', 'title' => 'Обучение'],
    ['href' => '/faq', 'title' => 'FAQ'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.png" type="image/png">
    <link rel="stylesheet" href="/css/nullstyle.css">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">
    <title><?=$company->name;?> - <?=mb_strtolower($company->description)?></title>
    <meta name="description" content="<?=$company->description?>">
</head>
<body>
    <?php include('header.php');?>
    <section>
        <div class="container">
            <h2>Чаще всего у нас покупают</h2>
            <div class="d-flex flex-row">
            <div class="card" style="width: 18rem;">
                <img src="..." class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
            </div>
        </div>
    </section>
    <?php include('footer.php');?>
    <?php include('scripts.php');?>
</body>
</html>
<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('functions/formatPrice.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Заказы";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_ORDER = new Order($conn);
$_USER = new User($conn);

if (!$_USER->isAdmin()) {
    $_PAGE->Redirect();
}

$viewCount = 32; // максимальное кол-во записей для вывода
$all_count = 0; // кол-во записей в таблице
$pattern = "http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "s" : "")."://{$_SERVER['SERVER_NAME']}"; // паттерн адреса страницы
$pagination = null;
$pagesData = [];
$currentPageNumber = 1; // страница
$search = null; // поиск

// редактируемая категория
$editCategory = explode('/',$_SERVER['PHP_SELF']);
$editCategory = basename($editCategory[count($editCategory) - 1], '.php');

if (isset($_GET['page'])) {
    $_GET['page'] = (int) $_GET['page'];
    if ($_GET['page'] > 0) {
        $currentPageNumber = $_GET['page'];
    } else {
        $_GET['page'] = $currentPageNumber;
    }
} else {
    $_GET['page'] = $currentPageNumber;
}

if (isset($editId)) {
    $data = $_ORDER->GetOrders('one', $editId);
    if (!isset($data['orders'][0])) $_PAGE->Redirect("admin/$editCategory");
} else {
    $execute = [];
    if (isset($_GET['search']) && trim($_GET['search']) !== "") {
        $search = trim($_GET['search']);
        $data = "SELECT o.id, u.surname user_surname FROM orders o INNER JOIN users u ON u.id = o.userId WHERE o.id = :orderId OR u.surname LIKE :search OR o.fullName LIKE :search ORDER BY o.id DESC LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
        $execute['orderId'] = (int)$search;
        $execute['search'] = "%$search%";
    } else {
        $data = "SELECT o.id, u.surname user_surname FROM orders o INNER JOIN users u ON u.id = o.userId ORDER BY o.id DESC LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
    }
    $data = $conn->prepare($data);
    $data->execute($execute);
    $data = $data->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($editId) && count($data) < 1) $_PAGE->Redirect("admin/$editCategory");
elseif (($currentPageNumber !== 1) && count($data) < 1) $_PAGE->Redirect("admin/$editCategory");

if (isset($editId)) {
    $pattern .= "/admin/$editCategory?action=edit&id=#";
} else {
    $all_count_execute = [];

    if (isset($search)) {
        $pattern .= "/admin/$editCategory?search=".addslashes(htmlspecialchars(trim($search)))."&page=#";
        $all_count = "SELECT COUNT(o.id) `counter` FROM `{$_ORDER->GetTable()}` o INNER JOIN users u ON u.id = o.userId WHERE o.id = :orderId OR u.surname LIKE :search OR o.fullName LIKE :search";
        $all_count_execute['orderId'] = (int)$search;
        $all_count_execute['search'] = "%$search%";
    } else {
        $pattern .= "/admin/$editCategory?page=#";
        $all_count = "SELECT COUNT(o.id) `counter` FROM `{$_ORDER->GetTable()}` o INNER JOIN users u ON u.id = o.userId";
    }
    
    $all_count = $conn->prepare($all_count);
    $all_count->execute($all_count_execute);
    $all_count = (int) $all_count->fetch(PDO::FETCH_ASSOC)['counter'];
    
    $pagination = new Pagination($currentPageNumber, $all_count, $pattern, $viewCount);
    $pagination->SetBeforeCurrent(2);
    $pagination->SetAfterCurrent(2);
    $pagination->SetButtonTitle(Pagination::PREVIOUS_BUTTON, 'Назад');
    $pagination->SetButtonTitle(Pagination::NEXT_BUTTON, 'Далее');
    $pagination->SetMainStyle("margin:40px;");
    $pagesData = [
        'currentPageNumber' => $currentPageNumber,
        'next' => $pagination->GetNextPage(),
        'prev' => $pagination->GetPreviousPage()
    ];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), "{$_PAGE->title} - {$_PAGE->description}", $_PAGE->description)?>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="/classes/php-pagination/css/main.css">
    <link rel="stylesheet" href="assets/css/<?= $editCategory ?>.css">
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="assets/js/actions.js"></script>
    <script defer src="assets/js/<?= $editCategory ?>/<?= $editCategory ?>.js"></script>
</head>
<body>
    <div class="page__title">
        <div class="container">
            <a href="/"><img src="/assets/icons/logo.svg" class="logo" alt="<?=$_COMPANY->name?>"></a>
            <h1 class="container"><a href="/admin"><?=$_PAGE->title?></a> - <a href="/admin/<?=$editCategory?>"><?=$_PAGE->description?></a></h1>
        </div>
    </div>
    <main style="min-height: 35vh;">
        <input type="hidden" data-editCategory="<?=$editCategory?>">
        <section class="m-0">
            <div class="container">
                <div id="main">

                    <?php if (isset($editId) && $action === "edit"): // редактирование?>

                        <?php $order = $data['orders'][0];?>
                        <script>console.log(<?=json_encode($order,JSON_UNESCAPED_UNICODE)?>)</script>
                        <div class="block">
                            <form method="POST" action="/admin/actions/<?=$editCategory?>/edit" id="formData" class="d-flex flex-column gap-20">
                            <input type="hidden" name="orderId" value="<?=$editId?>">
                            <input type="hidden" name="action" value="edit">
                                <ul id="order-list">
                                    <li class="item flex-row justify-content-end" id="order-number">
                                        <span>Заказ №<?=$order['id']?></span>
                                    </li>
                                    <li class="item flex-row align-items-center flex-wrap" id="js-first-item">
                                        <span class="title">Статус заказа:</span>
                                        <?php
                                        $statusArray = [];
                                        if ($order['status'] === "Ожидание оплаты"):?>
                                            <span><?=$order['status']?></span>
                                        <?php else:?>
                                            <select name="newStatus" class="field">
                                                <?php $statusArray = $_ORDER->GetStatusArray("progress");
                                                foreach($statusArray as $key => $status):?>
                                                    <option value="<?=$status?>"<?=$order['status'] === $status ? " selected" : null?>><?=$status?></option>
                                                <?php endforeach;?>
                                            </select>
                                        <?php endif;?>
                                    </li>
                                    <li class="item flex-row">
                                        <span class="title">Стоимость заказа:</span>
                                        <span><?=formatPrice($order['price'])?></span>
                                    </li>
                                    <?php
                                    $deliveryTable = (new Delivery($conn))->GetTable();
                                    $deliveryArray = $conn->prepare("SELECT * FROM `$deliveryTable`");
                                    $deliveryArray->execute();
                                    $deliveryArray = $deliveryArray->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <li class="item">
                                        <span class="title">Выбранная доставка:</span>
                                        <?php if(count($deliveryArray) > 0):?>
                                            <select class="field" name="deliveryId">
                                                <option value="0"<?=!isset($order['delivery']['id']) ? " selected" : null?>>Не выбрана</option>
                                                <?php foreach($deliveryArray as $key => $delivery):
                                                    $delivery['id'] = (int) $delivery['id'];?>
                                                    <option value="<?=$delivery['id']?>"<?=isset($order['delivery']['id']) && $order['delivery']['id'] === $delivery['id'] ? " selected" : null?>><?=$delivery['name']?></option>
                                                <?php endforeach;?>
                                            </select>
                                        <?php else:?>
                                            <span>Доставка не выбрана</span>
                                        <?php endif;?>
                                    </li>
                                    <li class="item">
                                        <span class="title">Номер отслеживания:</span>
                                        <input type="text" class="field" name="tracking" placeholder="Не отслеживается" value="<?=isset($order['tracking']) ? $order['tracking'] : null?>">
                                    </li>
                                    <li class="item">
                                        <span class="title">Товары:</span>
                                        <ul style="list-style-type: none;">
                                            <?php foreach($order['products'] as $product_key => $product):?>
                                                <li>
                                                    <a href="<?=$product['href']?>" target="_blank" title="Открыть товар в новой вкладке"><?=$product['name']?></a>
                                                    <span>- <?=$product['count']?> ед.</span>
                                                </li>
                                            <?php endforeach;?>
                                        </ul>
                                    </li>
                                    <li class="item flex-row">
                                        <span class="title">Дата создания заказа:</span>
                                        <span><?=$order['added']?></span>
                                    </li>
                                    <?php if((bool)$order['isClosed'] && isset($order['closed'])):?>
                                        <li class="item">
                                            <ul style="list-style-type: none;">
                                                <li class="d-flex flex-row flex-wrap gap-5">
                                                    <span>Дата закрытия заказа:</span>
                                                    <span><?=$order['closed']?></span>
                                                </li>
                                                <?php if(isset($order['idWhoClosed'])):?>
                                                    <li class="d-flex flex-row flex-wrap gap-5">
                                                        <span>Пользователь, закрывший заказ:</span>
                                                        <span><?=$order['idWhoClosed']?></span>
                                                    </li>
                                                <?php endif;?>
                                            </ul>
                                        </li>
                                    <?php endif;?>
                                    <li class="item">
                                        <span class="title">Информация о заказе:</span>
                                        <span class="d-flex flex-wrap gap-10">
                                            <span class="fw-bold">Адрес:</span>
                                            <a class="js-copy" data-copy-error="Не удалось скопировать адрес" data-copy-success="Адрес скопирован" title="Скопировать"><?=$order['address']?></a>
                                        </span>
                                        <span class="d-flex flex-wrap gap-10">
                                            <span class="fw-bold">Телефон:</span>
                                            <a class="js-copy" data-copy-error="Не удалось скопировать номер телефона" data-copy-success="Номер телефона скопирован" title="Скопировать"><?=$order['phone']?></a>
                                        </span>
                                    </li>
                                    <?php if(isset($order['userComment'])):?>
                                        <li class="item">
                                            <span class="title">Комментарий пользователя к заказу:</span>
                                            <p class="comment"><?=$order['userComment']?></p>
                                        </li>
                                    <?php endif;?>
                                    <li class="item">
                                        <span class="title">Комментарий магазина к заказу:</span>
                                        <textarea name="adminComment" class="comment"><?=isset($order['adminComment']) ? trim($order['adminComment']) : null?></textarea>
                                    </li>
                                </ul>
                                <input type="submit" class="button" value="Сохранить">
                            </form>
                        </div>

                    <?php else: // вывод всех элементов?>
                    
                        <form id="form-search">
                            <div class="search">
                                <input type="text" name="search" placeholder="Поиск заказа" value="<?=$search?>">
                            </div>
                        </form>

                        <?php if(count($data) > 0):?>

                            <div class="item-list">

                                <?php foreach($data as $key => $item):
                                    $item['id'] = (int) $item['id'];
                                    $item['user_surname'] = trim($item['user_surname']);?>

                                    <a class="item shadowBox" href="/admin/<?=$editCategory?>?action=edit&id=<?=$item['id']?>">
                                        <div class="item-content d-flex flex-column">
                                            <span class="d-flex flex-row flex-wrap gap-5">
                                                <span class="fw-bold">Номер заказа:</span>
                                                <span><?=$item['id']?></span>
                                            </span>
                                            <span class="d-flex flex-row flex-wrap text-center gap-5"><?=$item['user_surname']?></span>
                                        </div>
                                    </a>

                                <?php endforeach;?>

                            </div>

                        <?php else:?>
                            
                            <p class="text-center">Не найдено ни одного заказа</p>
                            
                        <?php endif;?>

                    <?php endif;?>

                </div>
            </div>
        </section>
    </main>
    <?php if (isset($pagination)):?><div class="container"><?=$pagination->Render()?></div><?php endif;?>
    <div id="mainMessageBox"></div>
</body>
</html>
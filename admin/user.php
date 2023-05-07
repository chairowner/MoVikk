<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('functions/formatPrice.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Пользователи";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
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

$execute = [];
if (isset($editId)) {
    $data = "SELECT * FROM `{$_USER->GetTable()}` WHERE `id` = :id";
    $execute = ['id' => $editId];
} else {
    if (isset($_GET['search']) && trim($_GET['search']) !== "") {
        $search = trim($_GET['search']);
        $data = "SELECT * FROM `{$_USER->GetTable()}` WHERE `name` LIKE :search OR `surname` LIKE :search OR `patronymic` LIKE :search OR `email` LIKE :search LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
        $execute['search'] = "%$search%";
    } else {
        $data = "SELECT * FROM `{$_USER->GetTable()}` LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
    }
}
$data = $conn->prepare($data);
$data->execute($execute);
$data = $data->fetchAll(PDO::FETCH_ASSOC); 

if (isset($editId) && count($data) < 1) $_PAGE->Redirect("$adminUrl/$editCategory");
elseif (($currentPageNumber !== 1) && count($data) < 1) $_PAGE->Redirect("$adminUrl/$editCategory");

if (isset($editId)) {
    $pattern .= "$editCategory?action=edit&id=#";
} else {
    $all_count_execute = [];
    if (isset($search)) {
        $pattern .= "$editCategory?search=".addslashes(htmlspecialchars(trim($search)))."&page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_USER->GetTable()}` WHERE `name` LIKE :search OR `href` LIKE :search";
        $all_count_execute = ['search' => "%$search%"];
    } else {
        $pattern .= "$editCategory?page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_USER->GetTable()}`";
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
    <link rel="stylesheet" href="assets/css/<?=$editCategory?>.css">
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="assets/js/actions.js"></script>
</head>
<body>
    <div class="page__title">
        <div class="container">
            <a href="/"><img src="/assets/icons/logo.svg" class="logo" alt="<?=$_COMPANY->name?>"></a>
            <h1 class="container"><a href="/<?=$adminUrl?>"><?=$_PAGE->title?></a> - <a href="<?=$editCategory?>"><?=$_PAGE->description?></a></h1>
        </div>
    </div>
    <main>
        <input type="hidden" data-editCategory="<?=$editCategory?>">
        <section class="m-0 h-100">
            <div class="container h-100">
                <div id="main" data-editId="<?=$editId?>">
                    <?php if (isset($editId)): // редактирование?>

                        <?php $item = $data[0]; unset($data);?>
                        <div class="w-100 h-100 d-flex flex-column gap-40 justify-content-center align-items-center">
                            <div id="user-data">
                                <label class="item">
                                    <span class="title">Фамилия</span>
                                    <span class="d-flex flex-wrap gap-5"><span><?=$item['surname']?></span></span>
                                </label>
                                <label class="item">
                                    <span class="title">Имя</span>
                                    <span class="d-flex flex-wrap gap-5"><span><?=$item['name']?></span></span>
                                </label>
                                <label class="item">
                                    <span class="title">Отчество</span>
                                    <span class="d-flex flex-wrap gap-5">
                                        <span><?=isset($item['patronymic']) ? $item['patronymic'] : "<i style=\"color:gray;\">Отсутствует</i>"?></span>
                                    </span>
                                </label>
                                <label class="item">
                                    <span class="title">E-mail</span>
                                    <span><?=$item['email']?></span>
                                </label>
                                <label class="item">
                                    <span class="title">Роль</span>
                                    <span><?=(bool)$item['isAdmin']?"Администратор":"Пользователь"?></span>
                                </label>
                            </div>
                        </div>

                    <?php else: // вывод всех элементов?>

                        <?php if (count($data) > 0):?>

                            <form id="form-search">
                                <div class="search">
                                    <input type="text" name="search" placeholder="Поиск по ФИО, E-mail" value="<?=$search?>">
                                </div>
                            </form>

                            <div class="item-list">

                                <?php foreach($data as $key => $item):
                                    $item['patronymic'] = isset($item['patronymic']) ? mb_substr($item['patronymic'], 0, 1)."." : null;
                                    $item['name'] = isset($item['name']) ? mb_substr($item['name'], 0, 1)."." : null;?>

                                    <a class="item shadowBox" href="<?=$editCategory?>?action=edit&id=<?=$item['id']?>">
                                        <div class="item-content d-flex flex-column align-items-start">
                                            <span class="d-flex flex-row flex-wrap gap-5 fw-bold"><?=trim("{$item['surname']} {$item['name']} {$item['patronymic']}")?></span>
                                            <span class="d-flex flex-row flex-wrap gap-5"><?=$item['email']?></span>
                                            <span class="d-flex flex-row flex-wrap gap-5"><?=(bool)$item['isAdmin'] ? "Администратор" : "Пользователь"?></span>
                                        </div>
                                    </a>

                                <?php endforeach;?>

                            </div>

                        <?php else:?>

                            <p class="text-center">Пользователи не найдены</p>

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
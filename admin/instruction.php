<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Инструкции";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_INSTRUCTIONS = new Instruction($conn);
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

if (isset($editId)) {
} else {
    $data = $_INSTRUCTIONS->Get("all");
}

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
    $data = $_INSTRUCTIONS->Get($editId);
    if (!isset($data['items'][0])) $_PAGE->Redirect(ADMIN_URL."/$editCategory");
} else {
    if (isset($_GET['search']) && trim($_GET['search']) !== "") {
        $search = trim($_GET['search']);
        $data = "SELECT * FROM `{$_INSTRUCTIONS->GetTable()}` WHERE `name` LIKE :search LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
        $execute['search'] = "%$search%";
    } else {
        $data = "SELECT * FROM `{$_INSTRUCTIONS->GetTable()}` LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
    }
    $data = $conn->prepare($data);
    $data->execute($execute);
    $data = $data->fetchAll(PDO::FETCH_ASSOC); 
}

if (isset($editId) && count($data) < 1) $_PAGE->Redirect(ADMIN_URL."/$editCategory");
elseif (($currentPageNumber !== 1) && count($data) < 1) $_PAGE->Redirect(ADMIN_URL."/$editCategory");

if (isset($editId)) {
    $pattern .= "$editCategory?action=edit&id=#";
} else {
    $all_count_execute = [];
    if (isset($search)) {
        $pattern .= "$editCategory?search=".addslashes(htmlspecialchars(trim($search)))."&page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_INSTRUCTIONS->GetTable()}` WHERE `name` LIKE :search";
        $all_count_execute = ['search' => "%$search%"];
    } else {
        $pattern .= "$editCategory?page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_INSTRUCTIONS->GetTable()}`";
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
    <script defer src="/assets/common/js/disableForm.js"></script>
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="assets/js/actions.js"></script>
</head>
<body>
    <div class="page__title">
        <div class="container">
            <a href="/"><img src="/assets/icons/logo.svg" class="logo" alt="<?=$_COMPANY->name?>"></a>
            <h1 class="container"><a href="/<?=ADMIN_URL?>"><?=$_PAGE->title?></a> - <a href="<?=$editCategory?>"><?=$_PAGE->description?></a></h1>
        </div>
    </div>
    <main style="min-height: 35vh;">
        <input type="hidden" data-editCategory="<?=$editCategory?>">
        <section class="m-0">
            <div class="container">
                <div id="main">

                    <?php if ($action === "add"): // создание?>

                        <form method="POST" action="actions/<?=$editCategory?>/add" id="formData">
                            <label class="item">
                                <span>Название<span class="error">*</span></span>
                                <input type="text" name="name" require class="field" maxlength="255">
                            </label>
                            <label class="item">
                                <span>Текст</span>
                                <textarea name="text" class="field"></textarea>
                            </label>
                            <label class="item">
                                <span>Видео</span>
                                <input type="file" name="video" accept=".wmv,.mp4,.avi,.webm,.mov,.mkv">
                            </label>
                            <input type="hidden" name="action" value="add">
                            <div class="item flex-row flex-wrap justify-content-between gap-20">
                                <input type="submit" class="button w-100" value="Добавить">
                                <a href="<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                            </div>
                        </form>

                    <?php else:?>

                        <?php if (isset($editId)): // редактирование?>

                            <?php $item = $data['items'][0];?>
                            <form method="POST" action="actions/<?=$editCategory?>/edit" id="formData">
                                <label class="item">
                                    <span>Название<span class="error">*</span></span>
                                    <input type="text" name="name" require class="field" maxlength="255" value="<?=$item['name']?>">
                                </label>
                                <label class="item">
                                    <span>Текст</span>
                                    <textarea name="text" class="field"><?=$item['text']?></textarea>
                                </label>
                                <?php if(isset($item['video']) && !empty($item['video'])):?>
                                    <label class="item">
                                        <video controls class="item instruction-video">
                                            <source src="/assets/videos/instructions/<?=$item['video']?>" type="video/mp4">
                                            <p>К сожалению, ваш браузер не поддерживает данный формат видео.</p>
                                        </video>
                                    </label>
                                <?php endif;?>
                                <label class="item">
                                    <span>Загрузка нового видео</span>
                                    <input type="file" name="video" accept=".wmv,.mp4,.avi,.webm,.mov,.mkv">
                                </label>
                                <input type="hidden" name="id" value="<?=$item['id']?>">
                                <div class="item flex-row flex-wrap justify-content-between gap-20">
                                    <input type="submit" class="button w-100" value="Сохранить">
                                    <a href="<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                                    <input type="button" class="button error delete w-100" value="Удалить">
                                </div>
                            </form>

                        <?php else: // вывод всех элементов?>

                            <form id="form-search">
                                <div class="search">
                                    <input type="text" name="search" placeholder="Поиск категории" value="<?=$search?>">
                                </div>
                            </form>

                            <a class="button shadowBox" href="<?=$editCategory?>?action=add">Добавить инструкцию</a>

                            <?php if(count($data) > 0):?>

                                <div class="item-list">

                                    <?php foreach($data as $key => $item):
                                        $item['id'] = (int) $item['id'];
                                        $item['name'] = trim($item['name']);?>

                                        <a class="item shadowBox" href="<?=$editCategory?>?action=edit&id=<?=$item['id']?>">
                                            <div class="item-content d-flex flex-column align-items-start">
                                                <span class="d-flex flex-row flex-wrap gap-5"><?=$item['name']?></span>
                                            </div>
                                        </a>

                                    <?php endforeach;?>

                                </div>

                            <?php else:?>
                                
                                <p class="text-center">Не найдено ни одной инструкции</p>
                                
                            <?php endif;?>

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
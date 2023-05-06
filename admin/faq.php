<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "FAQs";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_FAQs = new FAQ($conn);
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
    $data = $_FAQs->Get("all");
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
    $data = $_FAQs->Get($editId);
    if (!isset($data['items'][0])) $_PAGE->Redirect("admin/$editCategory");
} else {
    if (isset($_GET['search']) && trim($_GET['search']) !== "") {
        $search = trim($_GET['search']);
        $data = "SELECT * FROM `{$_FAQs->GetTable()}` WHERE `question` LIKE :search LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
        $execute['search'] = "%$search%";
    } else {
        $data = "SELECT * FROM `{$_FAQs->GetTable()}` LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
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
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_FAQs->GetTable()}` WHERE `question` LIKE :search";
        $all_count_execute = ['search' => "%$search%"];
    } else {
        $pattern .= "/admin/$editCategory?page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_FAQs->GetTable()}`";
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
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="assets/js/actions.js"></script>
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

                    <?php if ($action === "add"): // создание?>

                        <form method="POST" action="/admin/actions/<?=$editCategory?>/add" id="formData">
                            <label class="item">
                                <span>Вопрос<span class="error">*</span></span>
                                <input type="text" name="question" require class="field" maxlength="255">
                            </label>
                            <label class="item">
                                <span>Ответ<span class="error">*</span></span>
                                <input type="text" name="answer" require class="field" maxlength="255">
                            </label>
                            <input type="hidden" name="action" value="add">
                            <div class="item flex-row flex-wrap justify-content-between gap-20">
                                <input type="submit" class="button w-100" value="Добавить">
                                <a href="/admin/<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                            </div>
                        </form>

                    <?php else:?>

                        <?php if (isset($editId)): // редактирование?>

                            <?php $item = $data['items'][0];?>
                            <form method="POST" action="/admin/actions/<?=$editCategory?>/edit" id="formData">
                                <label class="item">
                                    <span>Вопрос<span class="error">*</span></span>
                                    <input type="text" name="question" require class="field" maxlength="255" value="<?=$item['question']?>">
                                </label>
                                <label class="item">
                                    <span>Ответ<span class="error">*</span></span>
                                    <input type="text" name="answer" require class="field" maxlength="255" value="<?=$item['answer']?>">
                                </label>
                                <input type="hidden" name="id" value="<?=$item['id']?>">
                                <div class="item flex-row flex-wrap justify-content-between gap-20">
                                    <input type="submit" class="button w-100" value="Сохранить">
                                    <a href="/admin/<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                                    <input type="button" class="button error delete w-100" value="Удалить">
                                </div>
                            </form>

                        <?php else: // вывод всех элементов?>
                        
                            <form id="form-search">
                                <div class="search">
                                    <input type="text" name="search" placeholder="Поиск вопроса" value="<?=$search?>">
                                </div>
                            </form>

                            <a class="button shadowBox" href="/admin/<?=$editCategory?>?action=add">Добавить вопрос</a>

                            <?php if(count($data) > 0):?>

                                <div class="item-list">

                                    <?php foreach($data as $key => $item):
                                        $item['id'] = (int) $item['id'];
                                        $item['question'] = trim($item['question']);?>

                                        <a class="item shadowBox" href="/admin/<?=$editCategory?>?action=edit&id=<?=$item['id']?>">
                                            <div class="item-content d-flex flex-column">
                                                <span class="d-flex flex-row flex-wrap gap-5 fw-bold"><?=$item['question']?></span>
                                                <span class="d-flex flex-row flex-wrap gap-5"><?=$item['answer']?></span>
                                            </div>
                                        </a>

                                    <?php endforeach;?>

                                </div>

                            <?php else:?>
                                
                                <p class="text-center">Не найдено ни одного вопроса</p>
                                
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
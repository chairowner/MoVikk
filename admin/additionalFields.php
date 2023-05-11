<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Дополнительные поля";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_USER = new User($conn);

if (!$_USER->isAdmin()) {
    $_PAGE->Redirect();
}

// редактируемая категория
$editCategory = explode('/',$_SERVER['PHP_SELF']);
$editCategory = basename($editCategory[count($editCategory) - 1], '.php');

if (isset($editId)) {
    $data = $conn->prepare("SELECT `id`, `title`, `value` FROM `additional_fields` WHERE `id` = :id");
    $data->execute(['id' => $editId]);
    $data = $data->fetch();
    if (!isset($data)) $_PAGE->Redirect(ADMIN_URL."/$editCategory");
} else {
    $data = $conn->prepare("SELECT `id`, `title` FROM `additional_fields`");
    $data->execute();
    $data = $data->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), "{$_PAGE->title} - {$_PAGE->description}", $_PAGE->description)?>
    <link rel="stylesheet" href="assets/css/main.css">
    <script defer src="/assets/common/js/disableForm.js"></script>
    <script defer src="/assets/common/js/showLoad.js"></script>
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

                    <?php // редактирование
                        if (isset($editId)):
                            $item = $data;
                            $item['id'] = (int)$item['id'];
                            $item['title'] = trim($item['title']);
                            $item['value'] = isset($item['value']) && trim($item['value']) !== "" ? trim($item['value']) : null;?>

                        <form method="POST" action="actions/<?=$editCategory?>/edit" id="formData">
                            <label class="item">
                                <h2><?=$item['title']?></h2>
                            </label>
                            <label class="item">
                                <span>Текст</span>
                                <textarea name="value" class="field"><?=isset($item['value'])?$item['value']:null?></textarea>
                            </label>
                            <input type="hidden" name="id" value="<?=$item['id']?>">
                            <div class="item flex-row flex-wrap justify-content-between gap-20">
                                <input type="submit" class="button w-100" value="Сохранить">
                                <a href="<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                            </div>
                        </form>

                    <?php else: // вывод всех элементов?>

                        <?php if(count($data) > 0):?>

                            <div class="item-list">

                                <?php foreach($data as $key => $item):
                                    $item['id'] = (int) $item['id'];
                                    $item['title'] = trim($item['title']);?>

                                    <a class="item shadowBox" href="<?=$editCategory?>?action=edit&id=<?=$item['id']?>">
                                        <div class="item-content d-flex flex-column">
                                            <span class="d-flex flex-row flex-wrap gap-5 fw-bold"><?=$item['title']?></span>
                                        </div>
                                    </a>

                                <?php endforeach;?>

                            </div>

                        <?php else:?>
                            
                            <p class="text-center">Не найдено ни одного дополнительного поля</p>
                            
                        <?php endif;?>

                    <?php endif;?>

                </div>
            </div>
        </section>
    </main>
    <div id="mainMessageBox"></div>
</body>
</html>
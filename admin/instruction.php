<?php
set_include_path("../");
require_once('includes/autoload.php');
require_once('./includes/includes.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Инструкции";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_INSTRUCTIONS = new Instruction($conn);
$_USER = new User($conn);

if (!$_USER->isAdmin()) {
    $_PAGE->Redirect();
}

// редактируемая категория
$editCategory = explode('/',$_SERVER['PHP_SELF']);
$editCategory = basename($editCategory[count($editCategory) - 1], '.php');

if (isset($editId)) {
    $data = $_INSTRUCTIONS->Get($editId);
    if (!isset($data['items'][0])) $_PAGE->Redirect("admin/$editCategory");
} else {
    $data = $_INSTRUCTIONS->Get("all");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), "{$_PAGE->title} - {$_PAGE->description}", $_PAGE->description)?>
    <link rel="stylesheet" href="assets/css/main.css">
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
                                <span>Название<span class="error">*</span></span>
                                <input type="text" name="name" require class="field" maxlength="255">
                            </label>
                            <label class="item">
                                <span>Текст</span>
                                <input type="text" name="text" class="field" maxlength="255">
                            </label>
                            <label class="item">
                                <span>Видео</span>
                                <input type="file" name="video" accept=".wmv,.mp4,.avi,.webm,.mov,.mkv">
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
                                    <span>Название<span class="error">*</span></span>
                                    <input type="text" name="name" require class="field" maxlength="255" value="<?=$item['name']?>">
                                </label>
                                <label class="item">
                                    <span>Текст</span>
                                    <input type="text" name="text" class="field" maxlength="255" value="<?=$item['text']?>">
                                </label>
                                <label class="item">
                                    <span>Видео (need add)</span>
                                </label>
                                <input type="hidden" name="id" value="<?=$item['id']?>">
                                <div class="item flex-row flex-wrap justify-content-between gap-20">
                                    <input type="submit" class="button w-100" value="Сохранить">
                                    <a href="/admin/<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                                    <input type="button" class="button error delete w-100" value="Удалить">
                                </div>
                            </form>

                        <?php else: // вывод всех элементов?>
                        
                            <div id="items">
                                <a class="button shadowBox" href="/admin/<?=$editCategory?>?action=add">Добавить вопрос</a>
                                <?php if (isset($data['items']) && !empty($data['items'])):?>
                                    <?php foreach($data['items'] as $key => $item):?>
                                        <a class="item shadowBox" href="/admin/<?=$editCategory?>?action=edit&id=<?=$item['id']?>">
                                            <span class="title"><?=$item['name']?></span>
                                        </a>
                                    <?php endforeach;?>
                                <?php else:?>
                                    <p>Ещё не добавлено ни одного вопроса</p>
                                <?php endif;?>
                            </div>

                        <?php endif;?>

                    <?php endif;?>

                </div>
            </div>
        </section>
    </main>
    <div id="mainMessageBox"></div>
</body>
</html>
<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Данные о компании";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_USER = new User($conn);

if (!$_USER->isAdmin()) {
    $_PAGE->Redirect();
}

// редактируемая категория
$editCategory = explode('/',$_SERVER['PHP_SELF']);
$editCategory = basename($editCategory[count($editCategory) - 1], '.php');

$data = $conn->prepare("SELECT * FROM `{$_COMPANY->GetTable()}`");
$data->execute();
$data = $data->fetch();
if (!isset($data)) $_PAGE->Redirect(ADMIN_URL."/$editCategory");
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
                    <?php
                    
                    ?>

                    <form method="POST" action="actions/<?=$editCategory?>/edit" id="formData">
                        <label class="item">
                            <span>Наименование компании</span>
                            <input name="name" placeholder="Наименование компании" class="field" value="<?=isset($data['name'])?$data['name']:null?>">
                        </label>
                        <label class="item">
                            <span>Контактный номер телефона</span>
                            <input name="phone" placeholder="Контактный номер телефона" class="field" value="<?=isset($data['phone'])?$data['phone']:null?>">
                        </label>
                        <label class="item">
                            <span>ИНН</span>
                            <input name="inn" placeholder="ИНН" class="field" value="<?=isset($data['inn'])?$data['inn']:null?>">
                        </label>
                        <label class="item">
                            <span>ОГРН</span>
                            <input name="ogrn" placeholder="ОГРН" class="field" value="<?=isset($data['ogrn'])?$data['ogrn']:null?>">
                        </label>
                        <label class="item">
                            <span>Номер счёта</span>
                            <input name="pay_acc" placeholder="Номер счёта" class="field" value="<?=isset($data['pay_acc'])?$data['pay_acc']:null?>">
                        </label>
                        <label class="item">
                            <span>БИК</span>
                            <input name="bik" placeholder="БИК" class="field" value="<?=isset($data['bik'])?$data['bik']:null?>">
                        </label>
                        <label class="item">
                            <span>КС</span>
                            <input name="ks" placeholder="КС" class="field" value="<?=isset($data['ks'])?$data['ks']:null?>">
                        </label>
                        <label class="item">
                            <span>Адрес</span>
                            <input name="place" placeholder="Адрес" class="field" value="<?=isset($data['place'])?$data['place']:null?>">
                        </label>
                        <div class="item flex-row flex-wrap justify-content-between gap-20">
                            <input type="submit" class="button w-100" value="Сохранить">
                            <a href="<?=$editCategory?>" class="button secondary w-100">Отмена</a>
                        </div>
                    </form>

                </div>
            </div>
        </section>
    </main>
    <div id="mainMessageBox"></div>
</body>
</html>
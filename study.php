<?php
$thisUrl = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
set_include_path(".");
require_once('functions/formSQL.php');
require_once('functions/numWord.php');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

$sqlSelect = ['i.id iId','i.name iName','i.video iVideo','i.text iText','p.id pId','p.name pName'];
$sqlFrom = ['instructions i'];
$sqlJoins = ["LEFT JOIN products p ON i.id = p.instructionId"];

$instructions = formSQL($conn, [$sqlSelect, $sqlFrom, null, $sqlJoins]);

$instruction = null;
if (isset($_GET['id'])) {
    $instruction = (int) $_GET['id'];
    if ($instruction > 0)  {
        $sqlSelect = ['i.id iId','i.name iName','i.video iVideo','i.text iText'];
        $sqlFrom = ['instructions i'];
        $sqlWhere[] = "i.id = '{$instruction}'";
        
        $instruction = formSQL($conn, [$sqlSelect, $sqlFrom, $sqlWhere, $sqlJoins], 'one');
    }
}
if (isset($instruction['iName'])) $instruction['iName'] = trim($instruction['iName']);
$isOpen = isset($instruction) && is_array($instruction) ? true : false;
$title = $isOpen ? $instruction['iName'] : null;
$main_header = "Обучения по пользованию аппаратами";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $title)?>
    <link rel="stylesheet" href="/assets/common/css/study.css">
    <script defer src="/assets/common/js/copyToBuffer.js"></script>
    <script defer src="/assets/common/js/study.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container d-flex align-items-center gap-10"><?=$isOpen ? "{$instruction['iName']} <img class=\"copy-href\" data-href=\"$thisUrl\" src=\"/assets/icons/copy-link.svg\" title=\"Скопировать ссылку\">" : $main_header?></h1>
    </div>
    <div class="content-wrapper">
        <?php if($isOpen):?>
            <main>
                <section class="m-0">
                    <div class="container">
                        <div class="instruction">
                            <?php if(isset($instruction['iText'])):$instruction['iText'] = trim($instruction['iText']);?><p class="item instruction-text"><?=$instruction['iText']?></p><?php endif;?>
                            <?php if(isset($instruction['iVideo'])):$instruction['iVideo'] = trim($instruction['iVideo']);?><video controls class="item instruction-video">
                                <source src="/assets/videos/instructions/<?=$instruction['iVideo']?>" type="video/mp4">
                                <p>К сожалению, ваш браузер не поддерживает данный формат видео.</p>
                            </video><?php endif;?>
                        </div>
                    </div>
                </section>
            </main>
        <?php endif;?>
        <?php if(!$isOpen):?><main><?php endif;?>
            <section class="m-0">
                <div class="container">
                    <div>
                        <?php
                        if ($isOpen) echo("<h2>$main_header</h2>");
                        if (count($instructions) > 0):?>
                            <div class="item-list">
                                <?php foreach($instructions as $key => $item):?>
                                    <a href="/study/<?=$item['iId']?>" class="item shadowBox"><?php $instructionName = "Обучение #".(int)$item['iId'];
                                        if (isset($item['pName'])) $instructionName = trim($item['pName']);
                                        else if (isset($item['iName'])) $instructionName = trim($item['iName']);
                                        echo($instructionName);?></a>
                                <?php endforeach;?>
                            </div>
                        <?php else:?>
                            <span>Пока нет доступных обучений</span>
                        <?php endif;?>
                    </div>
                </div>
            </section>
        <?php if(!$isOpen):?></main><?php endif;?>
    </div>
    <?php include_once('includes/footer.php')?>
</body>
</html>
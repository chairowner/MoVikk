<?php require_once('includes/autoload.php');?>
<?php require_once('functions/numWord.php');?>
<?php
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
$isOpen = isset($instruction) && is_array($instruction) ? true : false;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$isOpen ? $_PAGE->getHead(trim($instruction['iName'])) : $_PAGE->getHead()?>
    <link rel="stylesheet" href="/assets/css/study.css">
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$isOpen ? trim($instruction['iName']) : "Обучения по пользованию аппаратами"?></h1>
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
                                <p>Свяжитесь с нами и мы предоставим вам доступ к инструкции.</p>
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
                        if ($isOpen) echo('<h2>Обучения по пользованию аппаратами</h2>');
                        if (count($instructions) > 0):?>
                            <div class="study">
                                <?php foreach($instructions as $key => $item):?>
                                    <a href="/study/<?=$item['iId']?>" class="item shadowBox"> <?php $instructionName = "Обучение #".(int)$item['iId'];
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
    <?php include_once('includes/scripts.php')?>
    <script src="/assets/js/study.js"></script>
</body>
</html>
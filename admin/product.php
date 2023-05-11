<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Товары";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_CATEGORIES = new Categories($conn);
$_PRODUCTS = new Product($conn);
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

$data = $categories = [];

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
    $categories = $_CATEGORIES->Get("all");
    $data = $_PRODUCTS->getProduct($editId,"",true);
    if (!isset($data)||$data['notFound']) $_PAGE->Redirect(ADMIN_URL."/$editCategory");
} else {
    if ($action !== "create") {
        $execute = [];
        $categories = $_CATEGORIES->Get("all");
        if (isset($_GET['search']) && trim($_GET['search']) !== "") {
            $search = trim($_GET['search']);
            $data = "SELECT `c`.`name` `categoryName`,`p`.`name`,`p`.`id`,`p`.`description`,`p`.`price`,`p`.`sale`,`p`.`isDeleted` FROM `{$_PRODUCTS->GetTable()}` `p` INNER JOIN `{$_CATEGORIES->GetTable()}` `c` ON `c`.`id` = `p`.`categoryId` WHERE `p`.`name` LIKE :search LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
            $execute['search'] = "%$search%";
        } else {
            $data = "SELECT `c`.`name` `categoryName`,`p`.`name`,`p`.`id`,`p`.`description`,`p`.`price`,`p`.`sale`,`p`.`isDeleted` FROM `{$_PRODUCTS->GetTable()}` `p` INNER JOIN `{$_CATEGORIES->GetTable()}` `c` ON `c`.`id` = `p`.`categoryId` LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;
        }
        $data = $conn->prepare($data);
        $data->execute($execute);
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($editId) && count($data) < 1) $_PAGE->Redirect(ADMIN_URL."/$editCategory");
elseif (($currentPageNumber !== 1) && count($data) < 1) $_PAGE->Redirect(ADMIN_URL."/$editCategory");

if (isset($editId) ) {
    $pattern .= "/".ADMIN_URL."/$editCategory?action=edit&id=#";
} elseif ($action === "add") {
    $pattern .= "/".ADMIN_URL."/$editCategory?action=add";
}else {
    $all_count_execute = [];
    if (isset($search)) {
        $pattern .= "/".ADMIN_URL."/$editCategory?search=".addslashes(htmlspecialchars(trim($search)))."&page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_PRODUCTS->GetTable()}` WHERE `name` LIKE :search";
        $all_count_execute = ['search' => "%$search%"];
    } else {
        $pattern .= "/".ADMIN_URL."/$editCategory?page=#";
        $all_count = "SELECT COUNT(`id`) `counter` FROM `{$_PRODUCTS->GetTable()}`";
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

function getArray(PDO $conn, string $table) {
    $items = $conn->prepare("SELECT * FROM `$table`");
    $items->execute();
    return $items->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?= $_PAGE->GetHead($_USER->isGuest(), $_PAGE->title . " - " . $_PAGE->description, $_PAGE->description) ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="/classes/php-pagination/css/main.css">
    <link rel="stylesheet" href="assets/css/<?= $editCategory ?>.css">
    <script defer src="/assets/common/js/disableForm.js"></script>
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="assets/js/actions.js"></script>
    <script defer src="assets/js/<?= $editCategory ?>/<?= $editCategory ?>.js"></script>
</head>

<body>
    <div class="page__title">
        <div class="container">
            <a href="/"><img src="/assets/icons/logo.svg" class="logo" alt="<?= $_COMPANY->name ?>"></a>
            <h1 class="container"><a href="/<?=ADMIN_URL?>"><?= $_PAGE->title ?></a> - <a href="<?= $editCategory ?>"><?= $_PAGE->description ?></a></h1>
        </div>
    </div>
    <main style="min-height: 35vh;">
        <input type="hidden" data-editCategory="<?= $editCategory ?>">
        <section class="m-0">
            <div class="container">
                <div id="main">

                    <?php if ($action === "add" || $action === "edit") : /* создание */ ?>
                        
                        <?php if (count($categories['items']) > 0) : /* если есть категории */ ?>
                            
                            <?php if($action === "edit"):?>
                                <div class="text-center">
                                    <a style="max-width: 600px; margin: 0 auto;" target="_blank" class="button" href="/product/<?=trim($data['href'])?>-<?=$data['id']?>">Открыть страницу товара</a>
                                </div>
                            <?php endif;?>

                            <form method="POST" enctype="multipart/form-data" redirectToCategory="false" action="actions/<?= $editCategory ?>/add" id="formData">
                                <label class="item">
                                    <span>Категория<span class="error">*</span></span>
                                    <select name="categoryId" required class="field">
                                        <option value="">Не указана</option>
                                        <?php foreach ($categories['items'] as $key => $item) : $item['id'] = (int)$item['id'];?>
                                            <option value="<?= $item['id'] ?>"
                                                <?=(isset($data['categoryId'])&&($item['id']===$data['categoryId']))?' selected':null?>
                                            ><?= $item['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label class="item">
                                    <span>Страна</span>
                                    <select name="countryId" class="field">
                                        <option value="">Не указана</option>
                                        <?php $items = getArray($conn, "countries");
                                        foreach ($items as $key => $item) : $item['id'] = (int)$item['id'];?>
                                            <option value="<?= $item['id'] ?>"
                                                <?=(isset($data['countryId'])&&($item['id']===$data['countryId']))?' selected':null?>
                                            ><?= $item['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label class="item">
                                    <span>Инструкция</span>
                                    <select name="instructionId" class="field">
                                        <option value="">Не указана</option>
                                        <?php $items = getArray($conn, "instructions");
                                        foreach ($items as $key => $item) : $item['id'] = (int)$item['id']; ?>
                                            <option value="<?= $item['id'] ?>"
                                                <?=(isset($data['instructionId'])&&($item['id']===$data['instructionId']))?' selected':null?>
                                            ><?= $item['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                                <label class="item">
                                    <span>Название<span class="error">*</span></span>
                                    <input type="text" name="name" required class="field" maxlength="255" value="<?=isset($data['name'])?trim($data['name']):null?>">
                                </label>
                                <label class="item">
                                    <span>Описание<span class="error">*</span></span>
                                    <textarea name="description" class="field"><?=isset($data['description'])?trim($data['description']):null?></textarea>
                                </label>
                                <label class="item">
                                    <span>Цена<span class="error">*</span></span>
                                    <input type="number" min="1" step="0.01" name="price" required class="field" value="<?=isset($data['price'])?(float)$data['price']:null?>">
                                </label>
                                <label class="item">
                                    <span>Преймущества (разделять знаком ";")</span>
                                    <input type="text" name="features" class="field" value="<?php
                                        if(isset($data['features'])){
                                            echo(is_array($data['features'])?implode(';',$data['features']):null);
                                        } else {
                                            echo(null);
                                        }
                                    ?>">
                                </label>
                                <label class="item">
                                    <span>Технические характеристики (разделять знаком ";")<br>[в формате: "<em>Характеристика:Значение</em>"]<br>[пример: "<em>Характеристика1:Значение1;Характеристика2:Значение2</em>"]</span>
                                    <input type="text" name="techSpec" class="field" value="<?php
                                        if(isset($data['techSpec']) && is_array($data['techSpec']) && !empty($data['techSpec'])){
                                            $data_techSpec_count = count($data['techSpec']);
                                            if ($data_techSpec_count > 1) {
                                                for ($i = 0; $i < $data_techSpec_count; $i++) {
                                                    if ($i > 0) echo(";"); 
                                                    echo("{$data['techSpec'][$i]['name']}:{$data['techSpec'][$i]['value']}");
                                                }
                                            } elseif($data_techSpec_count === 1) {
                                                echo("{$data['techSpec'][0]['name']}:{$data['techSpec'][0]['value']}");
                                            } else {
                                                echo(null);
                                            }
                                        } else {
                                            echo(null);
                                        }
                                    ?>">
                                </label>
                                <label class="item">
                                    <span>Высота</span>
                                    <input type="number" placeholder="0" min="0" step="0.01" name="height" class="field" value="<?=isset($data['height'])?(float)$data['height']:null?>">
                                </label>
                                <label class="item">
                                    <span>Ширина</span>
                                    <input type="number" placeholder="0" min="0" step="0.01" name="width" class="field" value="<?=isset($data['width'])?(float)$data['width']:null?>">
                                </label>
                                <label class="item">
                                    <span>Длина</span>
                                    <input type="number" placeholder="0" min="0" step="0.01" name="length" class="field" value="<?=isset($data['length'])?(float)$data['length']:null?>">
                                </label>
                                <label class="item">
                                    <span>Скидка</span>
                                    <input type="number" placeholder="0" min="0" step="1" name="sale" class="field" value="<?=isset($data['sale'])?(int)$data['sale']:null?>">
                                </label>
                                <label class="item">
                                    <span>Изображения</span>
                                    <input type="file" name="files[]" multiple accept="image/*,image/jpeg">
                                </label>
                                <?php if($action === 'edit'):?>
                                    <input type="hidden" name="id" value="<?=$editId?>">
                                    <div class="item">
                                        <div id="productImages">
                                            <?php if(isset($data['images'])):?>
                                                <?php if(isset($data['images']['main'])&&(isset($data['images']['main']['src'])&&$data['images']['main']['src']!=="")):?>
                                                    <div class="productImage">
                                                        <div class="productImage-action">
                                                            <span class="item success" data-imageId="<?=$data['images']['main']['id']?>" title="Установленно как основное изображение">✔</span>
                                                            <span class="item error productImage_close" data-imageId="<?=$data['images']['main']['id']?>" title="Удалить">✖</span>
                                                        </div>
                                                        <img src="<?=$data['images']['main']['src']?>" alt="<?=isset($data['name'])?trim($data['name']):null?>">
                                                    </div>
                                                <?php endif;?>
                                                <?php foreach($data['images']['additional'] as $key => $image): $image['src'] = trim($image['src']);?>
                                                    <?php if($image['src']!==""):?>
                                                        <div class="productImage">
                                                            <div class="productImage-action">
                                                                <?php if(file_exists(get_include_path().'assets/icons/home.svg')):?>
                                                                    <span class="item success productImage_setMain" data-imageId="<?=$image['id']?>">
                                                                        <img src="/assets/icons/home.svg" alt="Установить" width="20" title="Сделать основным изображением">
                                                                    </span>
                                                                <?php else:?>
                                                                    <span class="productImage_setMain" data-imageId="<?=$data['images']['additional']['id']?>">Установить</span>
                                                                <?php endif;?>
                                                                <span class="item error productImage_close" data-imageId="<?=$image['id']?>" title="Удалить">✖</span>
                                                            </div>
                                                            <img src="<?=$image['src']?>" alt="<?=isset($data['name'])?trim($data['name']):null?>">
                                                        </div>
                                                    <?php endif;?>
                                                <?php endforeach;?>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                <?php endif;?>

                                <input type="hidden" name="action" value="<?=$action?>">

                                <div class="item flex-row flex-wrap justify-content-between gap-20">
                                    <?php if($action === 'edit'):?>
                                        <input type="submit" class="button w-100" value="Сохранить">
                                        <a href="<?= $editCategory ?>" class="button secondary w-100">Отмена</a>
                                        <input type="button" class="button error delete w-100" value="Удалить">
                                    <?php elseif($action === 'add'):?>
                                        <input type="submit" class="button w-100" value="Добавить">
                                        <a href="<?= $editCategory ?>" class="button secondary w-100">Отмена</a>
                                    <?php endif;?>
                                </div>
                            </form>

                        <?php else : ?>

                            <div class="d-flex flex-column justify-content-center align-items-center gap-10">
                                <span>Сначала добавьте категорию</span>
                                <a href="category?action=add" class="button">Добавить категорию</a>
                            </div>

                        <?php endif; ?>

                    <?php else : ?>
                            
                        <form id="form-search">
                            <div class="search">
                                <input type="text" name="search" placeholder="Поиск товара" value="<?=$search?>">
                            </div>
                        </form>

                        <a class="button shadowBox" href="<?= $editCategory ?>?action=add">Добавить товар</a>

                        <?php if (count($data) > 0) : ?>

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

                        <?php else : ?>
                            
                            <p class="text-center">Не занесено ни одного товара</p>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>
        </section>
    </main>
    <?php if (isset($pagination)):?><div class="container"><?=$pagination->Render()?></div><?php endif;?>
    <div id="mainMessageBox"></div>
</body>
</html>
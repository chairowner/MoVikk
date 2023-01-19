<?php
set_include_path("../");
require_once('includes/autoload.php');
require_once('./includes/includes.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Товары";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_CATEGORIES = new Categories($conn);
$_PRODUCTS;
$_USER = new User($conn);

if (!$_USER->isAdmin()) {
    $_PAGE->redirect();
}

$data = $categories = [];

// редактируемая категория
$editCategory = explode('/',$_SERVER['PHP_SELF']);
$editCategory = basename($editCategory[count($editCategory) - 1], '.php');

if (isset($editId)) {
    $categories = $_CATEGORIES->get("all");
    $_PRODUCTS = new Product($conn);
    $data = $_PRODUCTS->getProduct($editId,"",true);
    if (!isset($data)||$data['notFound']) $_PAGE->redirect("admin/$editCategory");
} else {
    if ($action !== "create") {
        $categories = $_CATEGORIES->get("all");
        $prepare = "SELECT `c`.`name` `categoryName`,`p`.`name`,`p`.`id`,`p`.`description`,`p`.`count`,`p`.`price`,`p`.`sale`,`p`.`isDeleted` FROM `products` `p` INNER JOIN `categories` `c` ON `c`.`id` = `p`.`categoryId`";
        $data = $conn->prepare($prepare);
        $data->execute();
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
    }
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
    <?= $_PAGE->getHead($_USER->isGuest(), $_PAGE->title . " - " . $_PAGE->description, $_PAGE->description) ?>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/<?= $editCategory ?>.css">
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="assets/js/actions.js"></script>
    <script>console.log(<?=json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>)</script>
</head>

<body>
    <div class="page__title">
        <div class="container">
            <a href="/"><img src="/assets/icons/logo.svg" class="logo" alt="<?= $_COMPANY->name ?>"></a>
            <h1 class="container"><a href="/admin"><?= $_PAGE->title ?></a> - <a href="/admin/<?= $editCategory ?>"><?= $_PAGE->description ?></a></h1>
        </div>
    </div>
    <main style="min-height: 35vh;">
        <input type="hidden" data-editCategory="<?= $editCategory ?>">
        <section class="m-0">
            <div class="container">
                <div id="main">

                    <?php if ($action === "add" || $action === "edit") : /* создание */ ?>
                        
                        <?php if (count($categories['items']) > 0) : /* если есть категории */ ?>

                            <form method="POST" enctype="multipart/form-data" action="/admin/actions/<?= $editCategory ?>/add" id="formData">
                                <label class="item">
                                    <span>Категория<span class="error">*</span></span>
                                    <select name="categoryId" require class="field">
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
                                    <input type="text" name="name" require class="field" maxlength="255" value="<?=isset($data['name'])?trim($data['name']):null?>">
                                </label>
                                <label class="item">
                                    <span>Описание<span class="error">*</span></span>
                                    <textarea name="description" class="field" maxlength="255"><?=isset($data['description'])?trim($data['description']):null?></textarea>
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
                                    <span>Цена<span class="error">*</span></span>
                                    <input type="number" min="1" step="0.01" name="price" require class="field" value="<?=isset($data['price'])?(float)$data['price']:null?>">
                                </label>
                                <label class="item">
                                    <span>Скидка</span>
                                    <input type="number" placeholder="0" min="0" step="1" name="sale" class="field" value="<?=isset($data['sale'])?(int)$data['sale']:null?>">
                                </label>
                                <label class="item">
                                    <span>Количество</span>
                                    <input type="number" placeholder="0" min="0" step="1" name="count" class="field" value="<?=isset($data['count'])?(int)$data['count']:null?>">
                                </label>
                                <label class="item">
                                    <?php if($action === 'edit'):?>
                                        <input type="hidden" name="id" value="<?=$editId?>">
                                        <div id="productImages">
                                            <?php if(isset($data['images'])):?>
                                                <?php if(isset($data['images']['main'])&&$data['images']['main']!==""):?>
                                                    <div class="productImage">
                                                        <span class="productImage_close error">X</span>
                                                        <img src="<?=$data['images']['main']?>" alt="<?=isset($data['name'])?trim($data['name']):null?>">
                                                    </div>
                                                <?php endif;?>
                                                <?php foreach($data['images']['additional'] as $key => $image): $image = trim($image);?>
                                                    <?php if(isset($image)&&$image!==""?$image:null):?>
                                                        <div class="productImage">
                                                            <span class="productImage_close error">X</span>
                                                            <img src="<?=$image?>" alt="<?=isset($data['name'])?trim($data['name']):null?>">
                                                        </div>
                                                    <?php endif;?>
                                                <?php endforeach;?>
                                            <?php endif;?>
                                        </div>
                                    <?php endif;?>
                                    <span>Изображения</span>
                                    <input type="file" name="files[]" multiple accept="image/*,image/jpeg">
                                </label>

                                <input type="hidden" name="action" value="<?=$action?>">

                                <div class="item flex-row flex-wrap justify-content-between gap-20">
                                    <?php if($action === 'edit'):?>
                                        <input type="submit" class="button w-100" value="Сохранить">
                                        <a href="/admin/<?= $editCategory ?>" class="button secondary w-100">Отмена</a>
                                        <input type="button" class="button error delete w-100" value="Удалить">
                                    <?php elseif($action === 'add'):?>
                                        <input type="submit" class="button w-100" value="Добавить">
                                        <a href="/admin/<?= $editCategory ?>" class="button secondary w-100">Отмена</a>
                                    <?php endif;?>
                                </div>
                            </form>

                        <?php else : ?>

                            <div class="d-flex flex-column justify-content-center align-items-center gap-10">
                                <span>Сначала добавьте категорию</span>
                                <a href="/admin/category?action=add" class="button">Добавить категорию</a>
                            </div>

                        <?php endif; ?>

                    <?php else : ?>

                        <div id="items">
                            <a class="button shadowBox" href="/admin/<?= $editCategory ?>?action=add">Добавить товар</a>
                            <?php if (count($data) > 0) : ?>
                                <?php foreach ($data as $key => $product) : ?>
                                    <a class="item shadowBox" href="/admin/<?= $editCategory ?>?action=edit&id=<?= $product['id'] ?>">
                                        <span class="title"><?= $product['name'] ?></span>
                                    </a>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="text-center">Не занесено ни одного товара</p>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </section>
    </main>
    <div id="mainMessageBox"></div>
</body>
</html>
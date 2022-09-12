<?php require_once('includes/autoload.php');?>
<?php require_once('functions/numWord.php');?>
<?php
$categoryHref = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$sortArr = [
    'popular' => 'По популярности',
    'new' => 'По новизне',
    'lowPrice' => 'Сначала дешёвые',
    'highPrice' => 'Сначала дорогие',
];
$sort = isset($_GET['sort']) && key_exists(trim($_GET['sort']), $sortArr) ? trim($_GET['sort']) : null;

$categories = $conn->prepare("SELECT * FROM categories");
$categories->execute();
$categories = $categories->fetchAll(PDO::FETCH_ASSOC);
$sql =
"SELECT DISTINCT p.id, p.categoryId, p.href, p.name, p.description, p.height, p.width, p.length, p.features, p.techSpec, p.countryId, p.quantity, p.price, (p.price - (p.price * p.sale / 100)) discounted, p.preOrder, p.keywords, p.sold, p.added, p.isDeleted";
if ($categoryHref !== 'all') $sql .=", c.href cHref";
$sql .= " FROM products p";
if ($categoryHref !== 'all') $sql .=" INNER JOIN categories c ON p.categoryId = c.id";
$sql .= " WHERE";
if ($categoryHref !== 'all') $sql .= " c.href = :href AND";
$sql .= " p.price >= 1 AND p.isDeleted = 0 AND ((p.quantity > 0) OR (p.quantity = 0 AND p.preOrder = 1))";
if (isset($sort)) {
    $sql .= " ORDER BY ";
    if ($sort === 'popular') {
        $sql .= "p.sold DESC";
    } else if ($sort === 'new') {
        $sql .= "p.added DESC";
    } else if ($sort === 'lowPrice') {
        $sql .= "discounted ASC";
    } else if ($sort === 'highPrice') {
        $sql .= "discounted DESC ";
    }
}
$sql .= " LIMIT ".($page - 1).", 30";

$products = $conn->prepare($sql);
if ($categoryHref !== 'all') {
    $products->execute(['href' => $categoryHref]);
} else {
    $products->execute();
}
$products = $products->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->getHead()?>
    <link rel="stylesheet" href="/assets/css/shop.css">
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <section class="common-block">
        <div class="container main">
            <div class="side-block custom-scroll">
                <div class="categoryMenu">
                    <?php foreach($categories as $key => $category):
                        $category['href'] = trim($category['href']);?>
                        <a class="item<?=$categoryHref === $category['href'] ? " active" : null?>" href="/shop/<?=$category['href']?>"><?=$category['name']?></a>
                    <?php endforeach;?>
                </div>
                <div class="sortMenu">
                    <strong class="sort-title user-select-none">Сортировка</strong>
                    <form method="GET" id="sort">
                        <input type="hidden" id="categoryUrl" value="<?=$categoryHref?>">
                        <div class="sortPrice">
                            <p>sort price</p>
                        </div>
                        <?php foreach($sortArr as $value => $title):?>
                        <div>
                            <input class="radioInp" type="radio" id="sort-<?=$value?>" name="sort" value="<?=$value?>" <?=isset($sort) && $value==$sort?' checked':null?> >
                            <label class="user-select-none" for="sort-<?=$value?>"><?=$title?></label>
                        </div>
                        <?php endforeach;?>
                        <input type="submit" class="button w-100" value="Применить">
                        <?php if($categoryHref !== 'all' || isset($sort)):?>
                            <input type="button" id="reset" class="button w-100" value="Сбросить">
                        <?php endif;?>
                    </form>
                </div>
            </div>
            <main class="product-cards">
                <?php
                $index = 0;
                if (count($products) > 0):
                foreach($products as $key => $product):
                    $index++;
                    if ($index === 31): break;
                    else:
                        $noImage = false;
                        $product['image'] = $conn->prepare( "SELECT `image` FROM products_images WHERE id = :id AND isMain = 1 LIMIT 1");
                        $product['image']->execute(['id' => (int) $product['id']]);
                        $product['image'] = $product['image']->fetch(PDO::FETCH_ASSOC);
                        if (isset($product['image']['image'])) $product['image'] = "/assets/images/products/".trim($product['image']['image']);
                        else {
                            $noImage = true;
                            $product['image'] = '/assets/icons/camera.svg';
                        }
                    ?>
                    <?php if(false && ($index / 4) === 0.25):?><div class="product-cards-row"><?php endif;?>
                    <div class="product-card shadowBox">
                        <div class="product-card-image">
                            <div class="product-card-image_block">
                                <?php if($noImage):?>
                                    <img src="<?=$product['image']?>" class="noPhoto" alt="<?=$product['name']?>">
                                <?php else:?>
                                    <img src="<?=$product['image']?>" alt="<?=$product['name']?>">
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="product-card-body">
                            <p class="product-card-title"><?=$product['name']?></p>
                            <div class="product-card-text">
                                <p><?=isset($product['description']) ? trim(substr($product['description'], 0, 300)) : '<i class="text-none">Описание отсутствует</i>'?></p>
                            </div>
                            <div class="product-card-buy">
                                <p class="product-card-price">
                                    <?php
                                    // есть ли скидка
                                    $product['price'] = doubleval($product['price']);
                                    $product['discounted'] = doubleval($product['discounted']);
                                    if($product['discounted'] === $product['price']):?>
                                        <strong class="product-card-price-main"><?=formatPrice($product['price'])?></strong>
                                    <?php else:?>
                                        <strong class="product-card-price-main"><?=formatPrice($product['discounted'])?></strong>
                                        <span class="product-card-price-old"><?=formatPrice($product['price'])?></span>
                                    <?php endif;?>
                                </p>
                                <a href="/product/<?="{$product['href']}-{$product['id']}"?>" class="button elems">
                                    <span>Подробнее</span>
                                    <i class="fa-solid fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php if(false && ($index / 4) === 1): $index = 0?></div><?php endif;?>
                <?php endif; endforeach; else:?>
                    <p class="w-100 text-center" style="font-size:20px;">К сожалению, в этой категории пока нет доступных товаров</p>
                <?php endif;?>
            </main>
        </div>
    </section>
    <?php include_once('includes/footer.php')?>
    <?php include_once('includes/scripts.php')?>
    <script src="/assets/js/shop.js"></script>
</body>
</html>
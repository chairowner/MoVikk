<?php
set_include_path(".");
require_once('includes/autoload.php');
require_once('functions/formatPrice.php');
require_once('functions/numWord.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

require_once('classes/php-pagination/Pagination.php');

$viewCount = 29;
$_GET['category'] = $categoryHref = isset($_GET['category']) ? trim($_GET['category']) : 'all';
if (isset($_GET['page'])) {
    $_GET['page'] = (int) $_GET['page'];
    if ($_GET['page'] > 0) {
        $currentPageNumber = $_GET['page'];
    } else {
        $_GET['page'] = $currentPageNumber = 1;
    }
} else {
    $_GET['page'] = $currentPageNumber = 1;
}
$sortArr = [
    'popular' => 'По популярности',
    'new' => 'По новизне',
    'lowPrice' => 'Сначала дешёвые',
    'highPrice' => 'Сначала дорогие',
];
$_GET['sort'] = $sort = isset($_GET['sort']) && key_exists(trim($_GET['sort']), $sortArr) ? trim($_GET['sort']) : null;

$pattern = "http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "s" : "")."://";
$pattern .= "{$_SERVER['SERVER_NAME']}/shop/$categoryHref";
if (isset($sort)) $pattern .= "/$sort";
$pattern .= "/#";

$categories = $conn->prepare("SELECT * FROM categories");
$categories->execute();
$categories = $categories->fetchAll(PDO::FETCH_ASSOC);
$sql =
"SELECT DISTINCT p.id, p.categoryId, p.href, p.name, p.description, p.height, p.width, p.length, p.features, p.techSpec, p.countryId, p.count, p.price, (p.price - (p.price * p.sale / 100)) discounted, p.preOrder, p.keywords, p.sold, p.added, p.isDeleted";
$all_products_count = "SELECT COUNT(p.id) `counter`";

if ($categoryHref !== 'all') {
    $sql .= ", c.href cHref";
}

$sql .= " FROM products p";
$all_products_count .= " FROM products p";

if ($categoryHref !== 'all') {
    $sql .= " INNER JOIN categories c ON p.categoryId = c.id";
    $all_products_count .= " INNER JOIN categories c ON p.categoryId = c.id";
}

$sql .= " WHERE";
$all_products_count .= " WHERE";

if ($categoryHref !== 'all') {
    $sql .= " c.href = :href AND";
    $all_products_count .= " c.href = :href AND";
}

$sql .= " p.price >= 1 AND p.isDeleted = 0 AND ((p.count > 0) OR (p.count = 0 AND p.preOrder = 1))";
$all_products_count .= " p.price >= 1 AND p.isDeleted = 0 AND ((p.count > 0) OR (p.count = 0 AND p.preOrder = 1))";

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
$sql .= " LIMIT ".(($currentPageNumber - 1) * $viewCount).", ".$viewCount;

$all_products_count = $conn->prepare($all_products_count);
$products = $conn->prepare($sql);

if ($categoryHref !== 'all') {
    $products->execute(['href' => $categoryHref]);
    $all_products_count->execute(['href' => $categoryHref]);
} else {
    $products->execute();
    $all_products_count->execute();
}

$products = $products->fetchAll(PDO::FETCH_ASSOC);
$all_products_count = $all_products_count->fetch(PDO::FETCH_ASSOC);

$all_products_count = (int)$all_products_count['counter'];

if (count($products) < 1 && $currentPageNumber !== 1) {
    $_PAGE->Redirect(str_replace('/#', '', $pattern), true);
}
$pagination = new Pagination($currentPageNumber, $all_products_count, $pattern, $viewCount, false);
$pagination->SetBeforeCurrent(2);
$pagination->SetAfterCurrent(2);
$pagination->SetButtonTitle(Pagination::PREVIOUS_BUTTON, 'Назад');
$pagination->SetButtonTitle(Pagination::NEXT_BUTTON, 'Далее');
$pagination->SetMainStyle("margin-top:40px;");
$pagesData = [
    'currentPageNumber' => $currentPageNumber,
    'next' => $pagination->GetNextPage(),
    'prev' => $pagination->GetPreviousPage()
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), null, null, null, $pagesData)?>
    <link rel="stylesheet" href="/assets/common/css/productCards.css">
    <link rel="stylesheet" href="/classes/php-pagination/css/main.css">
    <link rel="stylesheet" href="/assets/common/css/shop.css">
    <script defer src="/assets/common/js/shop.js"></script>
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
                <?php $index = 0;
                if (count($products) > 0):?>
                    <?php foreach($products as $key => $product): $index++;?>
                        <?php if ($index === $viewCount): break;?>
                        <?php else:
                        $noImage = false;
                        $product['image'] = $conn->prepare("SELECT `image` FROM products_images WHERE productId = :productId AND isMain = 1 LIMIT 1");
                        $product['image']->execute(['productId' => (int) $product['id']]);
                        $product['image'] = $product['image']->fetch(PDO::FETCH_ASSOC);
                        if (isset($product['image']['image'])) {
                            $product['image'] = "/assets/images/products/".trim($product['image']['image']);
                        } else {
                            $noImage = true;
                            $product['image'] = '/assets/icons/camera.svg';
                        }?>
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
                                            $product['price'] = (double)$product['price'];
                                            $product['discounted'] = (double)$product['discounted'];
                                            if($product['discounted'] === $product['price']):?>
                                                <strong class="product-card-price-main"><?=formatPrice($product['price'])?></strong>
                                            <?php else:?>
                                                <strong class="product-card-price-main"><?=formatPrice($product['discounted'])?></strong>
                                                <span class="product-card-price-old"><?=formatPrice($product['price'])?></span>
                                            <?php endif;?>
                                        </p>
                                        <a href="/product/<?="{$product['href']}-{$product['id']}"?>" class="button elems">
                                            <span>Подробнее</span>
                                            <img class="angle-right fill-white" src="/assets/icons/angle-right-solid.svg" alt=">">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php if(false && ($index / 4) === 1): $index = 0?></div><?php endif;?>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php else:?>
                    <p class="w-100 text-center" style="font-size:20px;">К сожалению, в этой категории пока нет доступных товаров</p>
                <?php endif;?>
            </main>
        </div>
    </section>
    <div class="container"><?=$pagination->Render()?></div>
    <?php include_once('includes/footer.php')?>
</body>
</html>
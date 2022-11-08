<?php
require_once('functions/formatPrice.php');
require_once('functions/numWord.php');
require_once('includes/autoload.php');
$PAGE = new Page($conn);
$COMPANY = new Company($conn);
$USER = new User($conn);
$CART = new Cart($conn);
$PRODUCT = new Product($conn);
$product['id'] = isset($_GET['id']) ? (int) $_GET['id']: 0;
$product['href'] = isset($_GET['href']) ? trim($_GET['href']) : null;
$product = $PRODUCT->getProduct($product['id'],$product['href']);
if ($product['notFound']) {
    $product['name'] = "Товар не найден";
    $product['description'] = "К сожалению, не удалось найти искомый товар";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$PAGE->getHead($USER->isGuest(), $product['name'], $product['description'])?>
    <link rel="stylesheet" href="/assets/common/css/product.css">
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/product.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$product['name']?></h1>
    </div>
    <main>
        <section class="m-0">
            <div class="container">
                <?php if($product['notFound']):?>
                    <div class="minScreenHeight">
                        <p style="font-size:20px;">По этому адресу ничего не найдено, но можно поискать товар в <a class="primary" href="/shop">нашем каталоге</a></p>
                    </div>
                <?php else:?>
                    <div class="product" data-product-id="<?=$product['id']?>">
                        <div class="product__row mainInfo">
                            <div class="product__images">
                                <?php $noAddImgs = false; if(isset($product['images']['additional'])):?>
                                    <div class="product__images__additional custom-scroll">
                                        <?php foreach($product['images']['additional'] as $key => $image):?>
                                            <a class="item" data-fancybox="images" data-src="<?=$image?>">
                                                <img src="<?=$image?>" alt="<?=$product['name']?>">
                                            </a>
                                        <?php endforeach;?>
                                    </div>
                                <?php else: $noAddImgs = true; endif;?>
                                <div class="product__images__main shadowBox<?=$noAddImgs ? ' noAddImgs' : null?>">
                                    <a class="item" data-fancybox="images" data-src="<?=$product['images']['main']?>">
                                        <img src="<?=$product['images']['main']?>" alt="<?=$product['name']?>">
                                    </a>
                                </div>
                            </div>
                            <div class="addCart_wrapper">
                                <div class="addCart shadowBox">
                                    <?php
                                    $noProduct = formatPrice($product['price']);
                                    $noProduct = (int) $noProduct;
                                    $noProduct = $noProduct < 1 ? true : false;
                                    if ($noProduct):?>
                                        <h2 style="margin: 0; color: var(--main-color);" class="w-100">К сожалению, на данный момент товар не продаётся :(</h2>
                                    <?php else:?>
                                        <div class="addCart__price">
                                            <?php // есть ли скидка
                                            if($product['sale'] > 0):?>
                                                <strong class="addCart__price__main"><?=formatPrice(($product['price'] - ($product['price'] * $product['sale'] / 100)))?></strong>
                                                <span class="addCart__price__old"><?=formatPrice($product['price'])?></span>
                                            <?php else:?>
                                                <strong class="addCart__price__main"><?=formatPrice($product['price'])?></strong>
                                            <?php endif;?>
                                        </div>
                                        <div class="addCart_buyCounter"><?=$product['sold'] > 0 ? "Купили ".numWord($product['sold'], ['раз', 'раза', 'раз']) : 'Товар ещё не покупали'?></div>
                                        <div class="addCart__button"><img class="w-100 text-center" style="height: 50px;" src='/assets/icons/spinner.svg' alt="Загрузка..."></div>
                                    <?php endif;?>
                                </div>
                                <div class="product__shortInfo_desc shadowBox custom-scroll">
                                    <div class="shortInfo__desc__country item">
                                        <div class="d-flex flex-column">
                                            <strong class="headTitle">Страна</strong>
                                            <span class="countryName"><?=$product['country']?></span>
                                        </div>
                                    </div>
                                    <?php if(!empty($product['techSpec'])):?>
                                        <div class="shortInfo__desc__lists item">
                                            <strong class="headTitle">Технические характеристики</strong>
                                            <div class="shortInfo__desc__lists__list">
                                                <?php foreach($product['techSpec'] as $key => $item):?>
                                                    <span class="item"><?=$item['name']?> - <?=$item['value']?></span>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    <?php endif;?>
                                    <?php if(!empty($product['features'])):?>
                                        <div class="shortInfo__desc__lists item">
                                            <strong class="headTitle">Функции товара</strong>
                                            <div class="shortInfo__desc__lists__list">
                                                <?php foreach($product['features'] as $key => $feature):?>
                                                    <span class="item"><?=$feature?></span>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="product__row">
                            <div class="product__description shadowBox">
                                <h2 class="product__description__title">Описание</h2>
                                <p class="product__description__data"><?=$product['description']?></p>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php')?>
</body>
</html>
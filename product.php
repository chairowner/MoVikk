<?php
set_include_path("./");
require_once('includes/autoload.php');
require_once('functions/formatPrice.php');
require_once('functions/numWord.php');

$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_PRODUCT = new Product($conn);

$product['id'] = isset($_GET['id']) ? (int) $_GET['id']: 0;
$product['href'] = isset($_GET['href']) ? trim($_GET['href']) : null;
$product = $_PRODUCT->getProduct($product['id'],$product['href']);
if ($product['notFound']) {
    $product['name'] = "Товар не найден";
    $product['description'] = "К сожалению, не удалось найти искомый товар";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $product['name'], $product['description'])?>
    <link rel="stylesheet" href="/assets/common/css/product.css">
    <link rel="stylesheet" href="/assets/libs/magnific-popup/magnific-popup.css">
    <script defer src="/assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
    <!-- <script defer src="/assets/libs/color-thief.min.js"></script> -->
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
                                <?php
                                $noAddImgs = true;
                                if (isset($product['images']['additional']) && !empty($product['images']['additional'])) {
                                    $noAddImgs = false;
                                }
                                ?>
                                <div id="js-mainImageRole-box" class="product__images__main shadowBox<?=$noAddImgs ? ' noAddImgs' : null?>">
                                    <a class="item js-gallery" href="<?=$product['images']['main']['src']?>" title="<?=$product['name']?>">
                                        <img id="js-mainImageRole" data-founded="<?=$product['images']['notFound']?"false":"true"?>" style="object-fit: contain;" src="<?=$product['images']['main']['src']?>" alt="<?=$product['name']?>">
                                    </a>
                                </div>
                                <?php if(!$noAddImgs):?>
                                    <div class="product__images__additional custom-scroll">
                                        <?php foreach($product['images']['additional'] as $key => $image):?>
                                            <a class="item js-gallery" href="<?=$image['src']?>" title="<?=$product['name']?>">
                                                <img src="<?=$image['src']?>" alt="<?=$product['name']?>">
                                            </a>
                                        <?php endforeach;?>
                                    </div>
                                <?php endif;?>
                            </div>
                            <div class="addCart_wrapper">
                                <?php if(!empty($product['country']) || !empty($product['techSpec']) || !empty($product['features'])):?>
                                    <div class="product__shortInfo_desc shadowBox custom-scroll">
                                        <?php if (!empty($product['country'])):?>
                                            <div class="shortInfo__desc__country item">
                                                <div class="d-flex flex-column">
                                                    <strong class="headTitle">Страна</strong>
                                                    <span class="countryName"><?=$product['country']?></span>
                                                </div>
                                            </div>
                                        <?php endif;?>
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
                                <?php endif;?>
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
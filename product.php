<?php require_once('includes/autoload.php');?>
<?php require_once('classes/Product.php');?>
<?php require_once('functions/numWord.php');?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->getHead($_PRODUCT->name, $_PRODUCT->description)?>
    <link rel="stylesheet" href="/assets/css/product.css">
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PRODUCT->name?></h1>
    </div>
    <main>
        <section class="m-0">
            <div class="container">
                <div class="product">
                    <div class="product__row">
                        <div class="product__images">
                            <?php $noAddImgs = false; if(isset($_PRODUCT->images['additional'])):?>
                                <div class="product__images__additional custom-scroll">
                                    <?php foreach($_PRODUCT->images['additional'] as $key => $image):?>
                                        <a class="item" data-fancybox="images" data-src="/assets/images/products/<?=$image?>">
                                            <img src="/assets/images/products/<?=$image?>" alt="<?=$_PRODUCT->name?>">
                                        </a>
                                    <?php endforeach;?>
                                </div>
                            <?php else: $noAddImgs = true; endif;?>
                            <div class="product__images__main shadowBox<?=$noAddImgs ? ' noAddImgs' : null?>">
                                <a class="item" data-fancybox="images" data-src="/assets/images/products/<?=$_PRODUCT->images['main']?>">
                                    <?php if(isset($_PRODUCT->images['main'])):?>
                                        <img src="/assets/images/products/<?=$_PRODUCT->images['main']?>" alt="<?=$_PRODUCT->name?>">
                                    <?php else:?>
                                        <img src="/assets/icons/camera.svg" alt="<?=$_PRODUCT->name?>">
                                    <?php endif;?>
                                </a>
                            </div>
                        </div>
                            <div class="product__shortInfo_desc shadowBox custom-scroll">
                                <div class="shortInfo__desc__country item">
                                    <div class="d-flex flex-column">
                                        <strong class="headTitle">Страна</strong>
                                        <span class="countryName"><?=$_PRODUCT->country?></span>
                                    </div>
                                </div>
                                <?php if(!empty($_PRODUCT->techSpec)):?>
                                    <div class="shortInfo__desc__lists item">
                                        <strong class="headTitle">Технические характеристики</strong>
                                        <div class="shortInfo__desc__lists__list">
                                            <?php foreach($_PRODUCT->techSpec as $key => $item):?>
                                                <span class="item"><?=$item['name']?> - <?=$item['value']?></span>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <?php if(!empty($_PRODUCT->features)):?>
                                    <div class="shortInfo__desc__lists item">
                                        <strong class="headTitle">Функции товара</strong>
                                        <div class="shortInfo__desc__lists__list">
                                            <?php foreach($_PRODUCT->features as $key => $feature):?>
                                                <span class="item"><?=$feature?></span>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                <?php endif;?>
                            </div>
                            <div class="addCart_wrapper">
                                <div class="addCart shadowBox">
                                    <?php
                                    $noProduct = formatPrice($_PRODUCT->price);
                                    $noProduct = (int) $noProduct;
                                    $noProduct = $noProduct < 1 ? true : false;
                                    if ($noProduct):?>
                                        <h2 style="margin: 0; color: var(--main-color);" class="w-100">К сожалению, на данный момент товар не продаётся :(</h2>
                                    <?php else:?>
                                        <div class="addCart__price">
                                            <?php // есть ли скидка
                                            if($_PRODUCT->sale > 0):?>
                                                <strong class="addCart__price__main"><?=formatPrice(($_PRODUCT->price - ($_PRODUCT->price * $_PRODUCT->sale / 100)))?></strong>
                                                <span class="addCart__price__old"><?=formatPrice($_PRODUCT->price)?></span>
                                            <?php else:?>
                                                <strong class="addCart__price__main"><?=formatPrice($_PRODUCT->price)?></strong>
                                            <?php endif;?>
                                        </div>
                                        <div class="addCart_buyCounter"><?=$_PRODUCT->sold > 0 ? "Купили ".numWord($_PRODUCT->sold, ['раз', 'раза', 'раз']) : 'Товар ещё не покупали'?></div>
                                        <div class="addCart__button">
                                            <button class="button w-100" id="js-addToCard">
                                                <span>Добавить в корзину</span>
                                            </button>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                    </div>
                    <div class="product__row">
                        <div class="product__description shadowBox">
                            <h2 class="product__description__title">Описание</h2>
                            <p class="product__description__data"><?=$_PRODUCT->description?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php')?>
    <?php include_once('includes/scripts.php')?>
    <script src="/assets/js/product.js"></script>
</body>
</html>
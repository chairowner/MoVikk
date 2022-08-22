<?php require_once('includes/autoload.php');?>
<?php require_once('classes/Product.php');?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->getHead($_PRODUCT->name, $_PRODUCT->description)?>
    <link rel="stylesheet" href="/assets/css/product.css">
</head>
<body>
    <?php include_once('includes/header.php')?>
    <main>
        <section>
            <div class="container">
                <div class="product">
                    <div class="product__images">
                        <?php $noAddImgs = false; if(isset($_PRODUCT->images['additional'])):?>
                            <div class="product__images__additional">
                                <?php foreach($_PRODUCT->images['additional'] as $key => $image):?>
                                    <a class="item" data-fancybox="images" data-src="/assets/images/products/<?=$image?>">
                                        <img src="/assets/images/products/<?=$image?>" alt="<?=$_PRODUCT->name?>">
                                    </a>
                                <?php endforeach;?>
                            </div>
                        <?php else: $noAddImgs = true; endif;?>
                        <div class="product__images__main<?=$noAddImgs ? ' noAddImgs' : null?>">
                            <a class="item" data-fancybox="images" data-src="/assets/images/products/<?=$_PRODUCT->images['main']?>">
                                <?php if(isset($_PRODUCT->images['main'])):?>
                                    <img src="/assets/images/products/<?=$_PRODUCT->images['main']?>" alt="<?=$_PRODUCT->name?>">
                                <?php else:?>
                                    <img src="/assets/icons/camera.svg" alt="<?=$_PRODUCT->name?>">
                                <?php endif;?>
                            </a>
                        </div>
                    </div>
                    <div class="product__shortInfo">
                        <h1 class="product__shortInfo__title"><?=$_PRODUCT->name?></h1>
                        <div class="product__shortInfo__price">
                            <div class="product__shortInfo__price">
                                <?php
                                // есть ли скидка
                                if($_PRODUCT->sale > 0):?>
                                    <span class="product__shortInfo__price__old"><?=formatPrice($_PRODUCT->price)?></span>
                                    <strong class="product__shortInfo__price__main"><?=formatPrice(($_PRODUCT->price - ($_PRODUCT->price * $_PRODUCT->sale / 100)))?></strong>
                                <?php else:?>
                                    <strong class="product__shortInfo__price__main">
                                        <?=$_PRODUCT->price?> ₽
                                    </strong>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="product__shortInfo__button">
                            <button class="button elems" id="js-addToCard">
                                <span>В корзину</span>
                                <img src="/assets/icons/button-plus.svg" alt="+">
                            </button>
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
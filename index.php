<?php require_once('includes/autoload.php');?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->getHead($_COMPANY->name.' - '.mb_strtolower($_PAGE->description), $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>
    <?php include_once('includes/header.php');?>
    <main>
        <section>
            <div class="container">
                <h2 class="text-uppercase">Популярные товары</h2>
                <div class="product-cards">
                    <?php
                    $popular_products = $conn->prepare("SELECT pim.image, p.name, p.description, p.href, p.id, p.price FROM products p INNER JOIN products_images pim ON p.id = pim.productId WHERE pim.isMain = 1 ORDER BY sold LIMIT 4");
                    $popular_products->execute();
                    $popular_products = $popular_products->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($popular_products as $product_key => $product):?>
                        <div class="product-card">
                            <div class="product-card-image">
                                <img src="/assets/images/products/<?=$product['image']?>" alt="<?=$product['name']?>">
                            </div>
                            <div class="product-card-body">
                                <p class="product-card-title"><?=$product['name']?></p>
                                <div class="product-card-text">
                                    <p><?=trim(substr($product['description'], 0, 300))?></p>
                                </div>
                                <p class="product-card-price"><?=formatPrice(doubleval($product['price']))?></p>
                                <a href="/product/<?="{$product['href']}-{$product['id']}"?>" class="button elems">
                                    <span>Подробнее</span>
                                    <i class="fa-solid fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
        </section>
        <section>
            <div class="container">
                <h2>Категории товаров</h2>
                <a href="/shop" class="category all_categories" style="background-image:url(/assets/images/all_products.png);">
                    <p>Все товары</p>
                    <div class="category__black"></div>
                </a>
                <div class="main-categories">
                    <?php
                    $categories = $conn->prepare("SELECT * FROM categories");
                    $categories->execute();
                    $categories = $categories->fetchAll(PDO::FETCH_ASSOC);
                    foreach($categories as $category_key => $category):?>
                    <a href="/shop/<?=$category['href']?>" class="category" style="background-image:url(/assets/images/categories/category_<?=$category['id']?>.png);">
                        <p><?=$category['name']?></p>
                    </a>
                    <?php endforeach;?>
                </div>
            </div>
        </section>
        <section>
            <div class="container">
                <h2 class="text-center">При покупке оборудования, обучение по эксплуатации в подарок!</h2>
                <iframe class="video-block" src="https://vk.com/video_ext.php?oid=-179978507&id=456240267&hash=fd0b36296619d848&hd=2" allow="autoplay; encrypted-media; fullscreen; picture-in-picture;" frameborder="0" allowfullscreen></iframe>
            </div>
        </section>
        <section>
            <?php
            $features = [
                [
                    'img' => 'pay.svg',
                    'title' => 'Оплата различными способами',
                    'text' => 'При покупке у вас будет возможность оформить кредит, рассрочку либо же оплатить по карте. Также есть возможность оплатить на расчетный счет компании.',
                ],
                [
                    'img' => 'box.svg',
                    'title' => 'Доставка по всей России',
                    'text' => 'Как вы уже поняли, доставка осуществляется по всей России. Мы можем отправить товар в любую почту, которая есть в вашем населённом пункте.',
                ],
                [
                    'img' => 'video-play.svg',
                    'title' => 'Обучение в подарок',
                    'text' => 'При покупке любого оборудования, если вы не разобрались как им пользоваться - мы предоставим вам видеоинструкцию о том, как им пользоваться.',
                ],
            ];
            
            ?>
            <div class="container">
                <h2 class="text-center">Покупая у нас вы получаете следующие возможности</h2>
                <div id="features">
                    <?php foreach($features as $key => $feature):?>
                        <div class="feature">
                            <div class="feature__img">
                                <img src="/assets/icons/<?=$feature['img']?>" alt="<?=$feature['title']?>">
                            </div>
                            <div>
                                <h3 class="feature__title"><?=$feature['title']?></h3>
                                <p class="feature__text"><?=$feature['text']?></p>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php');?>
    <?php include_once('includes/scripts.php');?>
    <script src="/assets/js/index.js"></script>
</body>
</html>
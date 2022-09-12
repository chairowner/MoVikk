<?php require_once('includes/autoload.php');?>
<?php
$popular_products = $conn->prepare("SELECT pim.image, p.name, p.description, p.href, p.id, p.price, (p.price - (p.price * p.sale / 100)) discounted FROM products p INNER JOIN products_images pim ON p.id = pim.productId WHERE pim.isMain = 1 ORDER BY p.sold LIMIT 4");
$popular_products->execute();
$popular_products = $popular_products->fetchAll(PDO::FETCH_ASSOC);
?>
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
                <div class="product-cards w-100">
                    <?php foreach($popular_products as $product_key => $product):
                        $noImage = false;
                        $product['image'] = $conn->prepare( "SELECT `image` FROM products_images WHERE id = :id AND isMain = 1 LIMIT 1");
                        $product['image']->execute(['id' => (int) $product['id']]);
                        $product['image'] = $product['image']->fetch(PDO::FETCH_ASSOC);
                        if (isset($product['image']['image'])) $product['image'] = "/assets/images/products/".trim($product['image']['image']);
                        else {
                            $noImage = true;
                            $product['image'] = '/assets/icons/camera.svg';
                        }?>
                        <div class="product-card stretch w-300px shadowBox">
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
                    <?php endforeach;?>
                </div>
            </div>
        </section>
        <section>
            <div class="container">
                <h2>Категории товаров</h2>
                <a href="/shop" class="category all_categories">
                    <p>Все товары</p>
                    <img src="/assets/images/all_products.png" alt="Все товары">
                </a>
                <div class="main-categories">
                    <?php
                    $categories = $conn->prepare("SELECT * FROM categories");
                    $categories->execute();
                    $categories = $categories->fetchAll(PDO::FETCH_ASSOC);
                    foreach($categories as $category_key => $category):
                    $category['id'] = (int) $category['id'];
                    $category['path'] = "assets/images/categories";
                    $category['path'] = file_exists("{$category['path']}/category_{$category['id']}.png") ?
                        "{$category['path']}/category_{$category['id']}.png" : null;?>
                        <a href="/shop/<?=$category['href']?>" class="category">
                            <?php if(isset($category['path'])):?>
                                <img src="/<?=$category['path']?>" alt="<?=$category['name']?>">
                            <?php endif;?>
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
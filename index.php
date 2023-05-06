<?php
set_include_path(".");
require_once('includes/autoload.php');
require_once('functions/formatPrice.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $_COMPANY->name.' - '.mb_strtolower($_PAGE->description), $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/productCards.css">
    <link rel="stylesheet" href="/assets/common/css/index.css">
    <script defer src="/assets/common/js/index.js"></script>
</head>
<body>
    <?php include_once('includes/header.php');?>
    <main>
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

                    $category_path = "assets";
                    if (!is_dir($category_path)) mkdir($category_path);
                    $category_path .= "/images";
                    if (!is_dir($category_path)) mkdir($category_path);
                    $category_path .= "/categories";
                    if (!is_dir($category_path)) mkdir($category_path);

                    foreach($categories as $category_key => $category):
                        $category['id'] = (int) $category['id'];
                        $category['image'] =
                            isset($category['image']) && trim($category['image']) !== "" ?
                                trim($category['image']) : null;
                        
                        $category['path'] =
                            file_exists("$category_path/{$category['image']}") ?
                                "$category_path/{$category['image']}" : null;?>
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
</body>
</html>
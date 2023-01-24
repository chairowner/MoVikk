<?php
set_include_path(".");
require_once('functions/numWord.php');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest())?>
    <link rel="stylesheet" href="/assets/common/css/faq.css">
    <script defer src="/assets/common/js/faq.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <main>
        <section class="m-0">
            <div class="container">
                <div class="w-100 d-flex justify-content-between flex-wrap" style="gap:40px;">
                    <div class="questions">
                        <?php
                        $faqs = $conn->prepare("SELECT * FROM faq");
                        $faqs->execute();
                        $faqs = $faqs->fetchAll(PDO::FETCH_ASSOC);
                        if(isset($faqs) && !empty($faqs)):?>
                            <?php foreach($faqs as $key => $faq):?>
                                <div class="shadowBox question">
                                    <div class="text"><?=$faq['question']?></div>
                                    <div class="answer"><span><?=$faq['answer']?></span></div>
                                </div>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>
                    <div class="quest-icon">
                        <svg class="quest-icon__img" width="261" height="262" viewBox="0 0 261 262" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M124.276 0.0523682C94.6253 1.6295 67.3825 12.608 44.9965 31.9637C42.1804 34.4011 35.8747 40.6277 33.3647 43.4543C22.8145 55.3135 14.6927 68.4631 8.9993 82.9441C4.42823 94.5575 1.63253 106.396 0.367318 119.648C-0.122439 124.81 -0.122439 137.222 0.367318 142.384C2.57123 165.324 9.67271 185.458 22.1615 204.158C29.4059 215.034 39.2214 225.644 49.588 233.857C69.7701 249.834 92.9519 259.092 119.174 261.631C124.317 262.123 136.683 262.123 141.826 261.631C164.538 259.44 184.741 252.291 203.127 239.941C210.024 235.291 215.657 230.683 221.881 224.517C227.268 219.213 230.472 215.567 234.778 209.832C249.634 190.087 258.204 167.7 260.633 142.384C261.122 137.222 261.122 124.81 260.633 119.648C257.837 90.5225 246.777 64.9811 227.635 43.4543C225.125 40.6277 218.82 34.4011 216.004 31.9637C194.964 13.755 169.783 3.02229 142.03 0.441528C138.724 0.134294 127.419 -0.11149 124.276 0.0523682ZM134.989 48.9026C147.539 50.152 158.579 55.0882 166.314 62.8919C171.334 67.9511 175.048 74.6283 176.803 81.7561C177.7 85.4019 177.945 87.8393 177.945 93.5334C177.945 100.62 177.313 105.147 175.68 110.042C172.864 118.481 167.252 124.892 158.641 129.459C154.192 131.815 150.009 133.249 142.907 134.805L138.499 135.788L138.418 142.158C138.316 149.061 138.254 149.47 137.03 151.908C135.989 153.977 133.867 155.738 131.296 156.639C129.378 157.336 125.48 157.336 123.46 156.66C119.991 155.492 117.93 153.239 116.889 149.45C116.562 148.242 116.522 147.054 116.522 136.649C116.522 126.387 116.562 125.056 116.868 124.052C117.623 121.615 119.011 121.021 128.051 119.157C134.377 117.846 137.581 116.74 140.969 114.712C147.723 110.636 151.498 104.471 152.111 96.5238C152.641 89.5189 150.6 84.1935 145.417 78.9705C143.132 76.6765 141.764 75.6729 139.275 74.4644C135.847 72.8054 132.786 72.1499 128.153 72.1499C121.848 72.1295 117.358 73.3174 113.154 76.0825C110.869 77.5982 107.114 81.3669 104.033 85.2381C101.217 88.761 98.2372 91.7514 96.9516 92.325C95.768 92.837 92.8295 93.1238 91.1153 92.8985C89.1971 92.6322 87.7687 91.8539 86.0953 90.1743C83.5853 87.655 82.7895 85.2995 83.0139 80.9778C83.3608 74.6078 85.4219 69.733 89.9726 64.592C94.9926 58.9593 102.012 54.4737 110.298 51.6677C118.583 48.8616 126.153 48.0218 134.989 48.9026ZM129.786 191.869C131.153 192.135 133.418 193.282 134.806 194.388C136.193 195.515 137.336 196.969 138.438 199.017C142.193 206.022 138.52 214.788 130.704 217.472C128.684 218.168 125.439 218.127 123.337 217.41C121.113 216.632 119.378 215.546 117.725 213.887C115.134 211.286 114.032 208.193 114.236 204.158C114.399 200.553 115.583 198.034 118.276 195.433C121.746 192.115 125.317 191.008 129.786 191.869Z" fill="#1A6B70"/>
                        </svg>
                        <strong>Не нашли ответ?<br>Напишите нам, и мы поможем!</strong>
                        <a class="d-flex justify-content-center align-items-center flex-wrap" style="gap:10px;" target="_blank" href="<?=$_COMPANY->socials['vk']['href']?>">
                            <img class="social-icon" src="/assets/icons/social-vk.svg" alt="ВКонтакте">
                            <?=$_COMPANY->socials['vk']['title']?>
                        </a>
                        <a class="d-flex justify-content-center align-items-center flex-wrap" style="gap:10px;" target="_blank" href="<?=$_COMPANY->socials['instagram']['href']?>">
                            <img class="social-icon" src="/assets/icons/social-instagram.svg" alt="Инстаграм">
                            <?=$_COMPANY->socials['instagram']['title']?>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php')?>
</body>
</html>
<footer>
    <div class="container">
        <div class="footer__left">
            <a href="/" class="footer__logo">
                <img src="/assets/icons/logo.svg" class="logo" alt="<?=$_COMPANY->name?>">
            </a>
            <div class="footer__data">
                <div class="social">
                <a href="<?=$_COMPANY->socials['vk']['href']?>" title="<?=$_COMPANY->socials['vk']['name']?>">
                    <i class="fa-brands fa-vk"></i>
                    <span><?=$_COMPANY->socials['vk']['title']?></span>
                </a>
                <a href="<?=$_COMPANY->socials['instagram']['href']?>" title="<?=$_COMPANY->socials['instagram']['name']?>">
                    <i class="fa-brands fa-instagram"></i>
                    <span><?=$_COMPANY->socials['instagram']['title']?></span>
                </a>
                </div>
                <span>Позвоните нам: <a href="tel:<?=$_COMPANY->phone?>"><?=$_COMPANY->phone_format?></a></span>
            </div>
        </div>
        <ul class="company_data">
            <?php foreach($_COMPANY->info as $key => $value):?>
                <li>
                    <span class="fw-bold"><?=$key?>:</span>
                    <span><?=$value?></span>
                </li>
            <?php endforeach;?>
        </div>
    </div>
</footer>
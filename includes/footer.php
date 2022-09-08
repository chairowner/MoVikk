<footer>
    <div class="container">
        <div class="footer__left">
            <a href="/" class="footer__logo">
                <img src="/assets/icons/logo.svg" class="logo" alt="<?=$_COMPANY->name?>">
            </a>
            <div class="footer__data">
                <div class="social">
                    <a target="_blank" href="<?=$_COMPANY->socials['vk']['href']?>" title="<?=$_COMPANY->socials['vk']['name']?>">
                        <i class="social-icon fa-brands fa-vk color-vk"></i>
                        <span><?=$_COMPANY->socials['vk']['title']?></span>
                    </a>
                    <a target="_blank" href="<?=$_COMPANY->socials['instagram']['href']?>" title="<?=$_COMPANY->socials['instagram']['name']?>">
                        <i class="social-icon fa-brands fa-instagram icon-instagram"></i>
                        <span><?=$_COMPANY->socials['instagram']['title']?></span>
                    </a>
                </div>
                <div class="footer__callUs">
                    <span>Позвоните нам:</span>
                    <a href="tel:<?=$_COMPANY->phone?>"><?=$_COMPANY->phone_format?></a>
                </div>
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
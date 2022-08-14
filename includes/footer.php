<footer>
    <div class="container">
        <div class="footer__left">
            <a href="/" class="footer__logo">
                <img src="/assets/icons/logo.svg" class="logo" alt="<?=$company->name?>">
            </a>
            <div class="d-flex flex-column justify-content-around">
                <div class="d-flex flex-row flex-wrap social">
                    <div><?=$company->vk?></div>
                    <div><?=$company->inst?></div>
                </div>
                <span>Позвоните нам: <a href="tel:<?=$company->tel?>"><?=$company->tel_format?></a></span>
            </div>
        </div>
        <ul class="d-flex flex-column company_data">
            <li>
                <span class="fw-bold">ИНН:</span>
                592011319403
            </li>
            <li>
                <span class="fw-bold">ОГРН:</span>
                314595810600339
            </li>
            <li>
                <span class="fw-bold">Расчётный счет:</span>
                40802810900000116741
            </li>
            <li>
                <span class="fw-bold">БИК:</span>
                044525974
            </li>
            <li>
                <span class="fw-bold">K/С:</span>
                30101810145250000974
            </li>
        </div>
    </div>
</footer>
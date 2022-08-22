<?php
/**
 * Класс компании
 * @param string $name название компании
 * @param string $phone номер телефона
 * @param string $timezone временая зона
 * @param array $info информация о компании
 * @param array $social ссылки на соц. сети
 */
class Company {
    public string $name;
    public string $phone;
    public string $timezone;
    public array $info;
    public array $social;

    /**
     * @param PDO $conn соединение с БД
     */
    function __construct(PDO $conn = null) {
        if (isset($conn)) {
            $company = $conn->prepare("SELECT * FROM company");
            $company->execute();
            $company = $company->fetch(PDO::FETCH_ASSOC);
            if (isset($company) && !empty($company)) {
                $this->name = trim($company['name']);
                $this->phone = $company['phone'];
                $this->phone_format = sprintf(
                    "%s (%s) %s-%s-%s",
                    intval(substr($this->phone, 1, 1)) + 1,
                    substr($this->phone, 2, 3),
                    substr($this->phone, 5, 3),
                    substr($this->phone, 8, 2),
                    substr($this->phone, 10)
                );
                $this->info = [
                    'ИНН' => $company['inn'],
                    'ОРГН' => $company['ogrn'],
                    'Расчётный счёт' => $company['pay_acc'],
                    'БИК' => $company['bik'],
                    'К/С' => $company['ks'],
                ];
            }
            $socials = $conn->prepare("SELECT * FROM socials");
            $socials->execute();
            $socials = $socials->fetchAll(PDO::FETCH_ASSOC);
            if (isset($socials) && !empty($socials)) {
                foreach ($socials as $social_key => $social) {
                    $this->socials[$social['shortKey']] = [
                        'name' => $social['name'],
                        'href' => $social['href'],
                        'title' => $social['title'],
                    ];
                }
            }
        }
    }
}

$_COMPANY = isset($conn) ? new Company($conn) : new Company();
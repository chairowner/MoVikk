<?php
/**
 * Класс компании
 * @param string $name название компании
 * @param string $phone номер телефона
 * @param array $info информация о компании
 * @param array $social ссылки на соц. сети
 */
class Company {
    public string $name;
    public string $phone;
    public array $info;
    public array $social;
    private string $mainTable = "company";

    /**
     * @param PDO $conn соединение с БД
     */
    function __construct(PDO $conn = null) {
        if (isset($conn)) {
            $company = $conn->prepare("SELECT * FROM `company`");
            $company->execute();
            $company = $company->fetch(PDO::FETCH_ASSOC);
            if (isset($company) && !empty($company)) {
                $this->name = trim($company['name']);
                $this->phone = trim($company['phone']);
                $this->phone_format = sprintf(
                    "%s (%s) %s-%s-%s",
                    (int) ((substr($this->phone, 1, 1)) + 1),
                    substr($this->phone, 2, 3),
                    substr($this->phone, 5, 3),
                    substr($this->phone, 8, 2),
                    substr($this->phone, 10)
                );
                $this->info = [
                    'ИНН' => trim($company['inn']),
                    'ОРГН' => trim($company['ogrn']),
                    'Расчётный счёт' => trim($company['pay_acc']),
                    'БИК' => trim($company['bik']),
                    'К/С' => trim($company['ks']),
                ];
            }
            $socials = $conn->prepare("SELECT * FROM `socials`");
            $socials->execute();
            $socials = $socials->fetchAll(PDO::FETCH_ASSOC);
            if (isset($socials) && !empty($socials)) {
                foreach ($socials as $social_key => $social) {
                    $this->socials[trim($social['shortKey'])] = [
                        'name' => trim($social['name']),
                        'href' => trim($social['href']),
                        'title' => trim($social['title']),
                    ];
                }
            }
        }
    }
}
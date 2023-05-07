<?php
/**
 * Класс компании
 * @param string $name название компании
 * @param string $phone номер телефона
 * @param array $info информация о компании
 * @param array $socials ссылки на соц. сети
 * @param string $mainTable таблица
 */
class Company {
    public string $name;
    public string $phone;
    public string $phone_format;
    public array $info;
    public array $socials;
    private string $mainTable = "company";

    /**
     * @param PDO $conn соединение с БД
     */
    function __construct(PDO $conn = null) {
        if (isset($conn)) {
            $company = $conn->prepare("SELECT * FROM `{$this->mainTable}`");
            $company->execute();
            $company = $company->fetch(PDO::FETCH_ASSOC);
            if (isset($company) && !empty($company)) {
                $this->name = trim($company['name']);
                $this->phone = trim($company['phone']);
                $this->phone_format = sprintf(
                    "%s (%s) %s-%s-%s",
                    (int) ((mb_substr($this->phone, 1, 1)) + 1),
                    mb_substr($this->phone, 2, 3),
                    mb_substr($this->phone, 5, 3),
                    mb_substr($this->phone, 8, 2),
                    mb_substr($this->phone, 10)
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

    public function GetTable():string {
        return $this->mainTable;
    }
}
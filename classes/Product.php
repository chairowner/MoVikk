<?php
/**
 * Класс продукта
 * @param int $id идентификатор продукта
 */
class Product {
    public int $id = 0;
    public int $categoryId = 0;
    public int $instructionId = 0;
    public string $href;
    public string $name;
    public string $description;
    public array $features;
    public string $country;
    public array $techSpec;
    public array $images;
    public int $quantity;
    public float $price;
    public float $sale;
    public bool $preOrder;
    public string $keywords;
    public int $sold;
    public DateTimeZone $addedTimezone;
    public DateTime $added;
    public bool $isDeleted;

    /**
     * @param PDO $conn подключение к БД
     */
    function __construct(PDO $conn = null, array $request = null) {
        if (isset($conn)) {
            $product = $conn->prepare("SELECT * FROM `products` WHERE `id` = :id AND `href` = :href");
            $product->execute([
                'id' => (int) ($request['id']),
                'href' => trim($request['href']),
            ]);
            $product = $product->fetch(PDO::FETCH_ASSOC);
            if (isset($product) && !empty($product)) {
                // $timezone = $conn->prepare("SELECT timezone FROM company");
                // $timezone->execute();
                // $timezone = $timezone->fetch(PDO::FETCH_ASSOC);
                // $timezone = isset($timezone) ? trim($timezone['timezone']) : 'Europe/Moscow';
                $this->id = (int) ($product['id']);
                $this->categoryId = (int) ($product['categoryId']);
                $this->instructionId = (int) ($product['categoryId']);
                $this->href = trim($product['href']);
                $this->name = trim($product['name']);
                $this->description = trim($product['description']);
                $images = $conn->prepare("SELECT * FROM products_images WHERE productId = :id");
                $images->execute([
                    'id' => (int) ($this->id),
                ]);
                $images = $images->fetchAll(PDO::FETCH_ASSOC);
                if (isset($images) && !empty($images)) {
                    $setMain = false;
                    foreach ($images as $key => $image) {
                        /* отбор главного изображение */
                        if ((int) ($image['isMain']) === 1 && !$setMain) {
                            $this->images['main'] = $image['image'];
                            $setMain = true;
                        } else {
                            $this->images['additional'][] = $image['image'];
                        }
                    }

                    if (!$setMain) /* если нет основого изображения */ {
                        if (isset($this->images['additional']) && !empty($this->images['additional']))
                        $this->images['main'] = $this->images['additional'][0];
                    }
                }
                $this->quantity = (int) ($product['quantity']);
                $this->price = doubleval($product['price']);
                $this->sale = doubleval($product['sale']);
                $this->preOrder = (int) ($product['preOrder']) === 1 ? true : false;
                $this->keywords = trim($product['keywords']);
                $this->sold = (int) ($product['sold']);
                $this->addedTimezone = new DateTimeZone($product['addedTimezone']);
                $this->added = new DateTime($product['added'], $this->addedTimezone);
                $this->isDeleted = (int) ($product['isDeleted']) === 1 ? true : false;
                $country = $conn->prepare("SELECT `name` FROM `countries` WHERE `id` = :id");
                $country->execute(['id' => (int) $product['countryId']]);
                $country = $country->fetch(PDO::FETCH_ASSOC);
                $this->country = $country['name'];
                
                if (isset($product['features']) && ! empty($product['features'])) {
                    $product['features'] = explode(';', $product['features']);
                    for ($i = 0; $i < count($product['features']); $i++) {
                        if (!empty(trim($product['features'][$i]))) {
                            $this->features[$i] = trim($product['features'][$i]);
                        }
                    }
                } else {$this->features = [];}

                if (isset($product['techSpec']) && !empty($product['techSpec'])) {
                    $sign = ';';
                    $product['techSpec'] = explode($sign, $product['techSpec']);
                    for ($i = 0; $i < count($product['techSpec']); $i++) {
                        $sign = ':';
                        if (strpos($product['techSpec'][$i], $sign) !== false) {
                            $spec = explode($sign, $product['techSpec'][$i]);
                            if (isset($spec[1]) && !empty(trim($spec[1]))) {
                                $this->techSpec[$i] = [
                                    'name' => trim($spec[0]),
                                    'value' => trim($spec[1]),
                                ];
                            }
                        }
                    }
                } else {$this->techSpec = [];}
            }
        }
    }
}

$_PRODUCT = isset($conn) && isset($_GET['href']) && isset($_GET['id']) ? new Product($conn, $_GET) : new Product();
<?php
/**
 * Класс продукта
 * @param int $id идентификатор продукта
 */
class Product {
    public int $id = 0;
    public int $categoryId = 0;
    public string $href;
    public string $name;
    public string $description;
    public array $images;
    public int $quantity;
    public float $price;
    public float $sale;
    public bool $preOrder;
    public string $keywords;
    public int $sold;
    public DateTimeZone $addedTimezone;
    public DateTime $added;

    /**
     * @param PDO $conn подключение к БД
     */
    function __construct(PDO $conn = null, array $request = null) {
        if (isset($conn)) {
            $product = $conn->prepare("SELECT * FROM `products` WHERE `id` = :id AND `href` = :href");
            $product->execute([
                'id' => intval($request['id']),
                'href' => trim($request['href']),
            ]);
            $product = $product->fetch(PDO::FETCH_ASSOC);
            if (isset($product) && !empty($product)) {
                // $timezone = $conn->prepare("SELECT timezone FROM company");
                // $timezone->execute();
                // $timezone = $timezone->fetch(PDO::FETCH_ASSOC);
                // $timezone = isset($timezone) ? trim($timezone['timezone']) : 'Europe/Moscow';
                $this->id = intval($product['id']);
                $this->categoryId = intval($product['categoryId']);
                $this->href = trim($product['href']);
                $this->name = trim($product['name']);
                $this->description = trim($product['description']);
                $images = $conn->prepare("SELECT * FROM products_images WHERE productId = :id");
                $images->execute([
                    'id' => intval($this->id),
                ]);
                $images = $images->fetchAll(PDO::FETCH_ASSOC);
                if (isset($images) && !empty($images)) {
                    $setMain = false;
                    foreach ($images as $key => $image) {
                        /* отбор главного изображение */
                        if (intval($image['isMain']) === 1 && !$setMain) {
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
                $this->quantity = intval($product['quantity']);
                $this->price = doubleval($product['price']);
                $this->sale = doubleval($product['sale']);
                $this->preOrder = intval($product['preOrder']) === 1 ? true : false;
                $this->keywords = trim($product['keywords']);
                $this->sold = intval($product['sold']);
                $this->addedTimezone = new DateTimeZone($product['addedTimezone']);
                $this->added = new DateTime($product['added'], $this->addedTimezone);
            }
        }
    }
}

$_PRODUCT = isset($conn) && isset($_GET['href']) && isset($_GET['id']) ? new Product($conn, $_GET) : new Product();
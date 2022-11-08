<?php
/**
 * Класс продукта
 * @param int $id идентификатор продукта
 * @param PDO $conn соединение с БД
 */
class Product {
    const ALL = 0;
    const ONE = 1;
    public int $id = 0; // $id идентификатор продукта
    public PDO $conn; // соединение с БД
    private string $mainTable = 'products'; // таблица с продуктами
    private string $addTable = 'products_images'; // таблица с изображениями продуктов
    private string $companyTable = 'company'; // таблица с компаниями
    private string $countryTable = 'countries'; // таблица со странами

    /**
     * @param PDO $conn подключение к БД
     */
    function __construct(PDO $conn) {
        if (isset($conn)) {
            $this->conn = $conn;
        }
    }

    /**
     * Функция возвращает ID товара
     * @return int
     */
    function getId() {
        return $this->id;
    }

    public function getPopular() {
        try {
            $popular_products = $this->conn->prepare("SELECT pim.image, p.name, p.description, p.href, p.id, p.price, (p.price - (p.price * p.sale / 100)) discounted FROM products p INNER JOIN products_images pim ON p.id = pim.productId WHERE pim.isMain = 1 ORDER BY p.sold LIMIT 4");
            $popular_products->execute();
            return $popular_products->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Функция возвращает массив с данными о товаре
     * @param int $productId id товара
     * @param string $href путь к изображению
     * @return array|null
     */
    function getProduct(int $id, string $href) {
        $href = trim($href);
        try {
            $response = null;
            $product = $this->conn->prepare("SELECT `categoryId`,`countryId`,`instructionId`,`name`,`description`,`height`,`width`,`length`,`features`,`techSpec`,`count`,`price`,`sale`,`preOrder`,`keywords`,`sold`,`added`,`isDeleted` FROM `{$this->mainTable}` WHERE `id` = :id AND `href` = :href");
            $product->execute([
                'id' => $id,
                'href' => $href,
            ]);
            $product = $product->fetch(PDO::FETCH_ASSOC);

            if (!isset($product) || empty($product)) return $response;

            $response['notFound'] = false;
            $response['id'] = $id;
            $response['categoryId'] = (int) ($product['categoryId']);
            $response['instructionId'] = (int) ($product['categoryId']);
            $response['href'] = $href;
            $response['name'] = trim($product['name']);
            $response['description'] = trim($product['description']);
            $response['images'] = $this->getImages($id);
            $response['count'] = (int) ($product['count']);
            $response['price'] = (float)$product['price'];
            $response['sale'] = (float)$product['sale'];
            $response['preOrder'] = (int) ($product['preOrder']) === 1 ? true : false;
            $response['sold'] = (int) ($product['sold']);
            $response['keywords'] = trim($product['keywords']);
            $timezone = $this->conn->prepare("SELECT `timezone` FROM `{$this->companyTable}`");
            $timezone->execute();
            $timezone = $timezone->fetch(PDO::FETCH_ASSOC);
            $response['added'] = new DateTime($product['added'], new DateTimeZone($timezone['timezone']));
            $response['isDeleted'] = (int) ($product['isDeleted']) === 1 ? true : false;
            $country = $this->conn->prepare("SELECT `name` FROM `{$this->countryTable}` WHERE `id` = :id");
            $country->execute(['id' => (int) $product['countryId']]);
            $country = $country->fetch(PDO::FETCH_ASSOC);
            $response['country'] = $country['name'];
            
            if (isset($product['features']) && ! empty($product['features'])) {
                $product['features'] = explode(';', $product['features']);
                for ($i = 0; $i < count($product['features']); $i++) {
                    if (!empty(trim($product['features'][$i]))) {
                        $response['features'][$i] = trim($product['features'][$i]);
                    }
                }
            } else {$response['features'] = [];}

            if (isset($product['techSpec']) && !empty($product['techSpec'])) {
                $sign = ';';
                $product['techSpec'] = explode($sign, $product['techSpec']);
                for ($i = 0; $i < count($product['techSpec']); $i++) {
                    $sign = ':';
                    if (strpos($product['techSpec'][$i], $sign) !== false) {
                        $spec = explode($sign, $product['techSpec'][$i]);
                        if (isset($spec[1]) && !empty(trim($spec[1]))) {
                            $response['techSpec'][$i] = [
                                'name' => trim($spec[0]),
                                'value' => trim($spec[1]),
                            ];
                        }
                    }
                }
            } else {$response['techSpec'] = [];}

            return $response;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * @param int $productId id товара
     * @return string путь к изображению
     */
    private function getImages(int $productId) {
        $img = ['path' => '/assets/images/products'];
        $icon = ['path' => '/assets/icons','name' => 'camera.svg'];
        $response = [];
        $images = $this->conn->prepare("SELECT * FROM `{$this->addTable}` WHERE `productId` = :productId");
        $images->execute(['productId' => $productId]);
        $images = $images->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($images) < 1) return ['main'=>"{$icon['path']}/{$icon['name']}"];

        $setMain = false;
        foreach ($images as $key => $image) {
            /* отбор главного изображение */
            if ((bool) $image['isMain'] && !$setMain) {
                $response['main'] = "{$img['path']}/{$image['image']}";
                $setMain = true;
            } else {
                $response['additional'][] = "{$img['path']}/{$image['image']}";
            }
        }

        if (!$setMain) /* если нет основого изображения */ {
            if (isset($response['additional']) && !empty($response['additional'])) {
                $response['main'] = $response['additional'][0];
            } else {
                $response['main'] = "{$icon['path']}/{$icon['name']}";
            }
        }

        return $response;
    }
}
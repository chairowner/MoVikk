<?php
/**
 * Класс продукта
 * @param int $id идентификатор продукта
 * @param PDO $conn соединение с БД
 */
class Product {
    public int $id = 0; // $id идентификатор продукта
    public PDO $conn; // соединение с БД
    private string $mainTable = 'products'; // таблица с продуктами
    private string $addTable = 'products_images'; // таблица с изображениями продуктов
    private string $companyTable = 'company'; // таблица с компаниями
    private string $countryTable = 'countries'; // таблица со странами
    private string $imagesPath = 'assets/images/products'; // путь к изображениям
    public array $fileTypes = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
    ]; // типы файлов

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
    
    /**
     * Функция добавляет товар
     * @param array $execute параметры товара
     * @return array
     */
    function add(array $execute) {
        try {
            $response =[
                'status' => false,
                'info' => []
            ];
            if (!isset($execute['name']) || !isset($execute['href'])) {
                $response['info'][] = 'Не указаны имя или ссылка на категорию';
                return $response;
            }

            $images = [];
            $images_count = 0;
            if (isset($execute['files']) && is_array($execute['files'])) {
                $images = $execute['files'];
                unset($execute['files']);
                $images_count = count($images['error']);
            }

            $sql =
            "INSERT INTO `{$this->mainTable}`
            (`categoryId`, `countryId`, `instructionId`, `href`, `name`, `description`, `height`, `width`, `length`, `features`, `techSpec`, `count`, `price`, `sale`)
            VALUES
            (:categoryId, :countryId, :instructionId, :href, :name, :description, :height, :width, :length, :features, :techSpec, :count, :price, :sale)";
            $query = $this->conn->prepare($sql);
            if ($query->execute($execute)) {
                $response['id'] = (int)$this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Товар добавлен!';
                if ($images_count > 0) {
                    $next = false;
                    if (is_dir(get_include_path().$this->imagesPath)) {
                        $next = true;
                    } elseif (mkdir(get_include_path().$this->imagesPath)) {
                        $next = true;
                    } else {
                        $response['info'][] = 'Ошибка добавления изображения';
                    }
                    if ($next) {
                        $query = "";
                        $values = $execute = [];
                        $query = "INSERT INTO `{$this->addTable}` (`productId`,`image`,`isMain`) VALUES ";
                        for ($i = 0; $i < $images_count; $i++) {
                            $isMain = $i === 0 ? 1 : 0;
                            $extension = pathinfo($images['name'][$i])['extension'];
                            $images['name'][$i] = md5($images['name'][$i].time()).".$extension";
                            if (move_uploaded_file($images['tmp_name'][$i], get_include_path()."{$this->imagesPath}/{$images['name'][$i]}")) {
                                // $values[] = "('{$response['id']}','{$images[$i]['name']}','{$isMain}')";
                                $values[] = "(:image{$i}Id,:image{$i}Name,:image{$i}isMain)";
                                $execute["image{$i}Id"] = $response['id'];
                                $execute["image{$i}Name"] = $images['name'][$i];
                                $execute["image{$i}isMain"] = $isMain;
                            }
                        }
                        if (count($execute) > 0) {
                            $query .= implode(",", $values);
                            $query = $this->conn->prepare($query);
                            $query->execute($execute);
                        }
                    }
                }
            } else {
                $response['info'][] = 'Не удалось добавить товар';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Ошибка добавления товара"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }

    /**
     * Функция редактирует товар
     * @param array $execute параметры товара
     * @return array
     */
    function edit(array $execute) {
        try {
            $response =[
                'status' => false,
                'info' => []
            ];
            if (!isset($execute['name']) || !isset($execute['href'])) {
                $response['info'][] = 'Не указаны имя или ссылка на категорию';
                return $response;
            }

            $images = [];
            $images_count = 0;
            if (isset($execute['files']) && is_array($execute['files'])) {
                $images = $execute['files'];
                unset($execute['files']);
                $images_count = count($images['error']);
            }

            $sql =
            "INSERT INTO `{$this->mainTable}`
            (`categoryId`, `countryId`, `instructionId`, `href`, `name`, `description`, `height`, `width`, `length`, `features`, `techSpec`, `count`, `price`, `sale`)
            VALUES
            (:categoryId, :countryId, :instructionId, :href, :name, :description, :height, :width, :length, :features, :techSpec, :count, :price, :sale)";
            $query = $this->conn->prepare($sql);
            if ($query->execute($execute)) {
                $response['id'] = (int)$this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Изменения сохранены!';
                if ($images_count > 0) {
                    $next = false;
                    if (is_dir(get_include_path().$this->imagesPath)) {
                        $next = true;
                    } elseif (mkdir(get_include_path().$this->imagesPath)) {
                        $next = true;
                    } else {
                        $response['info'][] = 'Ошибка добавления изображения';
                    }
                    if ($next) {
                        $query = "";
                        $values = $execute = [];
                        $query = "INSERT INTO `{$this->addTable}` (`productId`,`image`,`isMain`) VALUES ";
                        for ($i = 0; $i < $images_count; $i++) {
                            $isMain = $i === 0 ? 1 : 0;
                            $extension = pathinfo($images['name'][$i])['extension'];
                            $images['name'][$i] = md5($images['name'][$i].time()).".$extension";
                            if (move_uploaded_file($images['tmp_name'][$i], get_include_path()."{$this->imagesPath}/{$images['name'][$i]}")) {
                                // $values[] = "('{$response['id']}','{$images[$i]['name']}','{$isMain}')";
                                $values[] = "(:image{$i}Id,:image{$i}Name,:image{$i}isMain)";
                                $execute["image{$i}Id"] = $response['id'];
                                $execute["image{$i}Name"] = $images['name'][$i];
                                $execute["image{$i}isMain"] = $isMain;
                            }
                        }
                        if (count($execute) > 0) {
                            $query .= implode(",", $values);
                            $query = $this->conn->prepare($query);
                            $query->execute($execute);
                        }
                    }
                }
            } else {
                $response['info'][] = 'Не удалось редактировать товар';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Ошибка редактирования товара"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция удаляет товар
     * @param int $id id товара
     * @return array
     */
    function remove(int $id) {
        $response =[
            'status' => false,
            'info' => []
        ];
        try {
            $query = $this->conn->prepare("DELETE FROM `{$this->mainTable}` WHERE `id` = :id");
            $execute = ['id' => $id];
            if ($query->execute($execute)) {
                $response['status'] = true;
                $response['info'][] = 'Товар удалён';
                
                // удаление изображений
                $query = $this->conn->prepare("SELECT `image` FROM `{$this->addTable}` WHERE `productId` = :id");
                $execute = ['id' => $id];
                $query->execute($execute);
                $items = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($items) > 0) {
                    $images = [];
                    foreach ($items as $item_key => $item) {
                        $images[] = trim($item['image']);
                    }
                    
                    $query = $this->conn->prepare("DELETE FROM `{$this->addTable}` WHERE `productId` = :id");
                    $execute = ['id' => $id];
                    if ($query->execute($execute)) {
                        foreach ($images as $img_key => $img) {
                            unlink("/{$this->imagesPath}/$img");
                        }
                    }
                }
            } else {
                $response['info'][] = 'Не удалось удалить товар';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response['info'][] = "Системная ошибка: Не удалось удалить товар";
            if (DEBUG_MODE) $response['info'][] = $e->getMessage();
            return $response;
        }
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
    function getProduct(int $id, string $href = "", $isAdmin = false) {
        $href = trim($href);
        try {
            $response = ['id'=>$id,'notFound'=>true];
            $prepare = "SELECT `categoryId`,`countryId`,`instructionId`,`name`, `href`,`description`,`height`,`width`,`length`,`features`,`techSpec`,`count`,`price`,`sale`,`preOrder`,`keywords`,`sold`,`added`,`isDeleted` FROM `{$this->mainTable}` WHERE `id` = :id";
            $execute = ['id' => $id];
            if (!$isAdmin) {
                $prepare .= " AND `href` = :href";
                $execute['href'] = $href;
            }
            $product = $this->conn->prepare($prepare);
            $product->execute($execute);
            $product = $product->fetch(PDO::FETCH_ASSOC);

            if (!isset($product) || empty($product)) return $response;
            $response = null;

            $response['notFound'] = false;
            $response['id'] = $id;
            $response['categoryId'] = (int) ($product['categoryId']);
            $response['instructionId'] = isset($product['instructionId']) ?
                (int) ($product['instructionId']) : null;
            $response['href'] = trim($product['href']);
            $response['name'] = trim($product['name']);
            $response['description'] = isset($product['description']) ?
                trim($product['description']) : null;
            $response['images'] = $this->getImages($id, $isAdmin);
            $response['count'] = (int) ($product['count']);
            $response['price'] = (float)$product['price'];
            $response['sale'] = (float)$product['sale'];
            $response['preOrder'] = (int) ($product['preOrder']) === 1 ? true : false;
            $response['sold'] = (int) ($product['sold']);
            $response['keywords'] = trim($product['keywords']);
            $response['added'] = new DateTime($product['added']);
            $response['isDeleted'] = (int) ($product['isDeleted']) === 1 ? true : false;
            $country = $this->conn->prepare("SELECT `name` FROM `{$this->countryTable}` WHERE `id` = :id");
            $country->execute(['id' => (int) $product['countryId']]);
            $country = $country->fetch(PDO::FETCH_ASSOC);
            $response['countryId'] = isset($product['countryId']) ? (int)$product['countryId'] : null;
            $response['country'] = isset($country['name']) ? trim($country['name']) : null;
            
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
     * @param bool $isAdmin admin?
     * @return string путь к изображению
     */
    private function getImages(int $productId, bool $isAdmin = false) {
        $img = ['path' => "/{$this->imagesPath}"];
        $icon = ['path' => '/assets/icons','name' => 'camera.svg'];
        $response = ['main'=>"",'additional'=>[],'notFound' => true];
        $images = $this->conn->prepare("SELECT * FROM `{$this->addTable}` WHERE `productId` = :productId");
        $images->execute(['productId' => $productId]);
        $images = $images->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($images) < 1) {
            if (!$isAdmin) $response['main'] = "{$icon['path']}/{$icon['name']}";
            return $response;
        }
        $response['notFound'] = false;

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
                unset($response['additional'][0]);
                if (isset($response['additional']) && !empty($response['additional'])) $response['additional'] = array_values($response['additional']);
                else $response['additional'] = [];
            } else {
                $response['main'] = "{$icon['path']}/{$icon['name']}";
                $response['additional'] = [];
            }
        }

        return $response;
    }
}
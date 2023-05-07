<?php
/**
 * Класс корзины
 * @param int $id идентификатор пользователя
 * @param PDO $conn подключение к БД
 * @param string $mainTable основная таблица
 */
class Cart {
    private PDO $conn;
    private string $mainTable = 'carts';
    private string $addTable = 'products';
    private string $productImagesTable = 'products_images'; // таблица с изображениями продуктов

    /**
     * @param PDO $conn подключение к БД
     * @param array $session данные сессии
     */
    function __construct(PDO $conn = null, array $session = null) {
        if (isset($conn)) {
            $this->conn = $conn;
        }
    }
    
    /**
     * Количество товаров в корзине
     * @param int $userId id пользователя
     * @param int $limit лимит для запроса
     * @return array 
     */
    public function getProducts(int $userId, int $limit = 0) {
        $response = [];
        try {
            $sql = "SELECT `p`.`name`, `p`.`price` `oldPrice`, `p`.`sale`, `p`.`href`, `p`.`count` `availableCount`, `c`.`productId`, `c`.`count` `countInCart`  FROM `{$this->mainTable}` `c` INNER JOIN `{$this->addTable}` `p` ON `p`.`id` = `c`.`productId` WHERE `userId` = :userId";
            $sqlExecute = ['userId' => $userId];
            if ($limit > 0) {
                $sql .= " LIMIT :limit";
                $sqlExecute['limit'] = $limit;
            }
            $cart = $this->conn->prepare($sql);
            $cart->execute($sqlExecute);
            $cart = $cart->fetchAll(PDO::FETCH_ASSOC);
            for ($i=0; $i < count($cart); $i++) {
                $cart[$i]['productId'] = (int) $cart[$i]['productId'];
                $cart[$i]['href'] = '/product/'.trim($cart[$i]['href'])."-".$cart[$i]['productId'];
                $cart[$i]['name'] = trim($cart[$i]['name']);
                $cart[$i]['oldPrice'] = (float) $cart[$i]['oldPrice'];
                $cart[$i]['sale'] = (float) $cart[$i]['sale'];
                $cart[$i]['countInCart'] = (int) $cart[$i]['countInCart'];
                $cart[$i]['availableCount'] = (int) $cart[$i]['availableCount'];
                $response[$i] = [
                    'name' => $cart[$i]['name'], // название продукта
                    // 'sale' => $cart[$i]['sale'], // скидка, %
                    'oldPrice' => $cart[$i]['oldPrice'], // старая цена
                    'currentPrice' => $cart[$i]['oldPrice'] - ($cart[$i]['oldPrice'] * $cart[$i]['sale'] / 100), // актуальная цена
                    'productId' => $cart[$i]['productId'], // id продукта
                    'countInCart' => $cart[$i]['countInCart'], // кол-во в корзине
                    'availableCount' => $cart[$i]['availableCount'], // кол-во в наличии
                    'href' => $cart[$i]['href'], // ссылка на товар
                ];
                if ($response[$i]['oldPrice'] == $response[$i]['currentPrice']) $response[$i]['oldPrice'] = null;
                $response[$i]['image'] = $this->getMainImage($cart[$i]['productId']);
            }
        } catch (PDOException $e) {
            $response = [$e->getMessage()];
        }
        return $response;
    }

    /**
     * @param int $productId id товара
     * @return string путь к изображению
     */
    private function getMainImage(int $productId) {
        $img = [
            'path' => '/assets/images/products',
            'name' => null,
            'additional' => []
        ];
        $icon = [
            'path' => '/assets/icons',
            'name' => 'camera.svg'
        ];
        
        $images = $this->conn->prepare("SELECT * FROM `{$this->productImagesTable}` WHERE `productId` = :productId");
        $images->execute(['productId' => $productId]);
        $images = $images->fetchAll(PDO::FETCH_ASSOC);

        if (count($images) < 1) return "{$icon['path']}/{$icon['name']}";
        
        $setMain = false;
        foreach ($images as $key => $image) {
            $img['name'] = trim($image['image']);
            /* отбор главного изображение */
            if ((bool) $image['isMain'] && !$setMain) {
                $img['name'] = $image['image'];
                $setMain = true;
                break;
            } else {
                $img['additional'][] = $image['image'];
            }
        }

        if (!$setMain) /* если нет основого изображения */ {
            if (isset($img['additional']) && !empty($img['additional'])) {
                $img['name'] = $img['additional'][0];
                unset($img['additional']);
            }
        }

        return "{$img['path']}/{$img['name']}";
    }

    /**
     * Количество товаров в корзине
     * @param int $userId id пользователя
     * @param int $maxCount максимальное отображаемое число
     * @param bool $showPlus отобразить с плюсом
     * @return string 
     */
    public function getCount(int $userId, int $maxCount = 10, bool $showPlus = true) {
        $response = 0;
        try {
            $cartCount = $this->conn->prepare("SELECT `productId`, `count` FROM `{$this->mainTable}` WHERE `userId` = :userId");
            $cartCount->execute(['userId' => $userId]);
            $cartCount = $cartCount->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cartCount as $key => $value) {
                if ($value['count'] > 0) $response += (int) $value['count'];
                if ($maxCount > 0 && $response > $maxCount) break;
            }
            # отпределяемся, ставить ли "+"
            if ($maxCount > 0 && $response > $maxCount) {
                if ($showPlus) {
                    $response = "$maxCount+";
                } else {
                    $response = (string) $response;
                }
            } else {
                $response = (string) $response;
            }
            unset($cartCount);
        } catch (PDOException $e) {
            $response = 0;
        }
        return $response;
    }
    
    /**
     * Добавить товар в корзину
     * @param int $userId id пользователя
     * @param int $productId id продукта
     * @param int $count количество
     * @return string 
     */
    public function add(int $userId, int $productId, int $count = 1) {
        $response = ['status' => false,'info' => 'Товар не найден'];
        try {
            // $product = $this->conn->prepare("SELECT `productId`, `count` FROM `{$this->mainTable}` WHERE `userId` = :userId AND `productId` = :productId");
            $product = $this->conn->prepare("SELECT `price`, `preOrder`, `isDeleted`, `count` FROM `{$this->addTable}` WHERE `id` = :productId");
            $product->execute(['productId' => $productId]);
            $product = $product->fetch(PDO::FETCH_ASSOC);
            if (isset($product) && !empty($product)) {
                $product['count'] = (int) $product['count'];
                $product['preOrder'] = (bool) $product['preOrder'];
                $product['isDeleted'] = (bool) $product['isDeleted'];

                $add = true;

                if ($product['isDeleted']) $response['info'] = 'Товар удалён';
                if ($product['count'] < 1) {
                    $add = false;
                    $response['info'] = 'Товар закончился';
                    // if ($product['preOrder']) {$add = true; $response['info'] = null;}
                }

                if ($add) {
                    $cart = $this->conn->prepare("SELECT * FROM `{$this->mainTable}` WHERE `userId` = :userId AND `productId` = :productId");
                    $cart->execute(['userId' => $userId,'productId' => $productId]);
                    $cart = $cart->fetch(PDO::FETCH_ASSOC);
                    if (isset($cart) && !empty($cart)) /* продукт есть в корзине */ {
                        $newCount = (int) $cart['count'] + $count;
                        $entryId = (int) $cart['id'];
                        $cartExecute = ['entryId' => $entryId, 'count' => $newCount];
                        $cart = "UPDATE `{$this->mainTable}` SET `count` = :count WHERE `id` = :entryId";
                    } else /* продукта нет в корзине */ {
                        $newCount = $count;
                        $cartExecute = ['userId' => $userId, 'productId' => $productId, 'count' => $newCount];
                        $cart = "INSERT INTO `{$this->mainTable}` (`userId`, `productId`, `count`) VALUES (:userId, :productId, :count)";
                    }
                    if ($cartExecute['count'] <= $product['count']) {
                        $cart = $this->conn->prepare($cart);
                        if ($cart->execute($cartExecute)) {
                            $query = "SELECT `price`, `sale` FROM `{$this->addTable}` WHERE `id` = :id";
                            $query = $this->conn->prepare($query);
                            $query->execute(['id' => $productId]);
                            $query = $query->fetch(PDO::FETCH_ASSOC);
                            $query['price'] = (float)$query['price'];
                            $query['sale'] = (int)$query['sale'];
                            $response = [
                                'status' => true,
                                'count' => $newCount,
                                'oldPrice' => $query['price'],
                                'currentPrice' => ($query['price'] - ($query['price'] * $query['sale'] / 100)),
                                'info' => null
                            ];//'Товар добавлен в корзину'];
                        } else {
                            $response = ['status' => false,'info' => "При добавлении товара возникла внутренняя ошибка\nДля заказа можно позвонить нам или написать в ВК, WhatsApp или Viber c:"];
                        }
                    } else {
                        $response = ['status' => false,'info' => 'Вы пытаетесь добавить в корзину больше единиц товара, чем у нас есть'];
                    }
                }
            } else {
                $response['info'] = 'Товар не найден: ';
            }
        } catch (PDOException $e) {
            $response = [
                'status' => false,
                'info' => "При добавлении товара возникла внутренняя ошибка\nДля заказа можно позвонить нам или написать в ВК, WhatsApp или Viber c:",
                // 'error'=>$e->getMessage()
            ];
        }
        return $response;
    }
    
    /**
     * Удалить товар из корзину
     * @param int $userId id пользователя
     * @param int $productId id продукта
     * @param int $count количество
     * @return string 
     */
    public function remove(int $userId, int $productId, int $count = 1) {
        $response = ['status' => false,'info' => 'Не удалось убрать товар'];
        try {
            $cart = $this->conn->prepare("SELECT * FROM `{$this->mainTable}` WHERE `userId` = :userId AND `productId` = :productId");
            $cart->execute(['userId' => $userId,'productId' => $productId]);
            $cart = $cart->fetch(PDO::FETCH_ASSOC);
            if (isset($cart) && !empty($cart)) /* продукт есть в корзине */ {
                $newCount = (int) $cart['count'] - $count;
                if ($count <= 0) $newCount = 0;
                $entryId = (int) $cart['id'];

                $cartExecute = ['entryId' => $entryId, 'count' => $newCount];
                $cart = "UPDATE `{$this->mainTable}` SET `count` = :count WHERE `id` = :entryId";

                if ($newCount <= 0) {
                    $newCount = 0;
                    $cartExecute = ['entryId' => $entryId];
                    $cart = "DELETE FROM `{$this->mainTable}` WHERE `id` = :entryId";
                } 
                $cart = $this->conn->prepare($cart);
                if ($cart->execute($cartExecute)) {
                    $query = "SELECT `price`, `sale` FROM `{$this->addTable}` WHERE `id` = :id";
                    $query = $this->conn->prepare($query);
                    $query->execute(['id' => $productId]);
                    $query = $query->fetch(PDO::FETCH_ASSOC);
                    $query['price'] = (float)$query['price'];
                    $query['sale'] = (int)$query['sale'];
                    $response = [
                        'status' => true,
                        'count' => $newCount,
                        'oldPrice' => $query['price'],
                        'currentPrice' => ($query['price'] - ($query['price'] * $query['sale'] / 100)),
                        'info' => null
                    ];
                } else {
                    $response = ['status' => false,'info' => "При удалении товара из корзины возникла внутренняя ошибка\nДля заказа можно позвонить нам или написать в ВК, WhatsApp или Viber c:"];
                }
            } else /* продукта нет в корзине */ {
                $response = ['status' => false,'info' => "Товар не находится в вашей корзине"];
            }
        } catch (PDOException $e) {
            $response = [
                'status' => false,
                'info' => "При удалении товара из корзины возникла внутренняя ошибка\nДля заказа можно позвонить нам или написать в ВК, WhatsApp или Viber c:",
                // 'error'=>$e->getMessage()
            ];
        }
        return $response;
    }
    
    /**
     * Удалить все товары из корзину
     * @param int $userId id пользователя
     * @return string 
     */
    public function RemoveAll(int $userId) {
        $response = ['status' => false,'info' => ['Не удалось убрать товары']];
        try {
            $cart = $this->conn->prepare("DELETE FROM `{$this->mainTable}` WHERE `userId` = :userId");
            if ($cart->execute(['userId' => $userId])) /* продукт есть в корзине */ {
                $response = [
                    'status' => true,
                    'count' => 0,
                    'info' => ['Товары удалены из корзины']
                ];
            } else /* продукта нет в корзине */ {
                $response = ['status' => false,'info' => ["Корзина пуста"]];
            }
        } catch (PDOException $e) {
            $response = [
                'status' => false,
                'info' => ["При удалении товаров из корзины возникла внутренняя ошибка"],
            ];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
        }
        return $response;
    }

    public function checkProduct(int $userId, int $productId) {
        try {
            $cart = $this->conn->prepare("SELECT `count` FROM `{$this->mainTable}` WHERE `userId` = :userId AND `productId` = :productId");
            $cart->execute(['userId' => $userId,'productId' => $productId]);
            $cart = $cart->fetch(PDO::FETCH_ASSOC);
            if (isset($cart) && !empty($cart)) return ['status'=>true,'count'=>(int)$cart['count']];
            return ['status'=>false,'count'=>0];
        } catch (\Throwable $th) {
            return ['status'=>false,'count'=>0];
        }
    }

    public function getCartData(int $userId) {
        try {
            $cart = $this->conn->prepare("SELECT p.price oldPrice, (p.price - (p.price * p.sale / 100)) currentPrice, (p.price - (p.price - (p.price * p.sale / 100))) salePrice, c.count countInCart FROM carts c INNER JOIN products p ON p.id = c.productId WHERE c.userId = :userId");
            $cart->execute(['userId' => $userId]);
            $cart = $cart->fetchAll(PDO::FETCH_ASSOC);
            $response = ['status'=>true,'products' => []];
            if ($cart < 1) return $response;
            for ($i=0; $i < count($cart); $i++) { 
                $response['products'][] = [
                    'oldPrice' => (float) $cart[$i]['oldPrice'],
                    'currentPrice' => (float) $cart[$i]['currentPrice'],
                    'salePrice' => (float) $cart[$i]['salePrice'],
                    'countInCart' => (int) $cart[$i]['countInCart']
                ];
            }
            return $response;
        } catch (Exception $ex) {
            return ['status'=>false,'products' => [], 'info'=>"Не удалось получить данные вашей корзины"];
        }
    }
}
<?php
/**
 * Класс заказов
 * @param PDO $conn подключение к БД
 * @param string $mainTable основная таблица
 */
class Order {
    private PDO $conn;
    private string $mainTable = 'orders';
    private string $dataTable = 'orders_data';
    private string $productTable = 'products';
    private string $userTable = 'users';
    private string $cartTable = 'carts';

    /**
     * @param PDO $conn подключение к БД
     * @param array $session данные сессии
     */
    function __construct(PDO $conn = null) {
        if (isset($conn)) {
            $this->conn = $conn;
        }
    }
    
    /**
     * Получить название таблицы
     * @return string
     */
    public function GetTable() {
        return $this->mainTable;
    }

    /**
     * Оформление заказа
     * @param int $userId id пользователя
     * @param string $phone номер телефона
     * @param string $address адрес доставки
     * @param string|null  $userComment комментарий пользователя
     * @return array [bool $status, array(string) $info]
     */
    public function Create(int $userId, string $phone, string $fullName, string $address, string|null $userComment = null) {
        $response = [
            'status' => false,
            'info' => [],
            'payment' => []
        ];

        if ($userId > 0) {
            $query = $this->conn->prepare("SELECT `id` FROM `users` WHERE `id` = :id");
            if ($query->execute(['id' => $userId])) {
                if ($query->fetch(PDO::FETCH_ASSOC) == null) {
                    $response['info'][] = 'Пользователь не найден';
                }
            } else {
                $response['info'][] = 'Ошибка: Не удалость найти пользователя';
            }
            unset($query);
        } else {
            $response['info'][] = 'Неверный ID пользователя';
        }

        if (isset($phone)) {
            $phone = trim($phone);
            if ($phone === "") {
                $phone = null;
                $response['info'][] = "Контактный телефон не может быть пустым";
            }
        } else {
            $phone = null;
            $response['info'][] = "Заполните контактный телефон";
        }

        if (isset($fullName)) {
            $fullName = trim($fullName);
            if ($fullName === "") {
                $fullName = null;
                $response['info'][] = "ФИО не может быть пустым";
            }
        } else {
            $fullName = null;
            $response['info'][] = "Заполните ФИО";
        }

        if (isset($address)) {
            $address = trim($address);
            if ($address === "") {
                $address = null;
                $response['info'][] = "Адрес не может быть пустым";
            }
        } else {
            $address = null;
            $response['info'][] = "Заполните адрес";
        }

        if (isset($userComment)) {
            $userComment = trim($userComment);
            if ($userComment === "") {
                $userComment = null;
            }
        }
        
        if (count($response['info']) === 0) {
            try {
                // берём товары из корзины
                $cart = $this->conn->prepare("SELECT `p`.`name` `productName`, `c`.`productId`, `c`.`count` `cartCount`, `p`.`count` `productCount`, `p`.`price`, `p`.`sale` FROM `{$this->cartTable}` `c` INNER JOIN `{$this->productTable}` `p` ON `p`.`id` = `c`.`productId` WHERE `userId` = :userId");

                if ($cart->execute(['userId' => $userId])) {
                    $cart = $cart->fetchAll(PDO::FETCH_ASSOC);
                    $cart_count = count($cart);

                    if ($cart_count > 0) /* если в корзине пользователя есть товары */ {
                        // проверяем товар на наличие
                        for ($i = 0; $i < $cart_count; $i++) {
                            $cart[$i]['productName'] = trim($cart[$i]['productName']);
                            $cart[$i]['productId'] = (int) $cart[$i]['productId'];
                            $cart[$i]['productCount'] = (int) $cart[$i]['productCount'];
                            $cart[$i]['price'] = (float) $cart[$i]['price'];
                            $cart[$i]['sale'] = (int) $cart[$i]['sale'];
                            $cart[$i]['cartCount'] = (int) $cart[$i]['cartCount'];

                            if ($cart[$i]['cartCount'] > $cart[$i]['productCount']) /* если товаров в корзине больше, чем есть в наличии */ {
                                if (empty($response['info'])) {
                                    $response['info'][] = 'Не удалось оформить заказ - В наличии нет нужного количества товара (пересоберите корзину)';
                                }

                                $response['info'][] = "{$cart[$i]['name']}: в наличии - {$cart[$i]['productCount']}, в корзине - {$cart[$i]['cartCount']}";
                            }
                        }

                        if (empty($response['info'])) /* если товары есть в наличии*/ {
                            $next = true;

                            // удаляем единицы товара из таблицы с товарами
                            for ($i = 0; $i < $cart_count; $i++) {
                                $query = $this->conn->prepare("UPDATE `{$this->productTable}` SET `count` = :count WHERE `id` = :productId");
                                $query->execute(['count' => ($cart[$i]['productCount'] - $cart[$i]['cartCount']), 'productId' => $cart[$i]['productId']]);
                            }
                            
                            if ($next) /* успех */ {
                                // создаём заказ
                                $query = "INSERT INTO `{$this->mainTable}`(`userId`, `phone`, `fullName`, `address`, `userComment`) VALUES (:userId, :phone, :fullName, :address, :userComment)";
                                $query = $this->conn->prepare($query);

                                if ($query->execute(['userId' => $userId, 'phone' => $phone, 'fullName' => $fullName, 'address' => $address, 'userComment' => $userComment])) /* если заказ создался */ {
                                    // берём ID этого заказа
                                    $orderId = (int) $this->conn->lastInsertId();

                                    // добавляем товары к заказу
                                    $query = "INSERT INTO `{$this->dataTable}`(`orderId`, `productId`, `buyPrice`, `count`) VALUES";
                                    $execute = ['orderId' => $orderId];

                                    // перебор товаров с формированием запроса
                                    for ($i = 0; $i < $cart_count; $i++) {
                                        if ($i > 0) $query .= ",";

                                        $query .= " (:orderId, :{$i}productId, :{$i}buyPrice, :{$i}count)";
                                        $execute["{$i}buyPrice"] = (float) $this->GetCorrectPriceFormat($cart[$i]['price'] - ($cart[$i]['price'] * $cart[$i]['sale'] / 100));
                                        $execute["{$i}productId"] = $cart[$i]['productId'];
                                        $execute["{$i}count"] = $cart[$i]['cartCount'];
                                    }

                                    $query = $this->conn->prepare($query);

                                    if ($query->execute($execute)) /* если товары добавились к заказу */ {
                                        $response['status'] = true;
                                        $response['info'][] = "Заказ оформлен";

                                        try {
                                            $payment = $this->Tinkoff_Init($orderId);
                                            $payment = $payment['request'];
                                            $response['payment'] = [
                                                "Success" => $payment['Success'],
                                                "Status" => $payment['Status'],
                                                "PaymentId" => null,
                                                "Details" => null,
                                                "PaymentURL" => null,
                                            ];
                                            if ($response['payment']['Success']) {
                                                $response['payment']['PaymentURL'] = $payment['PaymentURL'];
                                                $response['payment']['PaymentId'] = $payment['PaymentId'];
                                                $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `payment_id` = :PaymentId, `payment_link` = :payment_link WHERE `id` = :orderId");
                                                $query->execute([
                                                    'orderId' => $orderId,
                                                    'PaymentId' => $response['payment']['PaymentId'],
                                                    'payment_link' => $response['payment']['PaymentURL'],
                                                ]);
                                            } else {
                                                $response['payment']['Details'] = $payment['Details'];
                                            }
                                        } catch (\Throwable $th) {
                                            $response['info'][] = "Не удалось перейти на страницу оплаты";
                                            if (DEBUG_MODE) {
                                                $response['info'][] = $th->getMessage();
                                            }
                                        }

                                        // удаляем единицы товара из магазина
                                        for ($i = 0; $i < $cart_count; $i++) {
                                            // берём актуальные параметры (кол-во товара и продажи (то, сколько раз его купили))
                                            $current_product = $this->conn->prepare("SELECT `count`, `sold` FROM `{$this->productTable}` WHERE `id` = :productId");

                                            if ($current_product->execute(['productId' => $cart[$i]['productId']])) {
                                                // текущее кол-во товара
                                                $current_product = $current_product->fetch(PDO::FETCH_ASSOC);

                                                if (isset($current_product['count'])) {
                                                    $current_product['count'] = (int) $current_product['count'];
                                                    $current_product['count'] -= $cart[$i]['cartCount'];

                                                    if ($current_product['count'] < 0) $current_product['count'] = 0; 
                                                } else {
                                                    $current_product['count'] = 0;
                                                }

                                                if (isset($current_product['sold'])) {
                                                    $current_product['sold'] = (int) $current_product['sold'];
                                                    $current_product['sold'] += $cart[$i]['cartCount'];

                                                    if ($current_product['sold'] < 0) $current_product['sold'] = 0;
                                                } else {
                                                    $current_product['sold'] = 0;
                                                }

                                                // обновляем кол-во и продажи товара
                                                $query = $this->conn->prepare("UPDATE `{$this->productTable}` SET `sold` = :sold WHERE `id` = :productId");

                                                if (!$query->execute(['sold' => $current_product['sold'], 'productId' => $cart[$i]['productId']])) {
                                                    if (DEBUG_MODE) {
                                                        $response['info'][] = "Не удалось поменять количество товара ID {$cart[$i]['productId']} #1";
                                                    }
                                                }
                                            } else {
                                                $query = $this->conn->prepare("UPDATE `{$this->productTable}` SET `count` = :count WHERE `id` = :productId");

                                                if (!$query->execute(['count' => $cart[$i]['productCount'], 'productId' => $cart[$i]['productId']])) {
                                                    if (DEBUG_MODE) {
                                                        $response['info'][] = "Не удалось поменять количество товара ID {$cart[$i]['productId']} #2";
                                                    }
                                                }
                                            }
                                        }
                                    } else /* если не удалось добавить товары к заказу */ {
                                        $response['info'][] = "Не удалось оформить заказ";
                                        $response['info'][] = "При занесении товаров в заказ произошла ошибка";

                                        // удаляем заказ, если не добавились товары
                                        $query = $this->conn->prepare("DELETE FROM `{$this->mainTable}` WHERE `id` = :orderId");

                                        if ($query->execute(['orderId' => $orderId])) {
                                            $response['info'][] = 'Ошибка заказа: заказ не принят';
                                        } else {
                                            $response['info'][] = 'Не удалось удалить заказ';
                                            $response['info'][] = 'Ошибка при удалении заказа';
                                        }

                                        $response['info'][] = 'Для уточнения информации свяжитесь с нами или повторите оформление';
                                    }
                                } else {
                                    $response['info'][] = 'Не удалось оформить заказ';
                                    $response['info'][] = 'Ошибка при создании заказа';
                                }
                            } else /* что-то с БД */ {
                                $response['info'][] = 'Не удалось оформить заказ';
                                $response['info'][] = "Не удалось ";
                            }
                        }

                    } else {
                        $response['info'][] = "Не удалось оформить заказ";
                        $response['info'][] = "Корзина пуста";
                    }

                } else {
                    $response['info'][] = "Не удалось оформить заказ";
                    $response['info'][] = "Не удалось взять товары из корзины";
                }

            } catch (PDOException $ex) {
                $response['info'][] = "При оформлении заказа произошла ошибка";
                if (DEBUG_MODE) {
                    $response['info'][] = $ex->getMessage();
                }
            }

        }

        return $response;
    }

    /**
     * Формирование Receipt для Тинькофф
     */
    public function CreateReceiptArray(array $products) {
        $Receipt_Items = [];
        if (count($products) < 1) return $Receipt_Items;
        foreach ($products as $productId => $product) {
            $product['buyPrice'] = $product['buyPrice'] * 100;
            $Receipt_Items[] = [
                "Name" => trim($product['name']), // Наименование товара
                "Quantity" => $product['count'], // Количество или вес товара
                "Amount" => $product['buyPrice'] * $product['count'], // Стоимость товара в копейках
                "Price" => $product['buyPrice'], // Цена за единицу товара в копейках
                "PaymentMethod" => "full_payment",
                "PaymentObject" => trim($product['payment_object_code']),
                "Tax" => "none"
            ];
        }
        return $Receipt_Items;
    }

    /**
     * Получить данные о заказах
     * @param int $id id пользователя/заказа
     * @return array ['noOrders' - bool, 'orders' - array, где keys - id]
     */
    public function GetOrders(string $load, int $id = 0, bool $isPaymentId = false) {
        $response = [
            'noOrders' => true,
            'orders' => [],
        ];
        
        $closedStatuses = $this->GetStatusArray('close');
        $closedStatuses = implode(',',$closedStatuses);
        
        $loadValues = ['active','history','all','one'];
        $load = trim($load);
        if (!in_array($load,$loadValues)) return $response; // return
        
        $sql = "SELECT o.id, o.status, o.added, o.closed, o.tracking, o.deliveryId, o.isClosed, o.userComment, o.adminComment, o.phone, o.address, od.buyPrice productPrice, od.productId, p.href productHref, p.name productName, od.count productCount, o.fullName order_fullName, u.surname user_surname, po.code payment_object_code, u.name user_name, u.patronymic user_patronymic, o.payment_link, o.payment_id FROM {$this->mainTable} o INNER JOIN {$this->dataTable} od ON od.orderId = o.id INNER JOIN {$this->productTable} p ON p.id = od.productId INNER JOIN payment_objects po ON po.id = p.payment_object_id INNER JOIN {$this->userTable} u ON u.id = o.userId";
        $execute = [];

        if ($load === 'one') {
            if ($isPaymentId) {
                $sql .= " WHERE o.payment_id = :paymentId";
                $execute['paymentId'] = $id;
            } else {
                $sql .= " WHERE o.id = :id";
                $execute['id'] = $id;
            }
        } else {
            if ($load === 'active') $sql .= " AND o.status NOT IN($closedStatuses)";
            elseif ($load === 'history') $sql .= " AND o.status IN($closedStatuses)";
        }

        $sql .= " ORDER BY o.id DESC";
        
        $orders = $this->conn->prepare($sql);
        $orders->execute($execute);
        $orders = $orders->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($orders) < 1) return $response; // return
        
        $response['noOrders'] = false;
        
        $lastOrderId = 0;
        $index = -1;
        foreach ($orders as $key => $order) {
            $order['id'] = (int) $order['id'];
            $order['productId'] = (int) $order['productId'];
            $order['productCount'] = (int) $order['productCount'];
            $order['productPrice'] = (float) $order['productPrice'];
            if ($lastOrderId !== $order['id']) {
                $lastOrderId = $order['id'];
                $index++;
            }
            $response['orders'][$index]['id'] = $order['id'];
            $response['orders'][$index]['price'] =
                isset($response['orders'][$index]['price']) && (float) $response['orders'][$index]['price'] >= 0 ?
                    ($response['orders'][$index]['price'] + ($order['productPrice'] * $order['productCount'])) :
                    ($order['productPrice'] * $order['productCount']);
            $response['orders'][$index]['status'] = $order['status'];
            $response['orders'][$index]['address'] =
                isset($order['address']) && trim($order['address']) !== "" ?
                    trim($order['address']) : null;
            $response['orders'][$index]['phone'] =
                isset($order['phone']) && trim($order['phone']) !== "" ?
                    trim($order['phone']) : null;
            $response['orders'][$index]['tracking'] =
                isset($order['tracking']) && trim($order['tracking']) !== "" ?
                    trim($order['tracking']) : null;
            $response['orders'][$index]['added'] =
                isset($order['added']) && trim($order['added']) !== "" ?
                    trim($order['added']) : null;
            $response['orders'][$index]['closed'] =
                isset($order['closed']) && trim($order['closed']) !== "" ?
                    trim($order['closed']) : null;
            $response['orders'][$index]['isClosed'] = (bool) $order['isClosed'];
            $response['orders'][$index]['userComment'] =
                isset($order['userComment']) && trim($order['userComment']) !== "" ?
                    trim($order['userComment']) : null;
            $response['orders'][$index]['adminComment'] =
                isset($order['adminComment']) && trim($order['adminComment']) !== "" ?
                    trim($order['adminComment']) : null;
            $response['orders'][$index]['idWhoClosed'] =
                isset($order['idWhoClosed']) ?
                    (int) $order['idWhoClosed'] : null;
            $response['orders'][$index]['payment_id'] =
                isset($order['payment_id']) && trim($order['payment_id']) !== "" ?
                    trim($order['payment_id']) : null;
            $response['orders'][$index]['payment_link'] =
                isset($order['payment_link']) && trim($order['payment_link']) !== "" ?
                    trim($order['payment_link']) : null;

            $payment_object_code = isset($order['payment_object_code']) && trim($order['payment_object_code']) !== "" ? trim($order['payment_object_code']) : null;

            $response['orders'][$index]['products'][$order['productId']] = [
                'name' => $order['productName'],
                'count' => $order['productCount'],
                'buyPrice' => $order['productPrice'],
                'href' => "/product/{$order['productHref']}-{$order['productId']}",
                'payment_object_code' => $payment_object_code
            ];
            
            $image = $this->conn->prepare("SELECT `image` FROM `products_images` WHERE `productId` = :productId AND `isMain` = 1");
            $image->execute(['productId' => $order['productId']]);
            $image = $image->fetch(PDO::FETCH_ASSOC);
            if (isset($image) && !empty($image)) {
                $response['orders'][$index]['products'][$order['productId']]['image']['name'] = trim($image['image']);
                $response['orders'][$index]['products'][$order['productId']]['image']['path'] = "/assets/images/products/";
            } else {
                $response['orders'][$index]['products'][$order['productId']]['image']['name'] = "camera.svg";
                $response['orders'][$index]['products'][$order['productId']]['image']['path'] = "/assets/icons/";
            }

            $order['deliveryId'] = (int) $order['deliveryId'];
            $delivery = $this->conn->prepare("SELECT `name`, link FROM deliveries WHERE id = :deliveryId");
            $delivery->execute(['deliveryId' => $order['deliveryId']]);
            $delivery = $delivery->fetch(PDO::FETCH_ASSOC);
            if (isset($delivery) && !empty($delivery)) {
                $response['orders'][$index]['delivery']['id'] = $order['deliveryId'];
                $response['orders'][$index]['delivery']['name'] =
                    isset($delivery['name']) && trim($delivery['name']) != "" ?
                        trim($delivery['name']) : null;
                $response['orders'][$index]['delivery']['link'] =
                    isset($delivery['link']) && trim($delivery['link']) != "" ?
                        trim($delivery['link']) : null;
            } else {
                $response['orders'][$index]['delivery']['id'] = null;
                $response['orders'][$index]['delivery']['name'] = null;
                $response['orders'][$index]['delivery']['link'] = null;
            }
        }
        
        return $response; // return
    }

    public function GetPaymentId(string $orderId) : array
    {
        $prepare = "SELECT `payment_id` FROM `{$this->mainTable}`";
        $execute = [];
        if ($orderId !== "all") {
            $orderId = (int) $orderId;
            if ($orderId < 1) return [];
            $prepare .= " WHERE `id` = :orderId";
            $execute['orderId'] = $orderId;
        }

        $query = $this->conn->prepare($prepare);
        $query->execute($execute);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Создание токена для заказа
     * @param string $TerminalPassword пароль от терминала
     * @param string $PaymentId номер оплаты
     * @param string $TerminalKey ключ от терминала
     * @return string
     */
    public function GenerateOrderToken(string $TerminalPassword, string $PaymentId, string $TerminalKey) : string {
        $algo = "sha256";
        return hash($algo, $TerminalPassword.$PaymentId.$TerminalKey);
    }

    /**
     * Берём данные терминала
     * @return array|null
     */
    public function GetTerminalData() {
        try {
            $terminal = $this->conn->prepare("SELECT * FROM `tinkoff_settings`");
            $terminal->execute();
            $terminal = $terminal->fetch(PDO::FETCH_ASSOC);
            return $terminal;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function Tinkoff_Init(int $orderId) : array {
        $response = [
            'status' => false,
            'info' => [],
        ];
        
        if ($orderId < 1) {
            $response['info'][] = "Неверный номер заказа";
            return $response;
        }

        $terminal = $this->GetTerminalData();
        if (!isset($terminal) || empty($terminal)) {
            $response['info'][] = "В системе нет настроек терминала";
            return $response;
        }

        $terminal['TerminalKey'] = trim($terminal['TerminalKey']);
        $terminal['TerminalPassword'] = trim($terminal['TerminalPassword']);

        // берём данные заказа
        $order = $this->GetOrders('one', $orderId);
        
        if ($order['noOrders']) {
            $response['info'][] = "Заказ не найден";
            return $response;
        }

        $order_description = "Покупка в интернет-магазине MoVikk";
        $order = $order['orders'][0];
        $order['phone'] = trim($order['phone']);
        $Receipt_Items = $this->CreateReceiptArray($order['products']);
        
        $data = [
            "TerminalKey" => $terminal['TerminalKey'],
            "Amount" => $order['price'] * 100, // в копейках
            "OrderId" => $order['id'],
            "Description" => $order_description,
            "PayType" => 'O',
            "Receipt" => [
                "Phone" => $order['phone'],
                "Taxation" => $terminal['Taxation'],
                "Items" => $Receipt_Items,
            ],
        ];
        
        $response['request'] = $this->SendRequest('Init', $data);
        if ($response['request']['Success']) {
            $response['status'] = true;
        } else {
            $response['info'][] = "Не удалось создать запрос на оплату заказа";
        }

        return $response;
    }

    public function SendRequest(string $method, string|array $data, bool $isPost = true, bool $return = true, bool $decodeToArray = true) {
        if (in_array(gettype($data), ['array','object'])) $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $url = "https://securepay.tinkoff.ru/v2/$method";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, $isPost);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($return) {
            return $decodeToArray ? json_decode($response, true) : $response;
        }
    }

    /**
     * Получить массив статусов
     * @param string $type какие статусы вывести (all - все | process - в обработке продавца | close - закрытые | payment_process - в обработке банка | new - статусы нового заказа)
     * @return array string array
     */
    public function GetStatusArray(string $type = "all") {
        $type = mb_strtolower(trim($type));
        if ($type === "all") {
            return ['Ожидание оплаты','Обработка платежа','Сборка заказа','Отправлен','Доставлен','Закрыт','Отменён'];
        } elseif ($type === "process") {
            return ['Сборка заказа','Отправлен'];
        } elseif ($type === "payment_process") {
            return ['Ожидание оплаты','Обработка платежа'];
        } elseif ($type === "new") {
            return ['Ожидание оплаты'];
        } elseif ($type === "close") {
            return ['Доставлен','Закрыт','Отменён'];
        }
        return [];
    }

    /**
     * Получить статус
     * @param int $orderId ID заказа
     * @return string|null 
     */
    public function GetStatus(int $orderId) {
        try {
            $query = $this->conn->prepare("SELECT `status` FROM `{$this->mainTable}` WHERE `id` = :orderId");
            $query->execute(['orderId' => $orderId]);
            $query = $query->fetch(PDO::FETCH_ASSOC);
            if (!isset($query) && empty($query)) {
                return null;
            }
            return trim($query['status']);
        } catch (\Throwable $th) {
           return null;
        }
    }

    public function Change(int|null $userId, int $orderId, string $newStatus, int|null $deliveryId = null, string|null $tracking = null, string|null $adminComment = null, bool $willClose = false) : array {
        $response = [
            'status' => false,
            'info' => [],
        ];

        try {
            $newStatus = trim($newStatus);
            $closedStatuses = $this->GetStatusArray('close');

            // если хотят закрыть заказ, но статус не входит в категорию для закрытия, выводим ошибку
            if ($willClose && !in_array($newStatus, $closedStatuses)) {
                $response['info'][] = "Нельзя закрыть заказ с указанным статусом: $newStatus";
                $response['info'][] = "Для закрытия заказа используйте соответствующие статусы";
                return $response;
            }

            $query = "SELECT `id` FROM `{$this->GetTable()}` WHERE `id` = :orderId";
            $execute = ['orderId' => $orderId];

            $query = $this->conn->prepare($query);
            $query->execute($execute);
            $query = $query->fetch(PDO::FETCH_ASSOC);
            if (!isset($query) || empty($query)) {
                $response['info'][] = "Заказ №$orderId не найден";
                return $response;
            }

            $query = "UPDATE `{$this->GetTable()}` SET `status` = :newStatus, `deliveryId` = :deliveryId, `tracking` = :tracking, `adminComment` = :adminComment";
            $execute = ['orderId' => $orderId, 'newStatus' => $newStatus, 'deliveryId' => $deliveryId, 'tracking' => $tracking, 'adminComment' => $adminComment];
            if ($willClose) {
                $query .= ", `isClosed` = :isClosed, `closed` = :closed";
                $execute['isClosed'] = true;
                $execute['closed'] = date("Y-m-d H:i:s");
                if (isset($userId) && $userId > 0) {
                    $query .= ", `idWhoClosed` = :idWhoClosed";
                    $execute['idWhoClosed'] = $userId;
                }
            }
            $query .= " WHERE `id` = :orderId";

            $query = $this->conn->prepare($query);
            $query->execute($execute);

            $response['status'] = true;
            $response['info'][] = "Заказ №$orderId успешно отредактирован";
        } catch (\Throwable $th) {
            $response['info'][] = "Ошибка при изменении данных заказа №$orderId";
            if (DEBUG_MODE) $response['info'][] = $th->getMessage();
        }

        return $response;
    }

    public function Cancel(string $paymentId, int $idWhoClosed) {
        $response = [
            'status' => false,
            'info' => []
        ];

        $paymentId = trim($paymentId);
        if ($paymentId === "") $response['info'][] = "Не указан номер оплаты";

        if (count($response['info']) > 0) return $response;

        $terminal = $this->GetTerminalData();

        $order = $this->GetOrders('one', $paymentId, true);

        if ($order['noOrders']) {
            $response['info'][] = "Заказ не найден";
            return $response;
        }

        $order = $order['orders'][0];

        $terminal = $this->GetTerminalData();
        $token = $this->GenerateOrderToken($terminal['TerminalPassword'], $order['payment_id'], $terminal['TerminalKey']);

        $data = [
            "TerminalKey" => $terminal['TerminalKey'],
            "PaymentId" => $order['payment_id'],
            "Token" => $token
        ];

        $PaymentState = $this->SendRequest("GetState", $data);
        if (!$PaymentState['Success']) {
            $response['info'][] = "Ошибка проверки статуса оплаты";
            $response['info'][] = $PaymentState['Details'];
            return $response;
        }
        
        switch ($PaymentState['Status']) {
            case 'FORM_SHOWED':
            case 'NEW':
                // $data['Amount'] = $order['price'] * 100;
                // $data['Receipt'] = $this->CreateReceiptArray($order['products']);
                $PaymentState = $this->SendRequest("Cancel", $data);
                if ((isset($PaymentState['Success']) && $PaymentState['Success'] === true) && (isset($PaymentState['Status']) && $PaymentState['Status'] === "CANCELED")) {
                    $query = "UPDATE `{$this->mainTable}` SET `isClosed` = 1";
                    $query_execute = ['payment_id' => $order['payment_id']];
                    if ($idWhoClosed > 0) {
                        $query .= ", `idWhoClosed` = :idWhoClosed";
                        $query_execute['idWhoClosed'] = $idWhoClosed;
                    }
                    $query .= " WHERE `payment_id` = :payment_id";
                    
                    try {
                        $query = $this->conn->prepare($query);
                        $query->execute($query_execute);

                        $response['status'] = true;
                        $response['info'][] = "Оплата отменена";
                    } catch (\Throwable $th) {
                        $response['status'] = false;
                        $response['info'][] = "При смене статуса в базе данных возникла ошибка";
                        $response['info'][] = "Пожалуйста, свяжитесь с нами и опишите проблему";
                        if (DEBUG_MODE) $response['info'][] = $th->getMessage();
                    }
                } else {
                    $response['status'] = false;
                    $response['info'][] = "Не удалось отменить оплату";
                    $response['info'][] = "Возможно, оплата уже отменена";
                }
                break;
            case 'AUTHORIZED':
            case 'CONFIRMED':
                $response['info'][] = "Заказ оплачен";
                $response['info'][] = "Если вы хотите отменить заказ и вернуть деньги, пожалуйста, свяжитесь с нами";
                break;
            case 'CANCELED':
                $response['info'][] = "Оплата уже отменена";
                break;
            case 'REFUNDED':
                $response['info'][] = "Оплата возвращена";
                break;
            case 'PARTIAL_REFUNDED':
                $response['info'][] = "Оплата возвращена частично";
                break;
            case 'DEADLINE_EXPIRED':
                $response['info'][] = "Платежная сессия закрыта в связи с превышением срока отсутствия активности по текущему статусу";
                break;
            
            default:
                $response['info'][] = "Статус оплаты находится в обработке";
                break;
        }

        return $response;
    }

    public function GetState(string $paymentId) {
        $paymentId = trim($paymentId);

        $terminal = $this->GetTerminalData();
        $token = $this->GenerateOrderToken($terminal['TerminalPassword'], $paymentId, $terminal['TerminalKey']);

        $data = [
            "TerminalKey" => $terminal['TerminalKey'],
            "PaymentId" => $paymentId,
            "Token" => $token
        ];

        return $this->SendRequest("GetState", $data);
    }
    
    private function GetCorrectPriceFormat(float $value) {
        $value = number_format($value, 2, '.', '');
        $value = str_replace(('.00'), '', $value);
        return (float) $value;
    }

    function CheckStatesFromTinkoff() : array {
        $response = [];
        $processStatuses = $this->GetStatusArray('payment_process');
    
        if (count($processStatuses) < 1) return [];
        
        $prepare = "SELECT * FROM `{$this->mainTable}` WHERE";
        $execute = [];
        
        for ($i = 0; $i < count($processStatuses); $i++) {
            if ($i > 0) $prepare .= " OR";
            $prepare .= " `status` = :status$i";
            $execute["status$i"] = $processStatuses[$i];
        }
    
        $orders = $this->conn->prepare($prepare);
        $orders->execute($execute);
        $orders = $orders->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($orders) > 0) {
            for ($i = 0; $i < count($orders); $i++) {
                $order = $orders[$i];
                if (isset($order['payment_id'])) {
                    $order['payment_id'] = trim($order['payment_id']);
                    if (trim($order['payment_id']) !== "") {
                        $http_response = $this->GetState($order['payment_id']);
                        if (isset($http_response['Success']) && $http_response['Success']) {
                            // $response[] = $http_response;
                            $http_response['OrderId'] = (int)$http_response['OrderId'];
                            $newStatus = null;
                            $willClose = false;
                            switch ($http_response['Status']) {
                                // Создан
                                case 'NEW':
                                case 'FORM_SHOWED':
                                    $newStatus = "Ожидание оплаты";
                                    break;
        
                                // Отменен
                                case 'DEADLINE_EXPIRED':
                                case 'CANCELED':
                                case 'REJECTED':
                                case 'PARTIAL_REVERSED':
                                case 'REVERSED':
                                case 'REFUNDED':
                                case 'PARTIAL_REFUNDED':
                                    $newStatus = "Отменён";
                                    $willClose = true;
                                    break;
        
                                // Подтвержден
                                case 'AUTHORIZED':
                                case 'CONFIRMED':
                                    $newStatus = "Сборка заказа";
                                    break;
                                
                                default:
                                    $newStatus = "Обработка платежа";
                                    break;
                            }
                            $response[$i] = ['orderId'=>$http_response['OrderId'],'current_payment_status'=>$http_response['Status']];
                            try {
                                $change_response = $this->Change(null, $http_response['OrderId'], $newStatus, null, null, null, $willClose);
                                $response[$i]['stauts'] = $change_response['status'];
                                $response[$i]['info'] = $change_response['info'];
                                $response[$i]['orderId'] = $http_response['OrderId'];
                                $response[$i]['newStatus'] = $newStatus;
                                $response[$i]['willClose'] = $willClose;
                            } catch (\Throwable $th) {
                                $response[$i]['status'] = false;
                                $response[$i][] = $th->getMessage();
                            }
                        }
                    }
                }
            }
        }

        return $response;
    }
}

// switch ($http_response['Status']) {
//     case 'NEW':
//         // Создан
//         break;

//     case 'FORM_SHOWED':
//         // Платежная форма открыта покупателем
//         break;

//     case 'DEADLINE_EXPIRED':
//         // Платежная сессия закрыта в связи с превышением срока отсутствия активности по текущему статусу
//         break;

//     case 'CANCELED':
//         // Отменен
//         break;
    
//     case 'PREAUTHORIZING':
//         // Проверка платежных данных
//         // Промежуточный статус
//         break;
    
//     case 'AUTHORIZING':
//         // Резервируется
//         // Промежуточный статус
//         break;
    
//     case 'AUTH_FAIL':
//         // Не прошел авторизацию
//         // Промежуточный статус
//         break;
    
//     case 'REJECTED':
//         // Отклонен
//         break;
    
//     case '3DS_CHECKING':
//         // Проверяется по протоколу 3-D Secure
//         break;
    
//     case '3DS_CHECKED':
//         // Проверен по протоколу 3-D Secure
//         // Промежуточный статус
//         break;
    
//     case 'PAY_CHECKING':
//         // Платеж обрабатывается
//         // Промежуточный статус
//         break;
    
//     case 'AUTHORIZED':
//         // Зарезервирован
//         break;
    
//     case 'REVERSING':
//         // Резервирование отменяется
//         // Промежуточный статус
//         break;
    
//     case 'PARTIAL_REVERSED':
//         // Резервирование отменено частично
//         break;
    
//     case 'REVERSED':
//         // Резервирование отменено
//         break;

//     case 'CONFIRMING':
//         // Подтверждается
//         // Промежуточный статус
//         break;

//     case 'CONFIRM_CHECKING':
//         // Платеж обрабатывается
//         // Промежуточный статус
//         break;

//     case 'CONFIRMED':
//         // Подтвержден
//         break;

//     case 'REFUNDING':
//         // Возвращается
//         // Промежуточный статус
//         break;

//     case 'PARTIAL_REFUNDED':
//         // Возвращен частично
//         break;

//     case 'REFUNDED':
//         // Возвращен полностью
//         break;
    
//     default:
//         # code...
//         break;
// }
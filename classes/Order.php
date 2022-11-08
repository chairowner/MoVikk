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
     * Массив
     * @param int $userId id пользователя
     * @return array ['noOrders' - bool, 'orders' - array, где keys - id]
     */
    public function getOrders(string $load, int $userId = 0) {
        $response = [
            'noOrders' => true,
            'orders' => [],
        ];
        
        $closedStatuses = ["'Доставлен'","'Закрыт'"];
        $closedStatuses = implode(',',$closedStatuses);
        
        $loadValues = ['active','history','all'];
        $load = trim($load);
        if (!in_array($load,$loadValues)) exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return
        
        $sql = "SELECT o.id, o.status, o.added, o.closed, o.tracking, o.deliveryId, o.isClosed, o.comment, o.address, od.buyPrice productPrice, od.productId, p.href productHref, p.name productName, od.count productCount FROM {$this->mainTable} o INNER JOIN {$this->dataTable} od ON od.orderId = o.id INNER JOIN products p ON p.id = od.productId WHERE o.userId = :userId";
        if ($load === 'active') $sql .= " AND o.status NOT IN($closedStatuses)";
        else if ($load === 'history') $sql .= " AND o.status IN($closedStatuses)";
        $sql .= " ORDER BY o.id DESC";
        
        $activeOrders = $this->conn->prepare($sql);
        $activeOrders->execute(['userId' => $userId]);
        $activeOrders = $activeOrders->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($activeOrders) < 1) exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return
        
        $response['noOrders'] = false;
        
        $lastOrderId = 0;
        $index = -1;
        foreach ($activeOrders as $key => $order) {
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
                isset($order['address']) && trim($order['address']) != "" ?
                    trim($order['address']) : null;
            $response['orders'][$index]['tracking'] =
                isset($order['tracking']) && trim($order['tracking']) != "" ?
                    trim($order['tracking']) : null;
            $response['orders'][$index]['added'] =
                isset($order['added']) && trim($order['added']) != "" ?
                    trim($order['added']) : null;
            $response['orders'][$index]['closed'] =
                isset($order['closed']) && trim($order['closed']) != "" ?
                    trim($order['closed']) : null;
            $response['orders'][$index]['isClosed'] = (bool) $order['isClosed'];
            $response['orders'][$index]['comment'] =
                isset($order['comment']) && trim($order['comment']) != "" ?
                    trim($order['comment']) : null;
            $response['orders'][$index]['idWhoClosed'] = isset($order['idWhoClosed']) ?
                (int) $order['idWhoClosed'] : null;

            $response['orders'][$index]['products'][$order['productId']] = [
                'name' => $order['productName'],
                'count' => $order['productCount'],
                'href' => "/product/{$order['productHref']}-{$order['productId']}",
            ];
            
            $image = $this->conn->prepare("SELECT `image` FROM products_images WHERE productId = :productId AND isMain = 1");
            $image->execute(['productId' => $order['productId']]);
            $image = $image->fetch(PDO::FETCH_ASSOC);
            if (isset($image) && !empty($image)) {
                $response['orders'][$index]['products'][$order['productId']]['image']['name'] = trim($image['image']);
                $response['orders'][$index]['products'][$order['productId']]['image']['path'] = "/assets/images/products/";
            } else {
                $response['orders'][$index]['products'][$order['productId']]['image']['name'] = "camera.svg";
                $response['orders'][$index]['products'][$order['productId']]['image']['path'] = "/assets/icons/";
            }

            $delivery = $this->conn->prepare("SELECT `name`, link FROM deliveries WHERE id = :deliveryId");
            $delivery->execute(['deliveryId' => $order['deliveryId']]);
            $delivery = $delivery->fetch(PDO::FETCH_ASSOC);
            if (isset($delivery) && !empty($delivery)) {
                $response['orders'][$index]['delivery']['name'] =
                    isset($delivery['name']) && trim($delivery['name']) != "" ?
                        trim($delivery['name']) : null;
                $response['orders'][$index]['delivery']['link'] =
                    isset($delivery['link']) && trim($delivery['link']) != "" ?
                        trim($delivery['link']) : null;
            } else {
                $response['orders'][$index]['delivery']['name'] = null;
                $response['orders'][$index]['delivery']['link'] = null;
            }
        }
        
        exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return
    }
}
<?php
/**
 * Класс доставок
 * @param int $id идентификатор доставки
 * @param PDO $conn соединение с БД
 */
class Delivery {
    const ALL = 0;
    const ONE = 1;
    public PDO $conn; // соединение с БД
    private string $mainTable = 'deliveries'; // таблица

    /**
     * @param PDO $conn подключение к БД
     */
    function __construct(PDO $conn) {
        if (isset($conn)) {
            $this->conn = $conn;
        }
    }
    
    /**
     * get table
     * @return string
     */
    public function GetTable() {
        return $this->mainTable;
    }
    
    /**
     * Функция возвращает данные о доставке
     * @param int|string $id id доставки / all - все
     * @return array
     */
    function Get(int|string $id) {
        try {
            $prepare = "SELECT * FROM `{$this->mainTable}`";
            $execute = [];

            if ($id !== 'all') {
                $prepare .= " WHERE `id` = :id";
                $id = (int) $id;
                if ($id < 0) $id = 0;
                $execute['id'] = $id;
            }

            $items = $this->conn->prepare($prepare);
            $items->execute($execute);
            $items = $items->fetchAll(PDO::FETCH_ASSOC);
            
            $response['items'] = $items;
            $response['status'] = true;
            $response['info'] = ["Данные о доставке получены"];
            
            return $response;
        } catch (Exception $e) {
            $response = [
                'status' => false,
                'info' => ["Системная ошибка: не удалось получить данные о доставке"],
            ];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция добавляет доставку
     * @param string $name имя доставки
     * @param string $link ссылка на доставку
     * @return array
     */
    function Add(string $name, string $link) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            if (!isset($name)) {
                $response['info'][] = 'Не указано имя доставки';
                return $response;
            }
            if (!isset($link)) {
                $response['info'][] = 'Не указана ссылка на доставку';
                return $response;
            }
            $query = $this->conn->prepare("INSERT INTO `{$this->mainTable}` (`name`, `link`) VALUES (:name, :link)");
            $execute = ['name' => $name, 'link' => $link];
            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Доставка добавлена!';
            } else {
                $response['info'][] = 'Не удалось добавить доставку';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось добавить доставку"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция редактирует данные о доставщике
     * @param int $id id доставки
     * @param string $name имя доставки
     * @param string $link ссылка на доставку
     * @return array
     */
    function Edit(int $id, string $name, string $link) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];

            if (!isset($id)) {
                $response['info'][] = 'Не указан ID доставки';
                return $response;
            }
            if (!isset($name)) {
                $response['info'][] = 'Не указана доставка';
                return $response;
            }
            if (!isset($link)) {
                $response['info'][] = 'Не указана ссылка на доставку';
                return $response;
            }

            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `name` = :name, `link` = :link WHERE `id` = :id");
            $execute = ['id' => $id, 'name' => $name, 'link' => $link];
            if ($query->execute($execute)) {
                $response['id'] = $id;
                $response['status'] = true;
                $response['info'][] = 'Изменения сохранены';
            } else {
                $response['info'][] = 'Не удалось сохранить изменения';
            }
            
            return $response;
        } catch (Exception $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось сохранить изменения"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция удаляет доставку
     * @param int $id id доставки
     * @return array
     */
    function Remove(int $id) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            $query = $this->conn->prepare("DELETE FROM `{$this->mainTable}` WHERE `id` = :id");
            $execute = ['id' => $id];
            if ($query->execute($execute)) {
                $response['status'] = true;
                $response['info'][] = 'Доставка удалена';
                if (DEBUG_MODE) {
                    $response['info'][] = "DELETE FROM `{$this->mainTable}` WHERE `id` = $id";
                }
            } else {
                $response['info'][] = 'Не удалось удалить доставку';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось удалить доставку"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
}
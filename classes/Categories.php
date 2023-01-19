<?php
/**
 * Класс продукта
 * @param int $id идентификатор продукта
 * @param PDO $conn соединение с БД
 */
class Categories {
    const ALL = 0;
    const ONE = 1;
    public PDO $conn; // соединение с БД
    private string $mainTable = 'categories'; // таблица с продуктами
    private string $addTable = 'products'; // таблица с изображениями продуктов
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
     * Функция возвращает данные о категориях
     * @param int|string $id id категории / all - все
     * @return array
     */
    function get($id) {
        try {
            $prepare = "SELECT * FROM `{$this->mainTable}`";
            $execute = [];
            if ($id !== 'all') {
                $prepare .= " WHERE `id` = :id";
                $execute['id'] = (int) $id;
            }
            $items = $this->conn->prepare($prepare);
            $items->execute($execute);
            $items = $items->fetchAll(PDO::FETCH_ASSOC);
            
            $response['items'] = $items;
            $response['status'] = true;
            $response['info'] = ["Категория добавлена"];
            
            return $response;
        } catch (Exception $e) {
            $response = [
                'status' => false,
                'info' => ["Системная ошибка"],
            ];
            if (DEBUG_MODE) $response[] = $e->getMessage();
            return $response;
        }
    }
    
    /**
     * Функция добавляет категорию
     * @param string $name имя
     * @param string $href ссылка на категорию
     * @return array
     */
    function add(string $name = null, string $href = null) {
        try {
            $response =[
                'status' => false,
                'info' => []
            ];
            if (!isset($name) || !isset($href)) {
                $response['info'][] = 'Не указаны имя или ссылка на категорию';
                return $response;
            }
            $query = $this->conn->prepare("INSERT INTO `{$this->mainTable}` (`name`, `href`) VALUES (:name, :href)");
            $execute = ['name' => $name, 'href' => $href];
            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Категория добавлена!';
            } else {
                $response['info'][] = 'Не удалось добавить категорию';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось добавить категорию"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция редактирует категорию
     * @param int $id id категории
     * @param string $name имя
     * @param string $href ссылка на категорию
     * @return array
     */
    function edit(int $id, string $name, string $href) {
        try {
            $response =[
                'status' => false,
                'info' => []
            ];
            if (!isset($name) || !isset($href)) {
                $response['info'][] = 'Не указаны имя или ссылка';
                return $response;
            }
            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `name` = :name, `href` = :href WHERE `id` = :id");
            $execute = ['id' => $id,'name' => $name, 'href' => $href];
            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
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
     * Функция удаляет категорию
     * @param int $id id категории
     * @return array
     */
    function remove(int $id) {
        try {
            $response =[
                'status' => false,
                'info' => []
            ];
            $query = $this->conn->prepare("DELETE FROM `{$this->mainTable}` WHERE `id` = :id");
            $execute = ['id' => $id];
            if ($query->execute($execute)) {
                $response['status'] = true;
                $response['info'][] = 'Категория удалена';
                $response['info'][] = "DELETE FROM `{$this->mainTable}` WHERE `id` = $id";
            } else {
                $response['info'][] = 'Не удалось удалить категорию';
            }
            
            return $response;
        } catch (PDOException $e) {
            return ['status' => false, 'info' => [$e->getMessage()]];
        }
    }
}
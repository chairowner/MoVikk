<?php
/**
 * Класс страны-изготовителя
 * @param int $id идентификатор страны-изготовителя
 * @param PDO $conn соединение с БД
 */
class Country {
    const ALL = 0;
    const ONE = 1;
    public PDO $conn; // соединение с БД
    private string $mainTable = 'countries'; // таблица со странами

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
     * Функция возвращает данные о стране-изготовителя
     * @param int|string $id id страны-изготовителя / all - все
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
            $response['info'] = ["Страна-изготовитель выбрана"];
            
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
     * Функция добавляет страну-изготовителя
     * @param string $name имя страны-изготовителя
     * @return array
     */
    function Add(string $name = null) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            if (!isset($name)) {
                $response['info'][] = 'Не указано имя страны-изготовителя';
                return $response;
            }
            $query = $this->conn->prepare("INSERT INTO `{$this->mainTable}` (`name`) VALUES (:name)");
            $execute = ['name' => $name];
            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Страна-изготовитель добавлена!';
            } else {
                $response['info'][] = 'Не удалось добавить страну-изготовителя';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось добавить страну-изготовителя"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция редактирует страну-изготовителя
     * @param int $id id страны-изготовителя
     * @param string $name имя страны-изготовителя
     * @return array
     */
    function Edit(int $id, string $name) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            if (!isset($name)) {
                $response['info'][] = 'Не указана страна-изготовитель';
                return $response;
            }
            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `name` = :name WHERE `id` = :id");
            $execute = ['id' => $id,'name' => $name];
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
     * Функция удаляет страну-изготовителя
     * @param int $id id страны-изготовителя
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
                $response['info'][] = 'Страна-изготовитель удалена';
                if (DEBUG_MODE) {
                    $response['info'][] = "DELETE FROM `{$this->mainTable}` WHERE `id` = $id";
                }
            } else {
                $response['info'][] = 'Не удалось удалить страну-изготовителя';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось удалить страну-изготовителя"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
}
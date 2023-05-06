<?php
/**
 * Класс доставок
 * @param int $id идентификатор доставки
 * @param PDO $conn соединение с БД
 */
class FAQ {
    const ALL = 0;
    const ONE = 1;
    public PDO $conn; // соединение с БД
    private string $mainTable = 'faq'; // таблица

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
     * Функция возвращает данные
     * @param int|string $id id / all - все
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
            $response['info'] = ["Данные получены"];
            
            return $response;
        } catch (Exception $e) {
            $response = [
                'status' => false,
                'info' => ["Системная ошибка: не удалось получить данные"],
            ];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция добавляет доставку
     * @param string $question вопрос
     * @param string $answer ответ
     * @return array
     */
    function Add(string $question, string $answer) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            if (!isset($question)) {
                $response['info'][] = 'Не указан вопрос';
                return $response;
            }
            if (!isset($answer)) {
                $response['info'][] = 'Не указан ответ';
                return $response;
            }
            $query = $this->conn->prepare("INSERT INTO `{$this->mainTable}` (`question`, `answer`) VALUES (:question, :answer)");
            $execute = ['question' => $question, 'answer' => $answer];
            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Вопрос добавлен!';
            } else {
                $response['info'][] = 'Не удалось вопрос';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось добавить вопрос"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция редактирует данные
     * @param int $id id
     * @param string $question вопрос
     * @param string $answer ответ
     * @return array
     */
    function Edit(int $id, string $question, string $answer) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];

            if (!isset($id)) {
                $response['info'][] = 'Не указан ID';
                return $response;
            }
            if (!isset($question)) {
                $response['info'][] = 'Не указан вопрос';
                return $response;
            }
            if (!isset($answer)) {
                $response['info'][] = 'Не указан ответ';
                return $response;
            }

            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `question` = :question, `answer` = :answer WHERE `id` = :id");
            $execute = ['id' => $id, 'question' => $question, 'answer' => $answer];
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
     * Функция удаляет вопрос
     * @param int $id id
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
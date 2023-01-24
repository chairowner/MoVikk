<?php
/**
 * Класс инструкций
 * @param int $id id
 * @param PDO $conn соединение с БД
 */
class Instruction {
    const ALL = 0;
    const ONE = 1;
    public array $fileTypes = [
        'wmv' => "video/x-ms-wmv",
        'mp4' => "video/mp4",
        'avi' => "video/avi",
        'webm' => "video/webm",
        'mov' => "video/quicktime",
        'mkv' => "video/x-matroska"
    ];
    public PDO $conn; // соединение с БД
    private string $mainTable = 'instructions'; // таблица

    /**
     * @param PDO $conn подключение к БД
     */
    function __construct(PDO $conn) {
        if (isset($conn)) {
            $this->conn = $conn;
        }
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
     * Функция добавляет инструкцию
     * @param string $name вопрос
     * @param string $text ответ
     * @param string $text ответ
     * @return array
     */
    function Add(string $name, string $text = null, array|null $video = null) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];

            if (!isset($name)) {
                $response['info'][] = 'Не указано название инструкции';
                return $response;
            }

            $fileName = null;
            if (isset($video) && !empty($video)) {
                $fileName = md5($video['name'].time());
            }

            $execute = [
                'name' => $name,
                'text' => $text,
                'video' => $fileName
            ];

            $sql = "INSERT INTO `{$this->mainTable} (`name`, `text`, `video`)` VALUES (:name, :text, :video)";

            $query = $this->conn->prepare($sql);
            $execute = ['name' => $name, 'text' => $text];

            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Об!';
            } else {
                $response['info'][] = 'Не удалось обновить данные';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось обновить данные"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция редактирует данные
     * @param int $id id
     * @param string $name вопрос
     * @param string $text ответ
     * @return array
     */
    function Edit(int $id, string $name, string $text) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];

            if (!isset($id)) {
                $response['info'][] = 'Не указан ID';
                return $response;
            }
            if (!isset($name)) {
                $response['info'][] = 'Не указан вопрос';
                return $response;
            }
            if (!isset($text)) {
                $response['info'][] = 'Не указан ответ';
                return $response;
            }

            $execute = [
                'name' => $name,
                'text' => $text,
                'video' => isset($video) ? "" : null
            ];

            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `name` = :name, `text` = :text WHERE `id` = :id");
            $execute = ['id' => $id, 'name' => $name, 'text' => $text];
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
     * Функция удаляет инструкцию
     * @param int $id id
     * @return array
     */
    function Remove(int $id) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];

            $sql = "SELECT `text`, `video` FROM `{$this->mainTable}` WHERE `id` = :id";
            $execute = ['id' => $id];
            $query = $this->conn->prepare($sql);

            if ($query->execute($execute)) {
                if (isset($query['video'])) {
                    $videoPath = "assets/videos/instructions/".trim($query['video']);
                    if (file_exists($videoPath)) {
                        if (!unlink($videoPath)) {
                            $response['info'][] = "Ошибка: не удалось удалить видео";
                        }
                    }
                }
    
                $query = $this->conn->prepare("DELETE FROM `{$this->mainTable}` WHERE `id` = :id");
    
                if ($query->execute($execute)) {
                    $response['status'] = true;
                    $response['info'][] = 'Инструкция удалена';
                    if (DEBUG_MODE) {
                        $response['info'][] = "DELETE FROM `{$this->mainTable}` WHERE `id` = $id";
                    }
                } else {
                    $response['info'][] = 'Не удалось удалить инструкцию';
                }
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось удалить инструкцию"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
}
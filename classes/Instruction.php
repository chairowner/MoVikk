<?php
/**
 * Класс инструкций
 * @param int $id id
 * @param PDO $conn соединение с БД
 */
class Instruction {
    const ALL = 0;
    const ONE = 1;
    const ASSETS = "../../../assets/"; // путь
    const ASSETS_VIDEO = self::ASSETS."videos/"; // путь
    const UPLOAD_DIR = self::ASSETS_VIDEO."instructions/"; // путь
    public array $fileTypes = [
        "video/x-ms-wmv" => 'wmv',
        "video/mp4" => 'mp4',
        "video/avi" => 'avi',
        "video/webm" => 'webm',
        "video/quicktime" => 'mov',
        "video/x-matroska" => 'mkv'
    ];
    public PDO $conn; // соединение с БД
    public string $mainTable = 'instructions'; // таблица

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

    private function GenInsertIntoValues(string $table, array $execute):string|null {
        if (count($execute) === 0) return null;
        $sql_keys = "";
        $sql_values = "";
        $i = 0;
        foreach ($execute as $key => $value) {
            if ($i > 0) {
                $sql_keys .= ",";
                $sql_values .= ",";
            }
            $sql_keys .= "`$key`";
            $sql_values .= ":$key";
            $i++;
        }
        return "INSERT INTO `$table` ($sql_keys) VALUES ($sql_values)";
    }

    private function GenUpdateValues(string $table, array $execute, string $where_q):string|null {
        if (count($execute) === 0) return null;
        if (isset($execute[$where_q])) unset($execute[$where_q]);
        $sql_set = "";
        $i = 0;
        foreach ($execute as $key => $value) {
            if ($i > 0) $sql_set .= ",";
            $sql_set .= "`$key` = :$key";
            $i++;
        }
        return "UPDATE `$table` SET $sql_set WHERE `$where_q` = :$where_q";
    }

    private function UpdateDirs() {
        if (!is_dir(self::ASSETS)) mkdir(self::ASSETS, 0777, true);
        if (!is_dir(self::ASSETS_VIDEO)) mkdir(self::ASSETS_VIDEO, 0777, true);
        if (!is_dir(self::UPLOAD_DIR)) mkdir(self::UPLOAD_DIR, 0777, true);
    }
    
    /**
     * Функция добавляет инструкцию
     * @param string $name наименование
     * @param string|null $text ответ
     * @param array|null $video видеофайл
     * @return array
     */
    function Add(string $name, string|null $text = null, array|null $video = null) {
        $response = [
            'status' => false,
            'info' => []
        ];
        try { $this->UpdateDirs(); } catch (\Throwable $th) {}
        try {
            $execute = [
                'name' => $name,
                'text' => null,
                'video' => null
            ];

            if (!isset($name)) {
                $response['info'][] = 'Не указано название инструкции';
                return $response;
            }

            if (isset($text)) {
                $execute['text'] = trim($text);
            }

            if (isset($video) && !empty($video)) {
                if (!isset($this->fileTypes[$video["type"]])) {
                    $response['info'][] = 'Некорректный тип файла';
                    return $response;
                }
                $execute['video'] = md5($video['name'].time()).".".$this->fileTypes[$video["type"]];
                if (!move_uploaded_file($video["tmp_name"], self::UPLOAD_DIR.$execute['video'])) {
                    $execute['video'] = null;
                }
            }

            if (!isset($execute['text']) && !isset($execute['video'])) {
                $response['info'][] = "Для занесения инструкции либо введите текст, либо заргузите видео";
                return $response;
            }

            $sql = $this->GenInsertIntoValues($this->mainTable, $execute);
            $query = $this->conn->prepare($sql);

            if ($query->execute($execute)) {
                $response['id'] = $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Добавлено!';
            } else {
                $response['info'][] = 'Не удалось добавить инструкцию';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось добавить инструкцию"]];
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
    function Edit(int $id, string $name, string|null $text = null, array|null $video = null) {
        $response = [
            'status' => false,
            'info' => []
        ];
        try { $this->UpdateDirs(); } catch (\Throwable $th) {}
        // try {
            if (!isset($id)) {
                $response['info'][] = 'Не указан ID';
                return $response;
            }
            $execute = [
                'id' => $id,
                'name' => $name,
                'text' => null,
            ];

            if (!isset($name)) {
                $response['info'][] = 'Не указано название инструкции';
                return $response;
            }

            if (isset($text)) {
                $execute['text'] = trim($text);
            }

            if (isset($video) && !empty($video)) {
                if (!isset($this->fileTypes[$video["type"]])) {
                    $response['info'][] = 'Некорректный тип файла';
                    return $response;
                }

                $execute['video'] = md5($video['name'].time()).".".$this->fileTypes[$video["type"]];

                // если прислали новое видео, удаляем старое
                $oldVideo = $this->conn->prepare("SELECT `video` FROM `{$this->mainTable}` WHERE `id` = :id");
                $oldVideo->execute(['id' => $id]);
                $oldVideo = $oldVideo->fetch(PDO::FETCH_ASSOC);
                if (isset($oldVideo) && isset($oldVideo['video'])) {
                    $oldVideo['video'] = trim($oldVideo['video']);
                    $oldVideo['video'] = self::UPLOAD_DIR.$oldVideo['video'];
                    if (file_exists($oldVideo['video'])) {
                        if (!unlink($oldVideo['video'])) {
                            $response['info'][] = "Ошибка: не удалось удалить старое видео";
                            return $response;
                        }
                    }
                }

                if (!move_uploaded_file($video["tmp_name"], self::UPLOAD_DIR.$execute['video'])) $execute['video'] = null;
            }
            
            $sql = $this->GenUpdateValues($this->mainTable, $execute, 'id');
            $query = $this->conn->prepare($sql);
            if ($query->execute($execute)) {
                $response['id'] = $id;
                $response['status'] = true;
                $response['info'][] = 'Изменения сохранены';
            } else {
                $response['info'][] = 'Не удалось сохранить изменения';
            }
            
            return $response;
        // } catch (Exception $e) {
        //     $response = ['status' => false, 'info' => ["Системная ошибка: не удалось сохранить изменения"]];
        //     if (DEBUG_MODE) {
        //         $response['info'][] = $e->getMessage();
        //     }
        //     return $response;
        // }
    }
    
    /**
     * Функция удаляет инструкцию
     * @param int $id id
     * @return array
     */
    function Remove(int $id) {
        $response = [
            'status' => false,
            'info' => []
        ];
        try {
            $sql = "SELECT `text`, `video` FROM `{$this->mainTable}` WHERE `id` = :id";
            $execute = ['id' => $id];
            $query = $this->conn->prepare($sql);

            if ($query->execute($execute)) {
                $query = $query->fetch(PDO::FETCH_ASSOC);
                if (isset($query['video'])) {
                    $videoPath = self::UPLOAD_DIR.trim($query['video']);
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
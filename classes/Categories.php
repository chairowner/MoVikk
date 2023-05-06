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
    public string $imagesPath = 'assets/images/categories'; // таблица со странами
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
     * get table
     * @return string
     */
    public function GetTable() {
        return $this->mainTable;
    }
    
    /**
     * Функция возвращает данные о категориях
     * @param int|string $id id категории / all - все
     * @return array
     */
    function Get(int|string $id) {
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
            $response['info'] = ["Категория выбрана"];
            
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
     * @param array $images массив с изображениями
     * @return array
     */
    function Add(string $name = null, string $href = null, array $images = []) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];

            if ((!isset($name) || trim($name) === "") || (!isset($href) || trim($href) === "")) {
                $response['info'][] = 'Не указаны имя или ссылка на категорию';
                return $response;
            }

            $name = trim($name);
            $href = trim($href);

            $images_count = isset($images['error']) ? count($images['error']) : 0;

            $query = $this->conn->prepare("INSERT INTO `{$this->mainTable}` (`name`, `href`) VALUES (:name, :href)");
            if ($query->execute(['name' => $name, 'href' => $href])) {
                $response['id'] = (int) $this->conn->lastInsertId();
                $response['status'] = true;
                $response['info'][] = 'Категория добавлена!';
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
                        $extension = pathinfo($images['name'][0])['extension'];
                        $images['name'][0] = md5($images['name'][0].time()).".$extension";
                        if (move_uploaded_file($images['tmp_name'][0], get_include_path()."{$this->imagesPath}/{$images['name'][0]}")) {
                            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `image` = :image WHERE `id` = :imageId");
                            $query->execute(['imageId' => $response['id'], 'image' => $images['name'][0]]);
                        }
                    }
                }
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
     * @param array $images изображение
     * @return array
     */
    function Edit(int $id, string $name, string $href, array $images = []) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            
            if (!isset($name) || !isset($href)) {
                $response['info'][] = 'Не указаны имя или ссылка';
                return $response;
            }
            
            $category = $this->Get($id)['items'];
            $category = count($category) > 0 ? $category[0] : null;

            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `name` = :name, `href` = :href WHERE `id` = :id");
            if ($query->execute(['id' => $id,'name' => $name, 'href' => $href])) {
                $response['id'] = $id;
                $response['status'] = true;
                $response['info'][] = 'Изменения сохранены';

                $images_count = isset($images['error']) ? count($images['error']) : 0;

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
                        $extension = pathinfo($images['name'][0])['extension'];
                        $images['name'][0] = md5($images['name'][0].time()).".$extension";
                        if (move_uploaded_file($images['tmp_name'][0], get_include_path()."{$this->imagesPath}/{$images['name'][0]}")) {
                            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `image` = :image WHERE `id` = :imageId");
                            if ($query->execute(['imageId' => $response['id'], 'image' => $images['name'][0]])) {
                                if (isset($category['image']) && trim($category['image']) !== "") {
                                    $oldImagePath = get_include_path()."{$this->imagesPath}/{$category['image']}";
                                    if (file_exists($oldImagePath)) {
                                        if (unlink($oldImagePath)) {
                                            if (DEBUG_MODE) {
                                                $response['info'][] = "Старое изображение удалено";
                                            }
                                        } else {
                                            $response['info'][] = 'Не удалось удалить старое изображение';
                                        }
                                    } else {
                                        if (DEBUG_MODE) {
                                            $response['info'][] = "Старое изображение не найдено";
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (DEBUG_MODE) {
                        $response['info'][] = "Изображение не загружено";
                    }
                }
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
                $response['info'][] = 'Категория удалена';
                if (DEBUG_MODE) {
                    $response['info'][] = "DELETE FROM `{$this->mainTable}` WHERE `id` = $id";
                }
            } else {
                $response['info'][] = 'Не удалось удалить категорию';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось удалить категорию"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
    
    /**
     * Функция удаляет изображение категории
     * @param int $id id категории
     * @return array
     */
    function DeleteImage(int $id) {
        try {
            $response = [
                'status' => false,
                'info' => []
            ];
            $category = $this->Get($id)['items'];
            if (count($category) > 0) {
                $category = $category[0];
                if (isset($category['image'])) {
                    $category['image'] = trim($category['image']);
                    $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET `image` = NULL WHERE `id` = :id");
                    if ($query->execute(['id' => $id])) {
                        $response['status'] = true;
                        $response['info'][] = 'Изображение категории удалено';
                        if ($category['image'] !== "") {
                            $category_path = get_include_path()."{$this->imagesPath}/{$category['image']}";
                            if (file_exists($category_path)) {
                                if (!unlink($category_path)) {
                                    $response['info'][] = "Не удалось удалить файл изображения";
                                }
                            } else {
                                if (DEBUG_MODE) {
                                    $response['info'][] = "Файл изображения не существует";
                                }
                            }
                        } else {
                            if (DEBUG_MODE) {
                                $response['info'][] = "Прошлое название было пустым (\"\")";
                            }
                        }
                    } else {
                        $response['info'][] = 'Не удалось удалить изображение категории';
                        if (DEBUG_MODE) {
                            $response['info'][] = "UPDATE `{$this->mainTable}` SET `image` = NULL WHERE `id` = $id";
                        }
                    }
                } else {
                    $response['info'][] = 'Изображение категории не установлено';
                }
            } else {
                $response['info'][] = 'Категория не найдена';
            }
            
            return $response;
        } catch (PDOException $e) {
            $response = ['status' => false, 'info' => ["Системная ошибка: не удалось удалить изображение категории"]];
            if (DEBUG_MODE) {
                $response['info'][] = $e->getMessage();
            }
            return $response;
        }
    }
}
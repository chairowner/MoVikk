<?php
class Email {
    private PDO $conn;
    private string $mainTable = "smtp_settings";

    /**
     * @param PDO $conn подключение к БД
     * @param array $session данные сессии
     */
    public function __construct(PDO $conn = null) {
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
     * Выборка данных
     * @param array $tabelFields поля таблицы
     * @return array 
     */
    public function Get(array $tabelFields = ['*']) {
        $response = [];
        if (is_array($tabelFields) && isset($tabelFields) && !empty($tabelFields)) {
            try {
                $response = $this->conn->prepare("SELECT ".implode(', ', $tabelFields)." FROM `{$this->mainTable}`");
                $response->execute();
                $response = $response->fetch(PDO::FETCH_ASSOC);
                if (count($tabelFields) === 1) {
                    if (trim($tabelFields[0]) !== '*') {
                        $response = $response[$tabelFields[0]];
                    }
                }
            } catch (PDOException $e) {
                $response['error'] = $e->getMessage();
            }
        }
        return $response;
    }

    public function Change(array $execute) {
        $response = [
            'status' => false,
            'info' => []
        ];

        try {
            $query_set = null;
            $index = 0;
            foreach ($execute as $key => $value) {
                if ($index > 0) {
                    $query_set += ",";
                }
                $query_set += "`$key` = :$key";
                $index++;
            }
            
            $query = $this->conn->prepare("UPDATE `{$this->mainTable}` SET $query_set");
            $query->execute($execute);
        } catch (PDOException $ex) {
            if (DEBUG_MODE) {
                $response['info'][] = $ex->getMessage();
            }
        }

        if (count($response['info']) === 0) {
            $response['status'] = true;
            $response['info'][] = "Изменения сохранены";
        }

        return $response;
    }
}
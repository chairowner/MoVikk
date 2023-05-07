<?php
/**
 * @param PDO $conn подключение к БД
 * @param array $sqlData = [[], [], [], []] SELECT, FROM, WHERE, JOINS 
 * @param string $fetch FETCH all/one
 * @param bool $distinct убрать повторение значений
 * @param bool $getSQL взять значение
 * @return array
 */
function formSQL(PDO $conn = null, array $sqlData = [[], [], [], []], string $fetch = 'all', bool $distinct = false, bool $getSQL = false) {
    $response = [];

    $sqlSelect = [];
    $sqlFrom = [];
    $sqlWhere = [];
    $sqlJoins = [];

    if (isset($sqlData[0])) $sqlSelect = $sqlData[0];
    if (isset($sqlData[1])) $sqlFrom = $sqlData[1];
    if (isset($sqlData[2])) $sqlWhere = $sqlData[2];
    if (isset($sqlData[3])) $sqlJoins = $sqlData[3];

    if (isset($conn)) /* если есть подключение к БД */ {
        $sql = ""; # SQL-запрос
        if (count($sqlSelect) > 0 && count($sqlFrom) > 0) {
            # заполняем SELECT и FROM
            $sql .= "SELECT ";
            if (isset($distinct) && $distinct === true) $sql .= "DISTINCT ";
            $sql .= implode(',',$sqlSelect)." FROM ".implode(',',$sqlFrom);
            # заполняем JOINS'ы
            if (count($sqlJoins) > 0) $sql .= " ".implode(' ',$sqlJoins);
            # заполняем WHERE
            if (count($sqlWhere) > 0) $sql .= " WHERE ".implode(',',$sqlWhere);

            if ($getSQL) {
                $response = [$sql];
            } else {
                try {
                    $response = $conn->prepare($sql);
                    $response->execute();
                    if ($fetch === 'one') $response = $response->fetch(PDO::FETCH_ASSOC);
                    else $response = $response->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $response = [];
                }
            }
        }
    } 

    return $response;
}
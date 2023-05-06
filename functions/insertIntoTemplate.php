<?php
function insertIntoTemplate(array $execute, string $template) {
    $keys = $values = [];
    foreach ($execute as $key => $value) {
        $keys[] = '${'.$key.'}';
        $values[] = $value;
    }
    return str_replace($keys, $values, $template);
}
<?php
/**
 * Обрезка повторяющихся симвоов
 * @param string $text строка
 * @return string
 */
function trimSpaces(string $text) {
    $text = trim($text);
    return preg_replace('/[\s]{2,}/', ' ', $text);
}
<?php
/**
 * Склонение существительных после числительных.
 * 
 * @param string $value Значение
 * @param array $words Массив вариантов, например: array('товар', 'товара', 'товаров')
 * @param bool $show Включает значение $value в результирующею строку
 * @return string
 */
function numWord($number, $word, $show = true) 
{
    $n = abs($number);
    $n %= 100;
  
    if ($n >= 5 && $n <= 20) {
        if ($show) return "{$number} {$word[2]}";
        else return $word[2];
    }
  
    $n %= 10;
  
    if ($n === 1) {
        if ($show) return "{$number} {$word[0]}";
        else return $word[0];
    }
  
    if ($n >= 2 && $n <= 4) {
        if ($show) return "{$number} {$word[1]}";
        else return $word[1];
    }
  
    if ($show) return "{$number} {$word[2]}";
    else return $word[2];
}
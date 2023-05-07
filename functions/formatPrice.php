<?php
function formatPrice(float $value, string $unit = '₽', string $separator = ' ', string $floatSign = ',') {
	if ($value > 0) {
		$value = number_format($value, 2, $floatSign, $separator);
		$value = str_replace(($floatSign.'00'), '', $value);
 
		if (!empty($unit)) {
			$value .= " {$unit}";
		}
		return $value;
	} else {
		return 'Нет в наличии';
	}
}
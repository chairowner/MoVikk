<?php
function formatPrice(float $value, string $unit = '₽') {
	if ($value > 0) {
		$value = number_format($value, 2, ',', '.');
		$value = str_replace(',00', '', $value);
 
		if (!empty($unit)) {
			$value .= " {$unit}";
		}
		return $value;
	} else {
		return 'Нет в наличии';
	}
}
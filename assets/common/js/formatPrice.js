function formatPrice(value, unit = 'RUB', countryFormat = 'ru-RU') {
	if (typeof value == "undefined" || value.length < 0) return 'Нет в наличии';
    if (unit == null) return value.toLocaleString(countryFormat);
	value = value.toLocaleString(countryFormat, {
        style: 'currency',
        currency: unit,
    });
	return value;
}
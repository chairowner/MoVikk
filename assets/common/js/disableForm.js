function disableForm(form, mainButtons, willDisable = true) {
	const
    inputs = form.find('input'),
    textareas = form.find('textarea'),
    buttons = form.find('button'),
    selects = form.find('select'),
    bts = form.find('.button');
    
	if (willDisable) {
		inputs.addClass('pe-none');
		textareas.addClass('pe-none');
		buttons.addClass('pe-none');
		bts.addClass('pe-none');
		selects.addClass('pe-none');
        if (Array.isArray(mainButtons)) {
            for (let i = 0; i < mainButtons.length; i++) {
                $(mainButtons[i]).attr('disabled', true);
            }
        }
	} else {
		inputs.removeClass('pe-none');
		textareas.removeClass('pe-none');
		buttons.removeClass('pe-none');
		bts.removeClass('pe-none');
		selects.removeClass('pe-none');
        if (Array.isArray(mainButtons)) {
            for (let i = 0; i < mainButtons.length; i++) {
                $(mainButtons[i]).removeAttr('disabled');
            }
        }
	}
}
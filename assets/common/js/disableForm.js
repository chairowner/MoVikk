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

		inputs.attr('readonly', true);
		textareas.attr('readonly', true);
		buttons.attr('readonly', true);
		bts.attr('readonly', true);
		selects.attr('readonly', true);
        
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

		inputs.removeAttr('readonly', true);
		textareas.removeAttr('readonly', true);
		buttons.removeAttr('readonly', true);
		bts.removeAttr('readonly', true);
		selects.removeAttr('readonly', true);

        if (Array.isArray(mainButtons)) {
            for (let i = 0; i < mainButtons.length; i++) {
                $(mainButtons[i]).removeAttr('disabled');
            }
        }
	}
}
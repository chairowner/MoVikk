const form_sendRecoveryLink = $('#sendRecoveryLink');

$('.passwordView').on('click',function(e){
    const item = $(this);
    const passInp = form_sendRecoveryLink.find('input[name="password"]');
    const passRepeatInp = form_sendRecoveryLink.find('input[name="passwordRepeat"]');
    item.toggleClass('close');
    if (item.hasClass('close')) {
        passInp.attr('type', 'password');
        if (passRepeatInp.length > 0) passRepeatInp.attr('type', 'password');
    } else {
        passInp.attr('type', 'text');
        if (passRepeatInp.length > 0) passRepeatInp.attr('type', 'text');
    }
});
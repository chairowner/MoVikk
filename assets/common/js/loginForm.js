const msgBox = $('#login-msgBox'),reCAPTCHA_SITE_KEY = '6LcQsRAiAAAAALV4CnipHkz_FlVwo3MnZro7655c';
$('a.item__data[href="/lk"]').on('click',function(e){
    e.preventDefault();
    const overlay = $('#login-form-overlay'), timeout = 0.3;
    if (overlay.hasClass('active')) {
        overlayAct('close', overlay);
    } else {
        overlayAct('open', overlay);
    }
});
$('#close-login-form').on('click',function(){
    const
    overlay = $('#login-form-overlay'),
    forms = $('#login-form'),
    timeout = 0.3;
    if (overlay.hasClass('active')) {
        overlayAct('close', overlay);
    }
});
$('#login-form form.item').on('submit',function(e){
    e.preventDefault();
    const main = $('#login-form');
    main.addClass('close');
    setTimeout(() => {main.css('display','none');}, 200);
    const form = $(this);
    let url = '/actions/lk/',errMsg;
    if (form.attr('id') === 'login-form-auth') {
        url += "login";
        errMsg = `При авторизации произошла системная ошибка, но вы всё ещё можете связаться с нами через VK, Viber или WhatsApp!` + url;
    } else if (form.attr('id') === 'login-form-reg') {
        url += "reg";
        errMsg = `При авторизации произошла системная ошибка, но вы всё ещё можете связаться с нами через VK, Viber или WhatsApp!`;
    }
    function sendForm() {
        if (typeof token != "undefined")
        form.find('input[name="g-recaptcha-response"].g-recaptcha-response').val(token);;
        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            dataType: 'JSON',
            data: form.serialize(),
            success: function(data) {
                let type;
                if (data.status) {
                    type = 'success';
                    if (typeof data.reload != "undefined" && data.reload) {
                        location.href = "/";
                    } else {
                        overlayAct('close', overlay);
                    }
                } else {
                    type = 'error';
                    setTimeout(() => {main.css('display','flex');}, 200);
                    main.removeClass('close');
                }
                new Message(msgBox, data.info.join(";\n"), type, 7);
            },
            error: function(err) {
                console.log(`ERROR`);
                console.log(err);
                main.removeClass('close');
                new Message(msgBox, errMsg, 'error', 5);
            }
        });
    }
    if (typeof grecaptcha != "undefined") {
        grecaptcha.ready(function() {
            grecaptcha.execute(reCAPTCHA_SITE_KEY, {action: 'login'}).then(function(token) {
                sendForm();
            });
        });
    } else sendForm();
});
$('#toggle-login-forms').on('click',function(){
    const
    authForm = $('#login-form-auth'), regForm = $('#login-form-reg'),
    regText = "Регистрация", authText = "Авторизация";
    if (authForm.hasClass('active')) {
        authForm.removeClass('active');
        regForm.addClass('active');
        $(this).val(authText);
        const eye = authForm.find('.passView');
        if (!eye.hasClass('close')) eye.click();
    } else {
        authForm.addClass('active');
        regForm.removeClass('active');
        $(this).val(regText);
        const eye = regForm.find('.passView');
        if (!eye.hasClass('close')) eye.click();
    }
    authForm.find('input:not([type=submit])').val('');
    regForm.find('input:not([type=submit])').val('');
});
$('.passView').on('click',function(e){
    const item = $(this);
    const passInp = item.parent().parent().find('input[name="password"]');
    const passRepeatInp = item.parent().parent().parent().find('input[name="passwordRepeat"]');
    item.toggleClass('close');
    if (item.hasClass('close')) {
        passInp.attr('type', 'password');
        if (passRepeatInp.length > 0) passRepeatInp.attr('type', 'password');
    } else {
        passInp.attr('type', 'text');
        if (passRepeatInp.length > 0) passRepeatInp.attr('type', 'text');
    }
});
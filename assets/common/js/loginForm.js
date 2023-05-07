const loginMsgBox = $('#login-msgBox');
const reCAPTCHA_SITE_KEY = '6LdyVx8jAAAAAFpzmmmkEB4Hr_pAanZrZ-YxMz3L';

try {
    grecaptcha.ready(function() {
        grecaptcha.execute(reCAPTCHA_SITE_KEY, {action: 'login'}).then(function(token) {
            $('input[name="g-recaptcha-response"].g-recaptcha-response').val(token);
        });
    });
} catch (error) {
    console.error('ERROR',error);
}

$('#login-form form.item').on('submit',function(e){
    e.preventDefault();
    const main = $('#login-form');
    main.addClass('close');
    setTimeout(() => { main.css('display','none'); }, 200);
    const form = $(this);
    const url = '/actions/lk/login';
    const errMsg = `При авторизации произошла системная ошибка, но вы всё ещё можете связаться с нами через VK, Viber или WhatsApp!`;
    
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
                if (typeof data.redirect != "undefined") {
                    location.href = data.redirect;
                } else {
                    console.error("logerror");
                }
            } else {
                type = 'error';
                setTimeout(() => {main.css('display','flex');}, 200);
                main.removeClass('close');
            }
            new Message(loginMsgBox, data.info.join(";\n"), type, 7);
        },
        error: function(err) {
            console.log(`ERROR`);
            console.log(err);
            main.removeClass('close');
            new Message(loginMsgBox, errMsg, 'error', 5);
        }
    });
});

$('.passView').on('click',function(e){
    const item = $(this);
    const passInp = item.parent().parent().find('input[name="password"]');
    item.toggleClass('close');
    if (item.hasClass('close')) {
        passInp.attr('type', 'password');
    } else {
        passInp.attr('type', 'text');
    }
});
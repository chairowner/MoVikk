$('#login-form-reg').on('submit',function(e){
    e.preventDefault();
    $.ajax({
        url: '/actions/lk/registration.php',
        type: 'POST',
        cache: false,
        dataType: 'JSON',
        data: $(this).serialize(),
        success: function(data) {
            console.log(data);
        },
        error: function(err) {
            console.log(`ERROR`);
            console.log(err);
        }
    });
});
$(".phoneNumberField").mask("(999) 999-99-99");
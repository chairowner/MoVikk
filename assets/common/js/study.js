$('.copy-href').on("click",function(){
    const href = $(this).attr('data-href');
    copyToBuffer(href, $('#mainMessageBox'), "Ссылка скопирована", "Не удалось скопировать ссылку");
})
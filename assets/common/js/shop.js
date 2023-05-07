$('form#sort').on('submit', function(e){
    e.preventDefault();
    const form = $(this);
    const category = form.find('#categoryUrl').val();
    location.href = `/shop/` + category + `/` + form.find('input[name=sort]:checked').val();
});

$('#reset').on('click', function(e){
    e.preventDefault();
    location.href = `/shop`;
});
const adminComment = $('textarea[name="adminComment"]');
function updateTextareaHeight(textarea) {
    textarea.outerHeight(38).outerHeight(textarea[0].scrollHeight + 2);
}
$(document).on("input", "textarea", function () {
    updateTextareaHeight(adminComment);
});
updateTextareaHeight(adminComment);
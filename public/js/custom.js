$('.delete_logo').click(function(){
    $('input[name="hidden_logo"]').val('');
    $('.logo_image').remove();
    $(this).remove();
});

$("input[type='file']").change(function() {
    filename = this.files[0].name;
    $('.custom-file-label').text(filename);
});

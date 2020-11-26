$('.delete_logo').click(function(){
    $('input[name="hidden_logo"]').val('');
    $('.logo_image').remove();
    $(this).remove();
});

$("input[type='file']").change(function() {
    filename = this.files[0].name;
    $('.custom-file-label').text(filename);
});

function readURL(input)
{
    if (input.files && input.files[0])
    {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.logo_image').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(".business_logo").change(function() {
    readURL(this);
});

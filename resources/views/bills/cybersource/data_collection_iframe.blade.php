<iframe id="cardinal_collection_iframe" name="collectionIframe" height="10" width="10"
style="display: none;"></iframe>
<form id="cardinal_collection_form" method="POST" target="collectionIframe"
action=https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect>
<input id="cardinal_collection_form_input" type="hidden" name="JWT"
    value="{{$jwt}}">
</form>

<script>
    window.onload = function () {
        var cardinalCollectionForm = document.querySelector('#cardinal_collection_form');
        if (cardinalCollectionForm) // form exists 
            cardinalCollectionForm.submit();
    }

    window.addEventListener("message", function (event) {
        if (event.origin === "https://centinelapistag.cardinalcommerce.com") {
            console.log(event.data);
        }
    }, false);

</script>
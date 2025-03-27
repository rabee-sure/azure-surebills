<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Page</title>
</head>

<body>


    <iframe id='cardinal_collection_iframe' name='collectionIframe' height='10' width='10'
        style='display: none;'></iframe>
    <form id='cardinal_collection_form' method='POST' target='collectionIframe'
        action='https://centinelapistag.cardinalcommerce.com/V1/Cruise/Collect'> <input
            id='cardinal_collection_form_input' type='hidden' name='JWT' value='{{ $token }}'> </form>



    {{-- <iframe id="cardinal_collection_iframe" name="collectionIframe" height="10" width="10"
        style="display: none;"></iframe>
    <form id="cardinal_collection_form" method="POST" target="collectionIframe"
        action="https://centinelapistag.cardinalcommerce.com/V2/Cruise/StepUp">
        <input id="cardinal_collection_form_input" type="hidden" name="JWT" value="{{ $token }}">
    </form> --}}
</body>


<script>
    var cardinalCollectionForm = document.querySelector('#cardinal_collection_form');
    if (cardinalCollectionForm) // form exists 
        cardinalCollectionForm.submit();

    window.addEventListener("message", function(event) {
        if (event.origin === actionUrl) {
            // console.log(event.data);
        }
    }, false);
</script>



</html>

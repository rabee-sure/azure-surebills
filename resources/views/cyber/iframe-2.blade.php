<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Page</title>
</head>

<body>
    <iframe name="step-up-iframe" width="auto" height="1250"></iframe>
    <form id="step-up-form" target="step-up-iframe" method="post"
        action="https://centinelapistag.cardinalcommerce.com/V2/Cruise/StepUp"> <input type="hidden" name="JWT"
            value="{{ $token }}" />
        <input type="hidden" name="MD" value="optionally_include_custom_data_that_will_be_returned_as_is" />
    </form>
</body>

<script>
    window.onload = function () {
        var stepUpForm = document.querySelector('#step-up-form');
        if (stepUpForm) // Step-Up form exists
            stepUpForm.submit();
    }
</script>

</html>
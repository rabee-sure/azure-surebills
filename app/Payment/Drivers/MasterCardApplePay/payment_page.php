<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SureBills</title>
        <script><?php require 'payment-request.js'; ?></script>
    </head>
    <body>
        <button id="payment" lang="<?php echo App::getLocale() ?>" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy;"></button>
    </body>
</html>

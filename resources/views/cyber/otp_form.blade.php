<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Page</title>
</head>

<style>
html, body {
    margin: 0;
    padding: 0;
    overflow: hidden; /* Hide scrollbars */
    height: 100%;
}

iframe {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    border: none; /* Remove iframe border */
    overflow: hidden;
}
</style>

<body>
    <iframe name='step-up-iframe' id="fullPageIframe"></iframe>
    <form id='step-up-form' target='step-up-iframe' method='post'
        action="{{ config('cybersource.payer_auth_setup_url') }}">
        <input type='hidden' name='JWT' value={{ $setupAccessToken }} />
        <input type='hidden' name='MD' value='optionally_include_custom_data_that_will_be_returned_as_is' />
    </form>
</body>

<script>

window.onload = function () {
    let iframe = document.getElementById("fullPageIframe");
    iframe.style.height = window.innerHeight + "px";
    iframe.style.width = window.innerWidth + "px";
    
    // Update on window resize
    window.onresize = function () {
        iframe.style.height = window.innerHeight + "px";
        iframe.style.width = window.innerWidth + "px";
    };
};


    var stepUpForm = document.querySelector('#step-up-form');
    if (stepUpForm) // Step-Up form exists
    {
        stepUpForm.submit();
    }    
        

    // window.parent.postMessage({ redirect: "{{ route('home') }}" }, "*");


    // var iframe = document.getElementById("fullPageIframe");

    // window.addEventListener("message", function (event) {
    //     console.log('heree = '+ event.origin);
    //     if (event.origin === actionUrl) {
    //         // console.log(event.data);
    //         // alert('here');
    //     }
    // }, false);


    // iframe.onload = function () {
    //     // Check if iframe has changed (OTP processed)
    //     console.log(iframe.contentWindow);
    //     if (iframe.contentWindow.location.href.includes("success")) {
    //         window.location.href = "{{ route('home') }}"; // Redirect parent
    //     }
    // };
    
</script>



</html>

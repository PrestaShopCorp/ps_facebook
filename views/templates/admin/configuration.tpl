configuration.tpl 

{* todo: launch onboarding and retreive datas *}

<script>
window.fbAsyncInit = function() {
    //2. FB JavaScript SDK configuration and setup
    FB.init({
        appId      : '726899634800479', // FB App ID
        cookie     : true,  // enable cookies to allow the server to access the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v7.0' // uses graph api version v4.0
    });
};

//1. Load the JavaScript SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
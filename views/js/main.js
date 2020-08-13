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

//3. Facebook login with JavaScript SDK
function launchFBE() {
    FB.login(function (response) {
        if (response.authResponse) {
            // returns a User Access Token with scopes requested
            const accessToken = response.authResponse.accessToken;
            
            console.log('accessToken', accessToken);
            console.log('PsfacebookControllerLink', PsfacebookControllerLink);
            
            let request = $.ajax({
                url: PsfacebookControllerLink,
                method: 'GET',
                data: {
                    ajax: 1,
                    action: 'SaveTokenFbeAccount',
                    accessToken : accessToken 
                },
                dataType: 'json'
            }).success(function( msg ) {
                console.log('success');
            }).fail(function( jqXHR, textStatus ) {
                console.log('fail');
            });
        } else {
            console.log('User cancelled login or did not fully authorize.');
        }
    }, {
        scope: 'catalog_management',
        // refer to the extras object table for details
        extras: {
            "setup":{
                "external_business_id":"some-unique-merchand-id-1234",
                "timezone":"Europe\/Paris",
                "currency":"EUR",
                "business_vertical":"ECOMMERCE"
            },
            "business_config":{
                "business":{
                    "name":"PrestaShop SA (Change me)"
                }
            },
            "repeat":false
        }
    });
}
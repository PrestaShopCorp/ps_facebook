<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use FacebookAds\Api;

class FacebookDataProvider
{
    public function getContext()
    {
        $api = Api::init(
            '808199653047641',
            'b3f469de46ebc1f94f5b8e3e0db09fc4',
            'EAAKVHIKFB18BAJ3DDZBPcZBxY9UV3st26azZA7KZCQl48lgVdRh2G4IDwOWX7H6tVMg8qE0WzZC29bhJzmUTO9ZAAtsPXmzZA9gu3bjnilBUL8LsLQUPdxZChKa5QPWx82esxE9O9MZCIh6LrLqIDvxH7D3ZCppqZAmBSiFb2om8D4y02JbRX2rLkTc'
        );

        $api->getSession();
        return $api;
    }
}

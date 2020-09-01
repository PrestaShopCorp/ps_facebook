<?php 

class EventResolver
{
    public function resolve(string $name, array $params)
    {
        switch ($name) {
            case 'hookActionCartSave':
                $this->sendConversionEvent($name, $params);
                // pixel handled by core js event updateCart
            break;

            case 'hookActionSearch':
                $this->sendConversionEvent($name, $params);
                $this->sendPixelEvent($name, $params);
            break;
            
            default:
                # code...
                break;
        }
        // resolve if event will be sent via js variable in TPL or is handled elsewhere in JS
    }

    private function sendConversionEvent($name, $params)
    {
        $this->conversionHandler->send($name, $params);
    }

    private function sendPixelEvent($name, $params)
    {
        $this->pixelHandler->send($name, $params);
    }
}
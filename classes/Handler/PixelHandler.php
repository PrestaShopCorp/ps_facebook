<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Buffer\TemplateBuffer;
use PrestaShop\Module\PrestashopFacebook\Handler\Pixel\SearchEvent;
use PrestaShop\Module\PrestashopFacebook\Handler\Pixel\ViewContentEvent;

class PixelHandler
{
    private $context;
    private $templateBuffer;

    public function __construct(TemplateBuffer $tplBuffer)
    {
        $this->context = \Context::getContext();
        $this->templateBuffer = $tplBuffer;
    }

    // TODO: class that add data tpl for js front
    public function handleEvent($eventName, $event)
    {
        switch ($eventName) {
            case 'hookActionSearch':
                (new SearchEvent($this->context, $this->templateBuffer))->send($event);
            break;

            case 'hookDisplayHeader':
                (new ViewContentEvent($this->context, $this->templateBuffer))->send($event);
            break;

            default:
                // unsupported event
            break;
        }
    }

    /**
     * formatPixel
     *
     * @param array $params
     *
     * @return string|false
     */
    private function formatPixel($params)
    {
        // TODO: might need some refacto/clean ? this look like a manual jsonEncode()
        if (!empty($params)) {
            $format = '{';
            foreach ($params as $key => &$val) {
                if (gettype($val) === 'string') {
                    $format .= $key . ': \'' . addslashes($val) . '\', ';
                } elseif (gettype($val) === 'array') {
                    $format .= $key . ': [\'';
                    foreach ($val as &$id) {
                        $format .= (int) $id . "', '";
                    }
                    unset($id);
                    $format = \Tools::substr($format, 0, -4);
                    $format .= '\'], ';
                } else {
                    $format .= $key . ': ' . addslashes($val) . ', ';
                }
            }

            $format = \Tools::substr($format, 0, -2);
            $format .= '}';

            return $format;
        }

        return false;
    }

    /**
     * getCustomerInformations
     *
     * @return array
     */
    private function getCustomerInformations()
    {
        $arrayReturned = [];
        $simpleAddresses = $this->context->customer->getSimpleAddresses();

        if (count($simpleAddresses) > 0) {
            $current = reset($simpleAddresses);
            if ($current['city'] != null) {
                $arrayReturned['ct'] = $current['city'];
            }
            if ($current['country_iso'] != null) {
                $arrayReturned['country'] = $current['country_iso'];
            }
            if ($current['postcode'] != null) {
                $arrayReturned['zp'] = $current['postcode'];
            }
            if ($current['phone'] != null) {
                $arrayReturned['ph'] = $current['phone'];
            }
        }

        $gender = $this->context->customer->id_gender == '1' ? 'm' : 'f';
        $arrayReturned['gender'] = $gender;

        $birthDate = \DateTime::createFromFormat('Y-m-d', $this->context->customer->birthday);
        if ($birthDate instanceof \DateTime) {
            $arrayReturned['db'] = $birthDate->format('Ymd');
        }

        $arrayReturned['ln'] = $this->context->customer->firstname;
        $arrayReturned['fn'] = $this->context->customer->lastname;
        $arrayReturned['em'] = $this->context->customer->email;

        // data structured for pixel
        return $arrayReturned;
    }
}

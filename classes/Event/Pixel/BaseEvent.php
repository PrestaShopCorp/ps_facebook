<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

abstract class BaseEvent
{
    /**
     * @var \Context
     */
    protected $context;

    /**
     * @var \Ps_facebook
     */
    protected $module;

    public function __construct(\Context $context, \Ps_facebook $module)
    {
        $this->context = $context;
        $this->module = $module;
    }

    /**
     * formatPixel
     *
     * @param array $params
     *
     * @return string|false
     */
    protected function formatPixel($params)
    {
        return json_encode($params);
    }

    /**
     * getCustomerInformations
     *
     * @return array
     */
    protected function getCustomerInformations()
    {
        $arrayReturned = [];
        // TODO: fix for 1.6 ?
        if (true === $this->module->psVersionIs17) {
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

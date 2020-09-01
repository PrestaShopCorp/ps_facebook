<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

class PixelHandler
{
    private $context;

    public function __construct() 
    {
        $this->context = \Context::getContext();
    }

    // TODO: class that add data tpl for js front
    public function sendEvent($eventName, $event)
    {
        switch ($eventName) {
            case 'hookActionSearch':
                $this->sendSearchEvent($event);
            break;
            case 'hookDisplayHeader':
                $this->sendViewContentEvent($event);
            break;

            default:
                // unsupported event, log and throw ?
            break;
        }
    }

    public function sendSearchEvent($event)
    {
        // get some datas then display it for js
        // TODO: verify path
        return $this->context->module->display(__FILE__, 'views/templates/hook/search.tpl');
    }

    public function sendViewContentEvent($event)
    {
        $pixel_id = \Configuration::get('PS_PIXEL_ID');
        if (empty($pixel_id)) {
            return;
        }

        // Asset Manager to be sure the JS is loaded
        $this->context->controller->registerJavascript(
            'front_common',
            $this->context->module->js_path . 'printpixel.js', //TODO : verify path is correct
            ['position' => 'bottom', 'priority' => 150]
        );

        $type = '';
        $content = [];

        $page = $this->context->controller->php_self;
        if (empty($page)) {
            $page = \Tools::getValue('controller');
        }
        $page = pSQL($page);

        $controller_type = $this->context->controller->controller_type;
        $id_lang = (int) $this->context->language->id;
        $locale = \Tools::strtoupper($this->context->language->iso_code);
        $currency_iso_code = $this->context->currency->iso_code;
        $content_type = 'product';
        $track = 'track';

        /*
        * Triggers ViewContent product pages
        */
        if ($page === 'product') {
            $type = 'ViewContent';
            $prods = $this->context->controller->getTemplateVarProduct();

            $content = [
              'content_name' => \Tools::replaceAccentedChars($prods['name']) . ' ' . $locale,
              'content_ids' => $prods['id_product'],
              'content_type' => $content_type,
              'value' => (float) $prods['price_amount'],
              'currency' => $currency_iso_code,
            ];
        }

        /*
        * Triggers ViewContent for category pages
        */
        if ($page === 'category' && $controller_type === 'front') {
            $type = 'ViewCategory';
            $category = $this->context->controller->getCategory();

            $breadcrumbs = $this->context->controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

            $prods = $category->getProducts($id_lang, 1, 10);
            $track = 'trackCustom';

            $content = [
              'content_name' => \Tools::replaceAccentedChars($category->name) . ' ' . $locale,
              'content_category' => \Tools::replaceAccentedChars($breadcrumb),
              'content_ids' => array_column($prods, 'id_product'),
              'content_type' => $content_type,
            ];
        }

        /*
        * Triggers ViewContent for cms pages
        */
        if ($page === 'cms') {
            $type = 'ViewCMS';
            $cms = new \Cms((int) \Tools::getValue('id_cms'), $id_lang);

            $breadcrumbs = $this->context->controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));
            $track = 'trackCustom';

            $content = [
              'content_category' => \Tools::replaceAccentedChars($breadcrumb),
              'content_name' => \Tools::replaceAccentedChars($cms->meta_title) . ' ' . $locale,
            ];
        }

        /*
        * Triggers Search for result pages
        */
        if ($page === 'search') {
            $type = \Tools::ucfirst($page);
            $content = [
              'search_string' => pSQL(\Tools::getValue('s')),
            ];
        }

        /*
        * Triggers InitiateCheckout for checkout page
        */
        if ($page === 'cart') {
            $type = 'InitiateCheckout';

            $content = [
              'num_items' => $this->context->cart->nbProducts(),
              'content_ids' => array_column($this->context->cart->getProducts(), 'id_product'),
              'content_type' => $content_type,
              'value' => (float) $this->context->cart->getOrderTotal(),
              'currency' => $currency_iso_code,
            ];
        }

        // TODO: refaco this from Quickview
        // // Decode Product Object
        // $value = Tools::jsonDecode($params['value']);
        // $locale = pSQL(Tools::strtoupper($this->context->language->iso_code));
        // $iso_code = pSQL($this->context->currency->iso_code);

        // $content = array(
        // 'content_name' => Tools::replaceAccentedChars($value->product->name) .' '.$locale,
        // 'content_ids' => array($value->product->id_product),
        // 'content_type' => 'product',
        // 'value' => (float)$value->product->price_amount,
        // 'currency' => $iso_code,
        // );
        // $content = $this->formatPixel($content);

        // $this->context->smarty->assign(array(
        // 'type' => 'ViewContent',
        // 'content' => $content,
        // ));

        // $value->quickview_html .= $this->context->smarty->fetch(
        //     $this->local_path.'views/templates/hook/displaypixel.tpl'
        // );

        // // Recode Product Object
        // $params['value'] = Tools::jsonEncode($value);

        // die($params['value']);

        $content = $this->formatPixel($content);

        $smartyVariables = [
            'pixel_fc' => $this->context->module->front_controller,
            'id_pixel' => pSQL(\Configuration::get('PS_PIXEL_ID')),
            'type' => $type,
            'content' => $content,
            'track' => $track,
        ];

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformations();
        }

        $this->context->smarty->assign($smartyVariables);

        return $this->context->module->display(__FILE__, 'views/templates/hook/header.tpl');
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

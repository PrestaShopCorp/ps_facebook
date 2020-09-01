<?php 

namespace PrestaShop\Module\PrestashopFacebook\Handler;

class PixelHandler
{
    # TODO: class that add data tpl for js front  
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
                // $this->send($event);
            break;
        }
    }

    public function sendSearchEvent($event)
    {
        // get some datas then display it for js
        return $this->display(__FILE__, 'views/templates/hook/search.tpl');
    }

    public function sendViewContentEvent($event)
    {
        $pixel_id = Configuration::get('PS_PIXEL_ID');
        if (empty($pixel_id)) {
            return;
        }

        // Asset Manager to be sure the JS is loaded
        $this->context->controller->registerJavascript(
            'front_common',
            $this->context->module->js_path.'printpixel.js', //TODO : verify path is correct
            array('position' => 'bottom', 'priority' => 150)
        );

        $type = '';
        $content = array();

        $page = $this->context->controller->php_self;
        if (empty($page)) {
            $page = Tools::getValue('controller');
        }
        $page = pSQL($page);

        $controller_type = $this->context->controller->controller_type;
        $id_lang = (int)$this->context->language->id;
        $locale = Tools::strtoupper($this->context->language->iso_code);
        $currency_iso_code = $this->context->currency->iso_code;
        $content_type = 'product';
        $track = 'track';

        /**
        * Triggers ViewContent product pages
        */
        if ($page === 'product') {
            $type = 'ViewContent';
            $prods = $this->context->controller->getTemplateVarProduct();

            $content = array(
              'content_name' => Tools::replaceAccentedChars($prods['name']) .' '.$locale,
              'content_ids' => $prods['id_product'],
              'content_type' => $content_type,
              'value' => (float) $prods['price_amount'],
              'currency' => $currency_iso_code,
            );
        }

        /**
        * Triggers ViewContent for category pages
        */
        if ($page === 'category' && $controller_type === 'front') {
            $type = 'ViewCategory';
            $category = $this->context->controller->getCategory();

            $breadcrumbs = $this->context->controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));

            $prods = $category->getProducts($id_lang, 1, 10);
            $track = 'trackCustom';

            $content = array(
              'content_name' => Tools::replaceAccentedChars($category->name).' '.$locale,
              'content_category' => Tools::replaceAccentedChars($breadcrumb),
              'content_ids' => array_column($prods, 'id_product'),
              'content_type' => $content_type,
            );
        }

        /**
        * Triggers ViewContent for cms pages
        */
        if ($page === 'cms') {
            $type = 'ViewCMS';
            $cms = new Cms((int)Tools::getValue('id_cms'), $id_lang);

            $breadcrumbs = $this->context->controller->getBreadcrumbLinks();
            $breadcrumb = implode(' > ', array_column($breadcrumbs['links'], 'title'));
            $track = 'trackCustom';

            $content = array(
              'content_category' => Tools::replaceAccentedChars($breadcrumb),
              'content_name' => Tools::replaceAccentedChars($cms->meta_title) .' '.$locale,
            );
        }

        /**
        * Triggers Search for result pages
        */
        if ($page === 'search') {
            $type = Tools::ucfirst($page);
            $content = array(
              'search_string' => pSQL(Tools::getValue('s')),
            );
        }

        /**
        * Triggers InitiateCheckout for checkout page
        */
        if ($page === 'cart') {
            $type = 'InitiateCheckout';

            $content = array(
              'num_items' => $this->context->cart->nbProducts(),
              'content_ids' => array_column($this->context->cart->getProducts(), 'id_product'),
              'content_type' => $content_type,
              'value' => (float)$this->context->cart->getOrderTotal(),
              'currency' => $iso_code,
            );
        }

        $content = $this->formatPixel($content);

        $smartyVariables = array(
            'pixel_fc' => $this->front_controller,
            'id_pixel' => pSQL(Configuration::get('PS_PIXEL_ID')),
            'type' => $type,
            'content' => $content,
            'track' => $track,
        );

        if ($this->context->customer->id) {
            $smartyVariables['userInfos'] = $this->getCustomerInformations();
        }

        $this->context->smarty->assign($smartyVariables);
        
        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    /**
     * formatPixel
     *
     * @param array $params
     *
     * @return array|false
     */
    private function formatPixel($params)
    {
        // TODO: might need some refacto/clean ? this look like a manual jsonEncode()
        if (!empty($params)) {
            $format = '{';
            foreach ($params as $key => &$val) {
                if (gettype($val) === 'string') {
                    $format .= $key.': \''.addslashes($val).'\', ';
                } elseif (gettype($val) === 'array') {
                    $format .= $key.': [\'';
                    foreach ($val as &$id) {
                        $format .= (int)$id."', '";
                    }
                    unset($id);
                    $format = Tools::substr($format, 0, -4);
                    $format .= '\'], ';
                } else {
                    $format .= $key.': '.addslashes($val).', ';
                }
            }

            $format = Tools::substr($format, 0, -2);
            $format .= '}';

            return $format;
        }

        return false;
    }
}
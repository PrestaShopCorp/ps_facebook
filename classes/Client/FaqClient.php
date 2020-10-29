<?php
/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\Ps_facebook\Api\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Ring\Exception\RingException;
use PrestaShop\Module\Ps_facebook\Helper\JsonHelper;
use PrestaShopException;

/**
 * Retrieve the faq of the module
 */
class FaqClient
{
    /**
     * Module key to identify on which module we will retrieve the faq
     *
     * @var string
     */
    private $moduleKey;

    /**
     * The version of PrestaShop
     *
     * @var string
     */
    private $psVersion;

    /**
     * In which language the faq will be retrieved
     *
     * @var string
     */
    private $isoCode;

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => 'https://api.addons.prestashop.com/request/faq/',
            'defaults' => [
                'timeout' => 10,
            ],
        ]);
    }

    /**
     * Wrapper of method post from guzzle client
     *
     * @return array
     */
    public function post()
    {
        try {
            $response = $this->client->post($this->getRoute());
        } catch (RequestException $exception) {
            throw new PrestaShopException($exception->getMessage());
        } catch (RingException $exception) {
            throw new PrestaShopException($exception->getMessage());
        }

        if (null === $response->getBody()) {
            throw new PrestaShopException('HTTP body response is empty');
        }

        $responseContents = (new JsonHelper())->jsonDecode($response->getBody()->getContents(), true);

        return [
            'httpCode' => $response->getStatusCode(),
            'body' => $responseContents,
        ];
    }

    /**
     * Generate the route to retrieve the faq
     *
     * @return string route
     */
    public function getRoute()
    {
        return $this->getModuleKey() . '/' . $this->getPsVersion() . '/' . $this->getIsoCode();
    }

    /**
     * Setter moduleKey
     *
     * @param string $moduleKey
     *
     * @return void
     */
    public function setModuleKey($moduleKey)
    {
        $this->moduleKey = $moduleKey;
    }

    /**
     * Setter psVersion
     *
     * @param string $psVersion
     *
     * @return void
     */
    public function setPsVersion($psVersion)
    {
        $this->psVersion = $psVersion;
    }

    /**
     * Setter isoCode
     *
     * @param string $isoCode
     *
     * @return void
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * Getter isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Getter psVersion
     *
     * @return string
     */
    public function getPsVersion()
    {
        return $this->psVersion;
    }

    /**
     * Getter moduleKey
     *
     * @return string
     */
    public function getModuleKey()
    {
        return $this->moduleKey;
    }
}

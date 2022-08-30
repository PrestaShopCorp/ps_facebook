<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use FacebookAds\Api;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\Client\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookConversionAPIException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;

class ApiConversionHandler
{
    /**
     * @var false|string
     */
    private $pixelId;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @var FacebookClient
     */
    private $facebookClient;

    public function __construct(
        ConfigurationAdapter $configurationAdapter,
        ErrorHandler $errorHandler,
        FacebookClient $facebookClient
    ) {
        $this->configurationAdapter = $configurationAdapter;
        $this->errorHandler = $errorHandler;

        $this->pixelId = $this->configurationAdapter->get(Config::PS_PIXEL_ID);

        Api::init(
            null, // app_id
            null, // app_secret
            $this->configurationAdapter->get(Config::PS_FACEBOOK_SYSTEM_ACCESS_TOKEN),
            false
        );
        $this->facebookClient = $facebookClient;
    }

    public function handleEvent($params)
    {
        if (empty($this->pixelId)) {
            return;
        }

        if (isset($params['event_type'])) {
            $eventType = $params['event_type'];
        }
        if (isset($params['event_time'])) {
            $eventTime = $params['event_time'];
        }
        if (isset($params['user'])) {
            $userData = $params['user'];
        }
        if (isset($params['custom_data'])) {
            $customData = $params['custom_data'];
        }
        if (isset($params['event_source_url'])) {
            $eventSourceUrl = $params['event_source_url'];
        }

        if (isset($customData) && isset($customData['contents'])) {
            $contentsData = $customData['contents'];
        }

        if (isset($contentsData)) {
            $contents = [];
            foreach ($contentsData as $contentData) {
                $content = new Content();
                if (isset($contentData['id'])) {
                    $content->setProductId($contentData['id']);
                }
                if (isset($contentData['title'])) {
                    $content->setTitle($contentData['title']);
                }
                if (isset($contentData['category'])) {
                    $content->setCategory($contentData['category']);
                }
                if (isset($contentData['item_price'])) {
                    $content->setItemPrice($contentData['item_price']);
                }
                if (isset($contentData['brand'])) {
                    $content->setBrand($contentData['brand']);
                }
                if (isset($contentData['quantity'])) {
                    $content->setQuantity($contentData['quantity']);
                }

                $contents[] = $content;
            }
        }
        if (isset($userData)) {
            $user = $this->createSdkUserData($userData);
        }

        if (isset($customData)) {
            $customDataObj = new CustomData();
            if (isset($customData['currency'])) {
                $customDataObj->setCurrency($customData['currency']);
            }
            /* more about value here: https://www.facebook.com/business/help/392174274295227?id=1205376682832142 */
            if (isset($customData['value'])) {
                $customDataObj->setValue($customData['value']);
            }
            if (isset($contents)) {
                $customDataObj->setContents($contents);
            }
            if (isset($customData['content_type'])) {
                $customDataObj->setContentType($customData['content_type']);
            }
            if (isset($customData['content_name'])) {
                $customDataObj->setContentName($customData['content_name']);
            }
            if (isset($customData['content_category'])) {
                $customDataObj->setContentCategory($customData['content_category']);
            }
            if (isset($customData['content_type'])) {
                $customDataObj->setContentType($customData['content_type']);
            }
            if (isset($customData['content_ids'])) {
                $customDataObj->setContentIds($customData['content_ids']);
            }
            if (isset($customData['num_items'])) {
                $customDataObj->setNumItems($customData['num_items']);
            }
            if (isset($customData['order_id'])) {
                $customDataObj->setOrderId($customData['order_id']);
            }
            if (isset($customData['search_string'])) {
                $customDataObj->setSearchString($customData['search_string']);
            }
            if (isset($customData['custom_properties'])) {
                $customDataObj->setCustomProperties($customData['custom_properties']);
            }
        }

        $event = new Event();
        if (isset($eventType)) {
            $event->setEventName($eventType);
        }
        if (isset($eventTime)) {
            $event->setEventTime($eventTime);
        }
        if (isset($user)) {
            $event->setUserData($user);
        }
        if (isset($customDataObj)) {
            $event->setCustomData($customDataObj);
        }
        if (isset($eventSourceUrl)) {
            $event->setEventSourceUrl($eventSourceUrl);
        }
        if (isset($params['eventID'])) {
            $event->setEventId($params['eventID']);
        }
        $event->setActionSource(ActionSource::WEBSITE);

        $this->sendEvents([$event]);
    }

    /**
     * @return UserData
     */
    protected function createSdkUserData($customerInformation)
    {
        // \Context::getContext()->cookie doesn't have fbp and fbc
        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';
        $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        return (new UserData())
            ->setFbp($fbp)
            ->setFbc($fbc)
            ->setClientIpAddress($remoteAddr)
            ->setClientUserAgent($httpUserAgent)
            ->setEmail($customerInformation['email'])
            ->setFirstName($customerInformation['firstname'])
            ->setLastName($customerInformation['lastname'])
            ->setPhone($customerInformation['phone'])
            ->setDateOfBirth($customerInformation['birthday'])
            ->setCity($customerInformation['city'])
            ->setState($customerInformation['stateIso'])
            ->setZipCode($customerInformation['postCode'])
            ->setCountryCode($customerInformation['countryIso'])
            ->setGender($customerInformation['gender']);
    }

    protected function sendEvents(array $events)
    {
        $request = (new EventRequest($this->pixelId))
            ->setEvents($events)
            ->setPartnerAgent(Config::PS_FACEBOOK_CAPI_PARTNER_AGENT);

        // A test event code can be set to check the events are properly sent to Facebook
        $testEventCode = $this->configurationAdapter->get(Config::PS_FACEBOOK_CAPI_TEST_EVENT_CODE);
        if (!empty($testEventCode)) {
            $request->setTestEventCode($testEventCode);
        }

        try {
            $request->execute();
        } catch (AuthorizationException $e) {
            if (in_array($e->getCode(), Config::OAUTH_EXCEPTION_CODE)) {
                $this->facebookClient->disconnectFromFacebook();
                $this->configurationAdapter->updateValue(Config::PS_FACEBOOK_FORCED_DISCONNECT, true);

                return false;
            }
        } catch (\Exception $e) {
            $this->errorHandler->handle(
                new FacebookConversionAPIException(
                    'Failed to send conversion API event',
                    FacebookConversionAPIException::SEND_EVENT_EXCEPTION,
                    $e
                ),
                $e->getCode(),
                false
            );
        }
    }
}

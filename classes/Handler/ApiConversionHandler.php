<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Context;
use FacebookAds\Api;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookConversionAPIException;
use PrestaShop\Module\PrestashopFacebook\Handler\ErrorHandler\ErrorHandler;

class ApiConversionHandler
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var false|string
     */
    private $pixelId;

    public function __construct()
    {
        Api::init(
            null, // app_id
            null, // app_secret
            'EAAG8FZCTh0FABAJLPh2gQxbgZBVIAmDRCPq4Ea78jmy10wzPpZAZCL9h4JDP3z1A49IUrphg79lgQ9MtZArXkKTmXhpaPvGjSIOe79msSFsQl9Ngfwl8H26WilAaYjZBfLydyouPemafBLFbY7CZBZCSAsK3HX5RPUMDQDo8TisZAHEbOdVmxJDhM'
//            \Configuration::get(Config::FB_ACCESS_TOKEN) // access_token
        );

        $this->context = Context::getContext();
        $this->pixelId = \Configuration::get(Config::PS_PIXEL_ID);
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
            $contentData = reset($customData['contents']);
        }

        if (isset($contentData)) {
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
            if (isset($content)) {
                $customDataObj->setContents([$content]);
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
        if (isset($customData)) {
            $event->setCustomData($customDataObj);
        }
        if (isset($eventSourceUrl)) {
            $event->setEventSourceUrl($eventSourceUrl);
        }

        $events[] = $event;

        if (empty($event)) {
            return true;
        }

        $this->sendEvents($events);
    }

    /**
     * @return UserData
     */
    protected function createSdkUserData($customerInformation)
    {
        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';

        return (new UserData())
            ->setFbp($fbp)
            ->setFbc($fbc)
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
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
            ->setTestEventCode('TEST39621');

        try {
            $request->execute();
        } catch (\Exception $e) {
            $errorHandler = new ErrorHandler();
            $errorHandler->handle(
                new FacebookConversionAPIException(
                    'Failed to send conversion API event',
                    FacebookConversionAPIException::SEND_EVENT_EXCEPTION,
                    $e
                ),
                FacebookConversionAPIException::SEND_EVENT_EXCEPTION,
                false
            );
        }
    }
}

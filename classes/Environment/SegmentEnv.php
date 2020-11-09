<?php

namespace PrestaShop\Module\Ps_facebook\Environment;

class SegmentEnv extends Env
{
    /**
     * Firebase public api key
     *
     * @var string
     */
    private $segmentApiKey;

    public function __construct()
    {
        parent::__construct();

        $this->setSegmentApiKey($_ENV['SEGMENT_API_KEY']);
    }

    /**
     * getter for segmentApiKey
     */
    public function getSegmentApiKey()
    {
        return $this->segmentApiKey;
    }

    /**
     * setter for segmentApiKey
     *
     * @param string $apiKey
     */
    private function setSegmentApiKey($apiKey)
    {
        $this->segmentApiKey = $apiKey;
    }
}

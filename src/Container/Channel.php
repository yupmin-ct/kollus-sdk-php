<?php

namespace Kollus\Component\Container;

use Kollus\Component\Client;
use Kollus\Component\KollusClient;

class Channel extends AbstractContainer
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $key;

    /**
     * @var integer
     */
    private $count_of_media_contents;

    /**
     * @var integer
     */
    private $use_pingback;

    /**
     * @var integer
     */
    private $status;

    /**
     * Channel constructor.
     * @param object|array $items
     */
    public function __construct($items = [])
    {
        $items = (array)$items;
        if (isset($items['channel_name'])) {
            $items['name'] = $items['channel_name'];
            unset($items['channel_name']);
        }

        if (isset($items['channel_key'])) {
            $items['key'] = $items['channel_key'];
            unset($items['channel_key']);
        }
        parent::__construct($items);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getCountOfMediaContents()
    {
        return $this->count_of_media_contents;
    }

    /**
     * @param int $count_of_media_contents
     */
    public function setCountOfMediaContents($count_of_media_contents)
    {
        $this->count_of_media_contents = $count_of_media_contents;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getUsePingback()
    {
        return $this->use_pingback;
    }

    /**
     * @param int $use_pingback
     */
    public function setUsePingback($use_pingback)
    {
        $this->use_pingback = $use_pingback;
    }

    /**
     * @param int $page
     * @param array $getParams
     * @param bool $force
     * @return object
     * @throws ContainerException|Client\ClientException
     */
    public function findChannelMediaContentsByPage($page = 1, $getParams = [], $force = false)
    {
        $client = KollusClient::getDefaultClient();

        $response = (object) [];
        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $channelKey = $this->getKey();
            if (empty($channelKey)) {
                throw new ContainerException('Channel key is empty.');
            }
            $response = $client->findChannelMediaContentsByPage($channelKey, $page, $getParams, $force);
        }

        return $response;
    }

    /**
     * @param array $getParams
     * @param bool $force
     * @return MediaContent[]
     * @throws ContainerException
     */
    public function getChannelMediaContents(array $getParams = [], $force = false)
    {
        $client = KollusClient::getDefaultClient();

        $mediaContents = [];
        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $channelKey = $this->getKey();
            if (empty($channelKey)) {
                throw new ContainerException('Channel key is empty.');
            }
            $mediaContents = $client->getChannelMediaContents($channelKey, $getParams, $force);
        }

        return $mediaContents;
    }

    /**
     * @return self
     * @throws ContainerException
     */
    public function delete()
    {
        $client = KollusClient::getDefaultClient();

        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $channelKey = $this->getKey();
            if (empty($channelKey)) {
                throw new ContainerException('Channel key is empty.');
            }
            $client->deleteChannel($channelKey);
        }

        return $this;
    }
}

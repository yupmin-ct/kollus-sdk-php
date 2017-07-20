<?php

namespace Kollus\Component\Container;

use Kollus\Component\KollusClient;
use Kollus\Component\Client;

class ServiceAccount extends AbstractContainer
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $security_key;

    /**
     * @var string
     */
    protected $custom_key;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $api_access_token;

    /**
     * @var \ArrayObject
     */
    protected $categories;

    /**
     * ServiceAccount constructor.
     * @param object|array $items
     */
    public function __construct($items = [])
    {
        $this->categories = new ContainerArray();

        parent::__construct($items);
    }

    /**
     * @return string
     */
    public function getApiAccessToken()
    {
        return $this->api_access_token;
    }

    /**
     * @param string $api_access_token
     */
    public function setApiAccessToken($api_access_token)
    {
        $this->api_access_token = $api_access_token;
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
     * @return string
     * @throws ContainerException
     */
    public function getSecurityKey()
    {
        if (isset($this->security_key) && !empty($this->security_key)) {
            return (string)$this->security_key;
        } elseif (isset($this->key) && !empty($this->key)) {
            return (string)$this->key;
        }

        throw new ContainerException('Property "security_key" or "key" does not exist');
    }

    /**
     * @param string $security_key
     */
    public function setSecurityKey($security_key)
    {
        $this->security_key = $security_key;
    }

    /**
     * @return string
     */
    public function getCustomKey()
    {
        return $this->custom_key;
    }

    /**
     * @param string $custom_key
     */
    public function setCustomKey($custom_key)
    {
        $this->custom_key = $custom_key;
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
     * Add category
     *
     * @param Category $category
     *
     * @return ServiceAccount
     */
    public function addCategory(Category $category)
    {
        $this->categories->appendElement($category);

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     * @return ServiceAccount
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @param array $getParams
     * @return ContainerArray
     * @throws ContainerException
     */
    public function getCategories(array $getParams = [])
    {
        $client = KollusClient::getDefaultClient();

        $categories = new ContainerArray();
        if (KollusClient::getDefaultClientName() == Client\ApiClient::class) {
            $categories = $client->getCategories($getParams);
        }

        return $categories;
    }

    /**
     * @param array $getParams
     * @return ContainerArray
     * @throws ContainerException
     */
    public function getChannels(array $getParams = [])
    {
        $client = KollusClient::getDefaultClient();

        $channels = new ContainerArray();
        if (KollusClient::getDefaultClientName() == Client\ApiClient::class) {
            $channels = $client->getChannels($getParams);
        }

        return $channels;
    }

    /**
     * @param array $getParams
     * @return ContainerArray
     * @throws ContainerException
     */
    public function getLibraryMediaContents(array $getParams = [])
    {
        $client = KollusClient::getDefaultClient();

        $mediaContents = new ContainerArray();
        if (KollusClient::getDefaultClientName() == Client\ApiClient::class) {
            $mediaContents = $client->getLibraryMediaContents($getParams);
        }

        return $mediaContents;
    }

    /**
     * @param array $getParams
     * @param string $channelKey
     * @return ContainerArray
     * @throws ContainerException
     */
    public function getChannelMediaContents($channelKey, array $getParams = [])
    {
        $client = KollusClient::getDefaultClient();

        $mediaContents = new ContainerArray();
        if (KollusClient::getDefaultClientName() == Client\ApiClient::class) {
            $mediaContents = $client->getChannelMediaContents($channelKey, $getParams);
        }

        return $mediaContents;
    }
}

<?php

namespace Kollus\Component\Container;

use Kollus\Component\Client;
use Kollus\Component\KollusClient;

class Category extends AbstractContainer
{
    /**
     * @var int
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
     * @var int|bool
     */
    private $parend_id;

    /**
     * @var int
     */
    private $count_of_media_contents;

    /**
     * @var int
     */
    private $level;

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
    public function getParendId()
    {
        return (int)$this->parend_id;
    }

    /**
     * @param bool|int $parend_id
     */
    public function setParendId($parend_id)
    {
        $this->parend_id = $parend_id;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     * @throws ContainerException
     */
    public function getCountOfMediaContents()
    {
        return (int)$this->count_of_media_contents;
    }

    /**
     * @param int $count_of_media_contents
     */
    public function setCountOfMediaContents($count_of_media_contents)
    {
        $this->count_of_media_contents = $count_of_media_contents;
    }

    /**
     * @param int $page
     * @param array $getParams
     * @param bool $force
     * @return object
     * @throws ContainerException|Client\ClientException
     */
    public function findLibraryMediaContentsByPage($page = 1, $getParams = [], $force = false)
    {
        $client = KollusClient::getDefaultClient();

        $response = (object) [];
        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $categoryKey = $this->getKey();
            if (empty($categoryKey)) {
                throw new ContainerException('Channel key is empty.');
            }
            $getParams['category_key'] = $categoryKey;
            $response = $client->findLibraryMediaContentsByPage($page, $getParams, $force);
        }

        return $response;
    }

    /**
     * @param array $getParams
     * @param bool $force
     * @return MediaContent[]
     * @throws ContainerException
     */
    public function getLibraryMediaContents(array $getParams = [], $force = false)
    {
        $client = KollusClient::getDefaultClient();

        $mediaContents = [];
        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $categoryKey = $this->getKey();
            if (empty($categoryKey)) {
                throw new ContainerException('Category key is empty.');
            }
            $getParams['category_key'] = $categoryKey;
            $mediaContents = $client->getLibraryMediaContents($getParams, $force);
        }

        return $mediaContents;
    }

    /**
     * @param array $data
     * @return self
     * @throws ContainerException
     */
    public function edit($data)
    {
        $client = KollusClient::getDefaultClient();

        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $categoryKey = $this->getKey();
            if (empty($categoryKey)) {
                throw new ContainerException('Category key is empty.');
            }
            $client->editCategory($categoryKey, $data);
        }

        return $this;
    }

    /**
     * @return self
     * @throws ContainerException
     */
    public function delete()
    {
        $client = KollusClient::getDefaultClient();

        if (KollusClient::getDefaultClientName() == 'Kollus\Component\Client\ApiClient') {
            $categoryKey = $this->getKey();
            if (empty($categoryKey)) {
                throw new ContainerException('Category key is empty.');
            }
            $client->deleteCategory($categoryKey);
        }

        return $this;
    }
}

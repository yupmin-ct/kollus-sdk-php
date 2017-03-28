<?php

namespace Kollus\Component\Container;

class KollusPlayer extends AbstractContainer
{
    /**
     * @var string
     */
    private $file_url;

    /**
     * @var string
     */
    private $version;

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return $this->file_url;
    }

    /**
     * @param string $file_url
     */
    public function setFileUrl($file_url)
    {
        $this->file_url = $file_url;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}

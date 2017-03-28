<?php

namespace Kollus\Component\Container;

class Subtitle extends AbstractContainer
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $language_id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $subtitle_path;

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
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * @param int $language_id
     */
    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;
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
    public function getSubtitlePath()
    {
        return $this->subtitle_path;
    }

    /**
     * @param string $subtitle_path
     */
    public function setSubtitlePath($subtitle_path)
    {
        $this->subtitle_path = $subtitle_path;
    }
}

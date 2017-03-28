<?php

namespace Kollus\Component\Container;

class MediaItem extends AbstractContainer
{
    /**
     * @var string
     */
    private $media_content_key;

    /**
     * @var string
     */
    private $profile_key;

    /**
     * @var int
     */
    private $is_intro;

    /**
     * @var int
     */
    private $is_seekable;

    /**
     * @return string
     */
    public function getMediaContentKey()
    {
        return $this->media_content_key;
    }

    /**
     * @param string $media_content_key
     */
    public function setMediaContentKey($media_content_key)
    {
        $this->media_content_key = $media_content_key;
    }

    /**
     * @return string
     */
    public function getProfileKey()
    {
        return $this->profile_key;
    }

    /**
     * @param string $profile_key
     */
    public function setProfileKey($profile_key)
    {
        $this->profile_key = $profile_key;
    }

    /**
     * @return int
     */
    public function getIsIntro()
    {
        return $this->is_intro;
    }

    /**
     * @param int $is_intro
     */
    public function setIsIntro($is_intro)
    {
        $this->is_intro = $is_intro;
    }

    /**
     * @return int
     */
    public function getIsSeekable()
    {
        return $this->is_seekable;
    }

    /**
     * @param int $is_seekable
     */
    public function setIsSeekable($is_seekable)
    {
        $this->is_seekable = $is_seekable;
    }
}

<?php

namespace Kollus\Component\Container;

class TranscodingFile extends AbstractContainer
{

    /**
     * @var MediaProfileGroup
     */
    private $media_profile_group;

    /**
     * @var string
     */
    private $profile_name;

    /**
     * @var string
     */
    private $profile_key;

    /**
     * @var Object;
     */
    private $thumbnail_urls;

    /**
     * @var MediaInformation
     */
    private $media_information;

    /**
     * @var integer
     */
    private $created_at;

    /**
     * @var integer
     */
    private $updated_at;

    /**
     * MediaContent constructor.
     * @param array|object $items
     */
    public function __construct($items = [])
    {
        $items = (array)$items;

        if (isset($items['media_profile_group_name']) && isset($items['media_profile_group_key'])) {
            $this->media_profile_group = new MediaProfileGroup([
                'name' => $items['media_profile_group_name'],
                'key' => $items['media_profile_group_key'],
            ]);

            unset($items['media_profile_group_name']);
            unset($items['media_profile_group_key']);
        }

        if (isset($items['media_information'])) {
            $this->media_information = new MediaInformation($items['media_information']);
            unset($items['media_information']);
        }

        parent::__construct($items);
    }

    /**
     * @return MediaProfileGroup
     */
    public function getMediaProfileGroup()
    {
        return $this->media_profile_group;
    }

    /**
     * @param MediaProfileGroup $media_profile_group
     */
    public function setMediaProfileGroup($media_profile_group)
    {
        $this->media_profile_group = $media_profile_group;
    }

    /**
     * @return string
     */
    public function getProfileName()
    {
        return $this->profile_name;
    }

    /**
     * @param string $profile_name
     */
    public function setProfileName($profile_name)
    {
        $this->profile_name = $profile_name;
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
     * @return Object
     */
    public function getThumbnailUrls()
    {
        return $this->thumbnail_urls;
    }

    /**
     * @param Object $thumbnail_urls
     */
    public function setThumbnailUrls($thumbnail_urls)
    {
        $this->thumbnail_urls = $thumbnail_urls;
    }

    /**
     * @return MediaInformation
     */
    public function getMediaInformation()
    {
        return $this->media_information;
    }

    /**
     * @param MediaInformation $media_information
     */
    public function setMediaInformation($media_information)
    {
        $this->media_information = $media_information;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param int $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param int $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
}

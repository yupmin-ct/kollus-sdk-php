<?php

namespace Kollus\Component\Container;

class MediaProfile extends AbstractContainer
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
     * @var string
     */
    private $media_profile_group_key;

    /**
     * @var string
     */
    private $media_profile_group_name;

    /**
     * @var int
     */
    private $is_default_preset;

    /**
     * @var int
     */
    private $is_optional;

    /**
     * @var string
     */
    private $container_format;

    /**
     * @var string
     */
    private $video_codec;

    /**
     * @var int
     */
    private $video_bitrate;

    /**
     * @var int
     */
    private $video_width;

    /**
     * @var int
     */
    private $video_height;

    /**
     * @var string
     */
    private $video_framerate;

    /**
     * @var int
     */
    private $video_rotate_degree;

    /**
     * @var string
     */
    private $video_overlay_position;

    /**
     * @var string
     */
    private $video_overlay_path;

    /**
     * @var int
     */
    private $is_video_ratio;

    /**
     * @var int
     */
    private $is_video_vertical_flip;

    /**
     * @var int
     */
    private $is_video_horizontal_flip;

    /**
     * @var string
     */
    private $audio_codec;

    /**
     * @var int
     */
    private $audio_bitrate;

    /**
     * @var int
     */
    private $audio_samplerate;

    /**
     * @var int
     */
    private $audio_channel;

    /**
     * @var int
     */
    private $audio_volume_control;

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
     * @return string
     */
    public function getMediaProfileGroupKey()
    {
        return $this->media_profile_group_key;
    }

    /**
     * @param string $media_profile_group_key
     */
    public function setMediaProfileGroupKey($media_profile_group_key)
    {
        $this->media_profile_group_key = $media_profile_group_key;
    }

    /**
     * @return string
     */
    public function getMediaProfileGroupName()
    {
        return $this->media_profile_group_name;
    }

    /**
     * @param string $media_profile_group_name
     */
    public function setMediaProfileGroupName($media_profile_group_name)
    {
        $this->media_profile_group_name = $media_profile_group_name;
    }

    /**
     * @return MediaProfileGroup
     */
    public function getMediaProfileGroup()
    {
        $mediaProfileGroup = new MediaProfileGroup();
        $mediaProfileGroup->setKey($this->media_profile_group_key);
        $mediaProfileGroup->setName($this->media_profile_group_name);

        return $mediaProfileGroup;
    }

    /**
     * @return int
     */
    public function getIsDefaultPreset()
    {
        return $this->is_default_preset;
    }

    /**
     * @param int $is_default_preset
     */
    public function setIsDefaultPreset($is_default_preset)
    {
        $this->is_default_preset = $is_default_preset;
    }

    /**
     * @return int
     */
    public function getIsOptional()
    {
        return $this->is_optional;
    }

    /**
     * @param int $is_optional
     */
    public function setIsOptional($is_optional)
    {
        $this->is_optional = $is_optional;
    }

    /**
     * @return string
     */
    public function getContainerFormat()
    {
        return $this->container_format;
    }

    /**
     * @param string $container_format
     */
    public function setContainerFormat($container_format)
    {
        $this->container_format = $container_format;
    }

    /**
     * @return string
     */
    public function getVideoCodec()
    {
        return $this->video_codec;
    }

    /**
     * @param string $video_codec
     */
    public function setVideoCodec($video_codec)
    {
        $this->video_codec = $video_codec;
    }

    /**
     * @return int
     */
    public function getVideoBitrate()
    {
        return $this->video_bitrate;
    }

    /**
     * @param int $video_bitrate
     */
    public function setVideoBitrate($video_bitrate)
    {
        $this->video_bitrate = $video_bitrate;
    }

    /**
     * @return int
     */
    public function getVideoWidth()
    {
        return $this->video_width;
    }

    /**
     * @param int $video_width
     */
    public function setVideoWidth($video_width)
    {
        $this->video_width = $video_width;
    }

    /**
     * @return int
     */
    public function getVideoHeight()
    {
        return $this->video_height;
    }

    /**
     * @param int $video_height
     */
    public function setVideoHeight($video_height)
    {
        $this->video_height = $video_height;
    }

    /**
     * @return string
     */
    public function getVideoFramerate()
    {
        return $this->video_framerate;
    }

    /**
     * @param string $video_framerate
     */
    public function setVideoFramerate($video_framerate)
    {
        $this->video_framerate = $video_framerate;
    }

    /**
     * @return int
     */
    public function getVideoRotateDegree()
    {
        return $this->video_rotate_degree;
    }

    /**
     * @param int $video_rotate_degree
     */
    public function setVideoRotateDegree($video_rotate_degree)
    {
        $this->video_rotate_degree = $video_rotate_degree;
    }

    /**
     * @return string
     */
    public function getVideoOverlayPosition()
    {
        return $this->video_overlay_position;
    }

    /**
     * @param string $video_overlay_position
     */
    public function setVideoOverlayPosition($video_overlay_position)
    {
        $this->video_overlay_position = $video_overlay_position;
    }

    /**
     * @return string
     */
    public function getVideoOverlayPath()
    {
        return $this->video_overlay_path;
    }

    /**
     * @param string $video_overlay_path
     */
    public function setVideoOverlayPath($video_overlay_path)
    {
        $this->video_overlay_path = $video_overlay_path;
    }

    /**
     * @return int
     */
    public function getIsVideoRatio()
    {
        return $this->is_video_ratio;
    }

    /**
     * @param int $is_video_ratio
     */
    public function setIsVideoRatio($is_video_ratio)
    {
        $this->is_video_ratio = $is_video_ratio;
    }

    /**
     * @return int
     */
    public function getIsVideoVerticalFlip()
    {
        return $this->is_video_vertical_flip;
    }

    /**
     * @param int $is_video_vertical_flip
     */
    public function setIsVideoVerticalFlip($is_video_vertical_flip)
    {
        $this->is_video_vertical_flip = $is_video_vertical_flip;
    }

    /**
     * @return int
     */
    public function getIsVideoHorizontalFlip()
    {
        return $this->is_video_horizontal_flip;
    }

    /**
     * @param int $is_video_horizontal_flip
     */
    public function setIsVideoHorizontalFlip($is_video_horizontal_flip)
    {
        $this->is_video_horizontal_flip = $is_video_horizontal_flip;
    }

    /**
     * @return string
     */
    public function getAudioCodec()
    {
        return $this->audio_codec;
    }

    /**
     * @param string $audio_codec
     */
    public function setAudioCodec($audio_codec)
    {
        $this->audio_codec = $audio_codec;
    }

    /**
     * @return int
     */
    public function getAudioBitrate()
    {
        return $this->audio_bitrate;
    }

    /**
     * @param int $audio_bitrate
     */
    public function setAudioBitrate($audio_bitrate)
    {
        $this->audio_bitrate = $audio_bitrate;
    }

    /**
     * @return int
     */
    public function getAudioSamplerate()
    {
        return $this->audio_samplerate;
    }

    /**
     * @param int $audio_samplerate
     */
    public function setAudioSamplerate($audio_samplerate)
    {
        $this->audio_samplerate = $audio_samplerate;
    }

    /**
     * @return int
     */
    public function getAudioChannel()
    {
        return $this->audio_channel;
    }

    /**
     * @param int $audio_channel
     */
    public function setAudioChannel($audio_channel)
    {
        $this->audio_channel = $audio_channel;
    }

    /**
     * @return int
     */
    public function getAudioVolumeControl()
    {
        return $this->audio_volume_control;
    }

    /**
     * @param int $audio_volume_control
     */
    public function setAudioVolumeControl($audio_volume_control)
    {
        $this->audio_volume_control = $audio_volume_control;
    }
}

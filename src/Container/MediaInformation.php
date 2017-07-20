<?php

namespace Kollus\Component\Container;

class MediaInformation extends AbstractContainer
{
    /**
     * @var object
     */
    private $file;

    /**
     * @var object
     */
    private $video;

    /**
     * @var object
     */
    private $audio;

    /**
     * @return object
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return object
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @return object
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * @param object $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @param object $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @param object $audio
     */
    public function setAudio($audio)
    {
        $this->audio = $audio;
    }
}

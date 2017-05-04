<?php

namespace kollus\Component;

use Kollus\Component\Container\Channel;

class Callback
{
    /**
     * @param \Closure $callable
     */
    public function upload(\Closure $callable)
    {
        $uploadFileKey = isset($_POST['upload_file_key']) ? $_POST['upload_file_key'] : null;
        $filename = isset($_POST['filename']) ? $_POST['filename'] : null;
        $contentProviderKey = isset($_POST['content_provider_key']) ? $_POST['content_provider_key'] : null;

        $callable($uploadFileKey, $filename, $contentProviderKey);
    }

    /**
     * @param \Closure $callable
     */
    public function transcoding(\Closure $callable)
    {
        $uploadFileKey = isset($_POST['upload_file_key']) ? $_POST['upload_file_key'] : null;
        $filename = isset($_POST['filename']) ? $_POST['filename'] : null;
        $transcodingResult = isset($_POST['transcoding_result']) ? $_POST['transcoding_result'] : null;
        $contentProviderKey = isset($_POST['content_provider_key']) ? $_POST['content_provider_key'] : null;

        $callable($uploadFileKey, $filename, $transcodingResult, $contentProviderKey);
    }

    /**
     * @param \Closure $callable
     */
    public function channel(\Closure $callable)
    {
        $uploadFileKey = isset($_POST['upload_file_key']) ? $_POST['upload_file_key'] : null;
        $filename = isset($_POST['filename']) ? $_POST['filename'] : null;
        $mediaContentKey = isset($_POST['media_content_key']) ? $_POST['media_content_key'] : null;
        $updateType = isset($_POST['update_type']) ? $_POST['update_type'] : null;
        $contentProviderKey = isset($_POST['content_provider_key']) ? $_POST['content_provider_key'] : null;

        $channelKey = isset($_POST['channel_key']) ? $_POST['channel_key'] : null;
        $channelName = isset($_POST['channel_name']) ? $_POST['channel_name'] : null;
        $channel = new Channel([
            'key' => $channelKey,
            'name' => $channelName,
        ]);

        $callable($uploadFileKey, $filename, $mediaContentKey, $updateType, $channel, $contentProviderKey);
    }

    /**
     * @param \Closure $callable
     */
    public function progress(\Closure $callable)
    {
        // TODO
    }

    /**
     * @param \Closure $callable
     */
    public function play(\Closure $callable)
    {
        // TODO
    }

    /**
     * @param \Closure $callable
     */
    public function drm(\Closure $callable)
    {
        // TODO
    }
}

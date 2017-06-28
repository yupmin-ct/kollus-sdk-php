<?php

namespace Kollus\Component;

use Firebase\JWT\JWT;
use Kollus\Component\Container;

class Callback
{
    /**
     * @var array|object
     */
    private $data = [];

    /**
     * @var Container\ServiceAccount $serviceAccount
     */
    protected $serviceAccount;

    /**
     * Callback constructor.
     * @param Container\ServiceAccount $serviceAccount
     */
    public function __construct(Container\ServiceAccount $serviceAccount)
    {
        $this->serviceAccount = $serviceAccount;
    }

    /**
     * @return string
     */
    public function getJwtData()
    {
        return JWT::encode($this->data, $this->serviceAccount->getSecurityKey());
    }

    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getCustomKey()
    {
        return $this->serviceAccount->getCustomKey();
    }

    /**
     * @param \Closure $callable
     * @return self
     */
    public function upload(\Closure $callable)
    {
        $uploadFileKey = isset($_POST['upload_file_key']) ? $_POST['upload_file_key'] : null;
        $filename = isset($_POST['filename']) ? $_POST['filename'] : null;
        $contentProviderKey = isset($_POST['content_provider_key']) ? $_POST['content_provider_key'] : null;

        $callable($uploadFileKey, $filename, $contentProviderKey);

        return $this;
    }

    /**
     * @param \Closure $callable
     * @return self
     */
    public function transcoding(\Closure $callable)
    {
        $uploadFileKey = isset($_POST['upload_file_key']) ? $_POST['upload_file_key'] : null;
        $filename = isset($_POST['filename']) ? $_POST['filename'] : null;
        $transcodingResult = isset($_POST['transcoding_result']) ? $_POST['transcoding_result'] : null;
        $contentProviderKey = isset($_POST['content_provider_key']) ? $_POST['content_provider_key'] : null;

        $callable($uploadFileKey, $filename, $transcodingResult, $contentProviderKey);

        return $this;
    }

    /**
     * @param \Closure $callable
     * @return self
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
        $channel = new Container\Channel([
            'key' => $channelKey,
            'name' => $channelName,
        ]);

        $callable($uploadFileKey, $filename, $mediaContentKey, $updateType, $channel, $contentProviderKey);

        return $this;
    }

    /**
     * @param \Closure $callable
     * @return self
     */
    public function progress(\Closure $callable, $key = 'json_data')
    {
        if (isset($_POST[$key])) {
            $data = json_decode($_POST[$key]);

            $userInfo = $data->user_info;
            $contentInfo = $data->content_info;
            $blockInfo = $data->block_info;

            $progressBlocks = Container\ProgressBlock::importFromBlockInfo($blockInfo);

            $uservalues = isset($data->uservalues) ? new Container\Uservalues($data->uservalues) : null;
            $startAt = isset($contentInfo->start_at) ? $contentInfo->start_at : null;

            $mediaContentKey = isset($contentInfo->media_content_key) ? $contentInfo->media_content_key : null;
            $clientUserId = isset($userInfo->client_user_id) ? $userInfo->client_user_id : null;
            $playerId = isset($userInfo->player_id) ? $userInfo->player_id : null;

            $callable($clientUserId, $playerId, $mediaContentKey, $startAt, $progressBlocks, $uservalues, $data);
        }

        return $this;
    }

    /**
     * @param \Closure $callable
     * @return self
     */
    public function play(\Closure $callable)
    {
        $this->data = [];

        $kind = isset($_POST['kind']) ? (int)$_POST['kind'] : null;
        $clientUserId = isset($_POST['client_user_id']) ? $_POST['client_user_id'] : null;
        $playerId = isset($_POST['player_id']) ? $_POST['player_id'] : null;
        $mediaContentKey = isset($_POST['media_content_key']) ? $_POST['media_content_key'] : null;
        $uservalues = isset($_POST['uservalues']) ?
            new Container\Uservalues(json_decode($_POST['uservalues'])) : null;

        $data['device_name'] = isset($_POST['deviceName']) ? $_POST['deviceName'] : null;
        $data['hardware_id'] = isset($_POST['hardware_id']) ? $_POST['hardware_id'] : null;

        $this->data = $callable($kind, $clientUserId, $playerId, $mediaContentKey, $uservalues, $data);

        return $this;
    }

    /**
     * @param \Closure $callable
     * @return self
     */
    public function drm(\Closure $callable)
    {
        $this->data = [];

        $items = isset($_POST['items']) ? $_POST['items'] : '';
        $items = empty($items) ? [] : json_decode($items, true);

        $resultItems = [];
        foreach ($items as $item) {
            $kind = isset($item['kind']) ? (int)$item['kind'] : null;
            $clientUserId = isset($item['client_user_id']) ? $item['client_user_id'] : null;
            $playerId = isset($item['player_id']) ? $item['player_id'] : null;
            $mediaContentKey = isset($item['media_content_key']) ? $item['media_content_key'] : null;
            $uservalues = isset($_POST['uservalues']) ?
                new Container\Uservalues(json_decode($_POST['uservalues'])) : null;

            $data['device_name'] = isset($item['deviceName']) ? $item['deviceName'] : null;
            $data['hardware_id'] = isset($item['hardware_id']) ? $item['hardware_id'] : null;
            $data['session_key'] = isset($item['session_key']) ? $item['session_key'] : null;
            $data['start_at'] = isset($item['start_at']) ? $item['start_at'] : null;
            $data['content_expired'] = isset($item['content_expired']) ? $item['content_expired'] : null;
            $data['reset_req'] = isset($item['reset_req']) ? $item['reset_req'] : null;

            $resultItem = $callable($kind, $clientUserId, $playerId, $mediaContentKey, $uservalues, $data);

            if (!empty($resultItem)) {
                $resultItems['data'][] = $resultItem;
            }
        }

        $this->data = $resultItems;

        return $this;
    }
}

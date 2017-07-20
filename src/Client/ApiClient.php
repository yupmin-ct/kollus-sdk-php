<?php

namespace Kollus\Component\Client;

use GuzzleHttp\Client as HttpClient;
use Kollus\Component\Container;

class ApiClient extends AbstractClient
{
    /**
     * @param string $method
     * @param string $uri
     * @param array $optParams
     * @param array $postParams
     * @param array $getParams
     * @param int $retry
     * @return mixed
     * @throws ClientException
     */
    public function getResponseJSON(
        $method,
        $uri,
        array $getParams = [],
        array $postParams = [],
        array $optParams = [],
        $retry = 3
    ) {
        if (count($getParams) > 0) {
            $optParams['query'] = $getParams;
        }
        if (count($postParams) > 0) {
            $optParams['form_params'] = $postParams;
        }
        if (!isset($optParams['timeout'])) {
            $optParams['timeout'] = $this->optParams['timeout'];
        }

        do {
            $response = $this->client->request($method, $uri, $optParams);
            $statusCode = $response->getStatusCode();
            $retry --;
        } while ($statusCode != 200 && $retry > 0);

        $jsonResponse = json_decode($response->getBody()->getContents());

        if ($jsonResponse === false || (isset($jsonResponse->error) && (int)$jsonResponse->error === 1)) {
            $message = isset($jsonResponse->message) ? $jsonResponse->message : $response->getBody()->getContents();
            throw new ClientException($message, $statusCode);
        }

        return $jsonResponse;
    }

    /**
     * @param mixed|null $client
     * @return self
     * @throws ClientException
     */
    public function connect($client = null)
    {
        if (is_subclass_of($this->serviceAccount, Container\ServiceAccount::class)) {
            throw new ClientException('Service account is required.');
        }

        $serviceAccountKey = $this->serviceAccount->getKey();
        if (empty($serviceAccountKey)) {
            throw new ClientException('Service account key is empty.');
        }

        $apiAccessToken = $this->serviceAccount->getApiAccessToken();
        if (empty($apiAccessToken)) {
            throw new ClientException('Access token is empty.');
        }

        if (is_null($client)) {
            $this->client = new HttpClient([
                'base_uri' => $this->schema . '://api.' . $this->domain . '/' . $this->version . '/',
                'defaults' => ['allow_redirects' => false],
                'verify' => false,
            ]);
        } else {
            $this->client = $client;
        }

        return $this;
    }

    /**
     * @return self
     * @throws ClientException
     */
    public function disconnect()
    {
        unset($this->client);
        return $this;
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     * @throws ClientException
     */
    public function getMediaProfileGroups(array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/media_profile_group', $getParams);

        if (isset($response->error) && $response->error) {
            throw new ClientException($response->message);
        } elseif (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $mediaProfileGroups = new Container\ContainerArray();
        foreach ($items as $item) {
            $mediaProfileGroups->appendElement(new Container\MediaProfileGroup($item));
        }

        return $mediaProfileGroups;
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     * @throws ClientException
     */
    public function getMediaProfiles(array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/media_profile', $getParams);

        if (isset($response->error) && $response->error) {
            throw new ClientException($response->message);
        } elseif (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $mediaProfiles = new Container\ContainerArray();
        foreach ($items as $item) {
            $mediaProfiles->appendElement(new Container\MediaProfile($item));
        }

        return $mediaProfiles;
    }

    /**
     * @param int $mediaProfilePresetId
     * @return self
     * @throws ClientException
     */
    public function createMediaProfile($mediaProfilePresetId)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = ['media_profile_preset_id' => $mediaProfilePresetId];
        $this->getResponseJSON('POST', 'media/media_profile/create.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param int $mediaProfileId
     * @param array $postParams
     * @return $this
     * @throws ClientException
     */
    public function editMediaProfile($mediaProfileId, array $postParams = [])
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON(
            'POST',
            'media/media_profile/edit/' . $mediaProfileId . '.json',
            $getParams,
            $postParams
        );

        return $this;
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     * @throws ClientException
     */
    public function getMediaProfilePresets(array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/media_profile/preset', $getParams);

        if (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $mediaProfiles = new Container\ContainerArray();
        foreach ($items as $item) {
            $mediaProfiles->appendElement(new Container\MediaProfile($item));
        }

        return $mediaProfiles;
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     * @throws ClientException
     */
    public function getCategories(array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/category', $getParams);

        if (isset($response->error) && $response->error) {
            throw new ClientException($response->message);
        } elseif (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $categories = new Container\ContainerArray();
        foreach ($items as $item) {
            $categories->appendElement(new Container\Category($item));
        }

        return $categories;
    }

    /**
     * @param string $categoryName
     * @param int|null $parentId
     * @return self
     * @throws ClientException
     */
    public function createCategory($categoryName, $parentId = null)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = ['name' => $categoryName];
        if (!empty($parentId)) {
            $postParams['parent_id'] = $parentId;
        }
        $this->getResponseJSON('POST', 'media/category/create.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param string $categoryKey
     * @param array $postParams
     * @return self
     * @throws ClientException
     */
    public function editCategory($categoryKey, array $postParams = [])
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON('POST', 'media/category/edit/' . $categoryKey . '.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param string $categoryKey
     * @return self
     * @throws ClientException
     */
    public function deleteCategory($categoryKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON('POST', 'media/category/delete/' . $categoryKey . '.json', $getParams);

        return $this;
    }

    /**
     * @param string $uploadFileKey
     * @param bool $force
     * @return Container\MediaContent|null
     * @throws ClientException
     */
    public function getLibraryMediaContent($uploadFileKey, $force = false)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken(), 'force' => (int)$force];
        $response = $this->getResponseJSON('GET', 'media/library/media_content/' . $uploadFileKey, $getParams);

        if (isset($response->error) && $response->error) {
            throw new ClientException($response->message);
        } elseif (!isset($response->result->item)) {
            throw new ClientException('Response is invalid.');
        }

        return new Container\MediaContent($response->result->item);
    }

    /**
     * @param int $page
     * @param array $getParams
     * @param bool|false $force
     * @return object
     * @throws ClientException
     */
    public function findLibraryMediaContentsByPage($page = 1, array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['page'] = $page;
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/library/media_content', $getParams);

        if (isset($response->error) && $response->error) {
            throw new ClientException($response->message);
        } elseif (isset($response->result->per_page) && isset($response->result->count) &&
            isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $mediaContents = new Container\ContainerArray();
        foreach ($items as $item) {
            $mediaContents->appendElement(new Container\MediaContent($item));
        }

        return (object)[
            'per_page' => $response->result->per_page,
            'count' => $response->result->count,
            'items' => $mediaContents,
        ];
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     */
    public function getLibraryMediaContents(array $getParams = [], $force = false)
    {
        if (!isset($getParams['per_page'])) {
            $getParams['per_page'] = 100;
        }
        $page = 1;
        $result = $this->findLibraryMediaContentsByPage($page, $getParams, $force);
        $pages = (int)ceil($result->count / $result->per_page);

        /**
         * @var Container\ContainerArray $mediaContents
         */
        $mediaContents = $result->items;
        for ($page = 2; $page <= $pages; $page++) {
            $result = $this->findLibraryMediaContentsByPage($page, $getParams, $force);

            foreach ($result->items as $item) {
                $mediaContents->appendElement($item);
            }
        }
        return $mediaContents;
    }

    /**
     * @param string $uploadFileKey
     * @return self
     * @throws ClientException
     */
    public function deleteMediaContent($uploadFileKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON('POST', 'media/library/delete/' . $uploadFileKey . '.json', $getParams);

        return $this;
    }

    /**
     * @param string $uploadFileKey
     * @param string $categoryKey
     * @return self
     * @throws ClientException
     */
    public function changeCategoryMediaContent($uploadFileKey, $categoryKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = ['category_key' => $categoryKey];
        $this->getResponseJSON('POST', 'media/library/set_enable/' . $uploadFileKey . '.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param string $uploadFileKey
     * @return self
     * @throws ClientException
     */
    public function setEnableMediaContent($uploadFileKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON('POST', 'media/library/set_enable/' . $uploadFileKey . '.json', $getParams);

        return $this;
    }

    /**
     * @param string $uploadFileKey
     * @return self
     * @throws ClientException
     */
    public function setDisableMediaContent($uploadFileKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON('POST', 'media/library/set_disable/' . $uploadFileKey . '.json', $getParams);

        return $this;
    }

    /**
     * @param string $uploadFileKey
     * @param string $filePath
     * @return self
     * @throws ClientException
     */
    public function uploadPoster($uploadFileKey, $filePath)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $optParams = ['multipart' => [
            [
                'name' => 'file',
                'contents' => fopen($filePath, 'r'),
            ],
        ]];
        $this->getResponseJSON(
            'POST',
            'media/library/upload_poster/' . $uploadFileKey . '.json',
            $getParams,
            [],
            $optParams
        );

        return $this;
    }

    /**
     * @param string $uploadFileKey
     * @param string $profileKey
     * @return self
     * @throws ClientException
     */
    public function addAdditionalTranscodingFile($uploadFileKey, $profileKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = ['profile_key' => $profileKey];
        $this->getResponseJSON(
            'POST',
            'media/library/create_transcoding_file/'.$uploadFileKey.'.json',
            $getParams,
            $postParams
        );

        return $this;
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     * @throws ClientException
     */
    public function getChannels(array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/channel', $getParams);

        if (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $channels = new Container\ContainerArray();
        foreach ($items as $item) {
            $channels->appendElement(new Container\Channel($item));
        }

        return $channels;
    }

    /**
     * @param string $channelName
     * @param bool|false $isEncrypted
     * @param bool|true $isShared
     * @param int $mediaPlayerPolicy
     * @param bool|false $usePingBack
     * @param string|null $pingbackURL
     * @param string|null $progressPlugin
     * @return self
     * @throws ClientException
     */
    public function createChannel(
        $channelName,
        $isEncrypted = false,
        $isShared = true,
        $mediaPlayerPolicy = 0,
        $usePingBack = false,
        $pingbackURL = null,
        $progressPlugin = null
    ) {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = [
            'name' => $channelName,
            'is_encrypted' => $isEncrypted,
            'isShared' => $isShared,
            'media_player_policy' => $mediaPlayerPolicy,
            'use_pingback' => $usePingBack,
            'pingback_url' => $pingbackURL,
            'progress_plugin' => $progressPlugin,
        ];
        $this->getResponseJSON('POST', 'media/channel/create.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param string $channelKey
     * @return self
     * @throws ClientException
     */
    public function deleteChannel($channelKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $this->getResponseJSON('POST', 'media/channel/delete/'.$channelKey.'.json', $getParams);

        return $this;
    }

    /**
     * @param string $channelKey
     * @param string $uploadFileKey
     * @return self
     * @throws ClientException
     */
    public function attachChannel($channelKey, $uploadFileKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = ['channel_key' => $channelKey];
        $this->getResponseJSON('POST', 'media/channel/attach/'.$uploadFileKey.'.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param string $channelKey
     * @param string $uploadFileKey
     * @return self
     * @throws ClientException
     */
    public function detachChannel($channelKey, $uploadFileKey)
    {
        $getParams = ['access_token' => $this->serviceAccount->getApiAccessToken()];
        $postParams = ['channel_key' => $channelKey];
        $this->getResponseJSON('POST', 'media/channel/detach/'.$uploadFileKey.'.json', $getParams, $postParams);

        return $this;
    }

    /**
     * @param string $channelKey
     * @param string $uploadFileKey
     * @param bool $force
     * @return Container\MediaContent|null
     * @throws ClientException
     */
    public function getChannelMediaContent($channelKey, $uploadFileKey, $force = false)
    {
        $getParams = [
            'access_token' => $this->serviceAccount->getApiAccessToken(),
            'channel_key' => $channelKey,
            'force' => (int)$force,
        ];
        $response = $this->getResponseJSON(
            'GET',
            'media/channel/media_content/' . $uploadFileKey . '.json',
            $getParams
        );

        if (!isset($response->result->item)) {
            throw new ClientException('Response is invalid.');
        }

        return new Container\MediaContent($response->result->item);
    }

    /**
     * @param string $channelKey
     * @param int $page
     * @param array $getParams
     * @param bool|false $force
     * @return object
     * @throws ClientException
     */
    public function findChannelMediaContentsByPage($channelKey, $page = 1, array $getParams = [], $force = false)
    {
        $getParams['channel_key'] = $channelKey;
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['page'] = $page;
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/channel/media_content.json', $getParams);

        if (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $mediaContents = new Container\ContainerArray();
        foreach ($items as $item) {
            $mediaContents->appendElement(new Container\MediaContent($item));
        }

        return (object)[
            'per_page' => $response->result->per_page,
            'count' => $response->result->count,
            'items' => $mediaContents,
        ];
    }

    /**
     * @param string $channelKey
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     */
    public function getChannelMediaContents($channelKey, array $getParams = [], $force = false)
    {
        if (!isset($getParams['per_page'])) {
            $getParams['per_page'] = 100;
        }
        $page = 1;
        $result = $this->findChannelMediaContentsByPage($channelKey, $page, $getParams, $force);
        $pages = (int)ceil($result->count / $result->per_page);

        /**
         * @var Container\ContainerArray $mediaContents
         */
        $mediaContents = $result->items;
        for ($page = 2; $page <= $pages; $page++) {
            $result = $this->findChannelMediaContentsByPage($channelKey, $page, $getParams, $force);

            foreach ($result->items as $item) {
                $mediaContents->appendElement($item);
            }
        }
        return $mediaContents;
    }

    /**
     * @param int $page
     * @param array $getParams
     * @param bool|false $force
     * @return object
     * @throws ClientException
     */
    public function findUploadFilesByPage($page = 1, array $getParams = [], $force = false)
    {
        $getParams['access_token'] = $this->serviceAccount->getApiAccessToken();
        $getParams['page'] = $page;
        $getParams['force'] = (int)$force;
        $response = $this->getResponseJSON('GET', 'media/upload_file', $getParams);

        if (isset($response->result->count) && isset($response->result->items)) {
            if (is_array($response->result->items)) {
                $items = $response->result->items;
            } elseif (isset($response->result->items->item) && is_array($response->result->items->item)) {
                $items = $response->result->items->item;
            } else {
                throw new ClientException('Response is invalid.');
            }
        } else {
            throw new ClientException('Response is invalid.');
        }

        $uploadFiles = new Container\ContainerArray();
        foreach ($items as $item) {
            $uploadFiles->appendElement(new Container\UploadFile($item));
        }

        return (object)[
            'per_page' => $response->result->per_page,
            'count' => $response->result->count,
            'items' => $uploadFiles,
        ];
    }

    /**
     * @param array $getParams
     * @param bool|false $force
     * @return Container\ContainerArray
     */
    public function getUploadFiles(array $getParams = [], $force = false)
    {
        if (!isset($getParams['per_page'])) {
            $getParams['per_page'] = 100;
        }
        $page = 1;
        $result = $this->findUploadFilesByPage($page, $getParams, $force);
        $pages = (int)ceil($result->count / $result->per_page);

        /**
         * @var Container\ContainerArray $uploadFiles
         */
        $uploadFiles = $result->items;
        for ($page = 2; $page <= $pages; $page++) {
            $result = $this->findUploadFilesByPage($page, $getParams, $force);

            foreach ($result->items as $item) {
                $uploadFiles->appendElement($item);
            }
        }
        return $uploadFiles;
    }

    /**
     * @param string|null $categoryKey
     * @param bool|false $useEncryption
     * @param bool|false $isAudioUpload
     * @param string $title
     * @param int $expireTime
     * @return object
     * @throws ClientException
     */
    public function getUploadURLResponse(
        $categoryKey = null,
        $useEncryption = false,
        $isAudioUpload = false,
        $title = '',
        $expireTime = 600
    ) {
        $postParams = [
            'access_token' => $this->serviceAccount->getApiAccessToken(),
            'category_key' => $categoryKey,
            'expire_time' => $expireTime,
            'is_encryption_upload' => (bool)$useEncryption,
            'is_audio_upload' => (bool)$isAudioUpload,
            'title' => (empty($title) ? null : $title)
        ];
        $response = $this->getResponseJSON('POST', 'media_auth/upload/create_url.json', [], $postParams);

        if (!isset($response->result)) {
            throw new ClientException('Response is invalid.');
        }

        return (object)$response->result;
    }

    /**
     * @param string $filePath
     * @param string|null $categoryKey
     * @param bool $useEncryption
     * @param bool $isAudioUpload
     * @param string $title
     * @param int $expireTime
     * @param HttpClient $httpClient
     * @return $this
     * @throws \Exception|ClientException
     */
    public function uploadFileByUploadURL(
        $filePath,
        $categoryKey = null,
        $useEncryption = false,
        $isAudioUpload = false,
        $title = '',
        $expireTime = 600,
        $httpClient = null
    ) {
        $optParams = [
            'multipart' => [
                [
                    'name' => 'upload-file',
                    'contents' => fopen($filePath, 'r'),
                ]
            ],
            'disable_alert' => 0,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
        $retryCount = 10;
        $retry = 0;

        do {
            if (is_null($httpClient)) {
                $httpClient = new HttpClient([
                    'defaults' => ['allow_redirects' => false],
                    'verify' => false,
                ]);
            }

            try {
                $apiResponse = $this->getUploadURLResponse(
                    $categoryKey,
                    $useEncryption,
                    $isAudioUpload,
                    $title,
                    $expireTime
                );

                sleep(3); // need delay time.

                $response = $httpClient->request('POST', $apiResponse->upload_url, $optParams);
                $content = $response->getBody()->getContents();

                $jsonResponse = @json_decode($content);

                if (empty($jsonResponse)) {
                    throw new ClientException('JSON Response is empty');
                } elseif (isset($jsonResponse->error) && $jsonResponse->error) {
                    throw new ClientException($jsonResponse->message);
                }
            } catch (\Exception $e) {
                $retry++;
                if ($retry < $retryCount) {
                    continue;
                } else {
                    throw $e;
                }
            }

            unset($httpClient);

            break;
        } while ($retry < $retryCount);

        return $this;
    }

    /**
     * @param string $clientUserId
     * @return string
     * @throws ClientException
     */
    public function getAudioWaterMarkingCode($clientUserId)
    {
        $postParams = [
            'content_provider_key' => $this->serviceAccount->getKey(),
            'clinet_user_id' => $clientUserId,
        ];
        $response = $this->getResponseJSON(
            'POST',
            'media_auth/audio_watermarking_code/get_code.json',
            [],
            $postParams
        );

        if (!isset($response->result->code)) {
            throw new ClientException('Response is invalid.');
        }

        return $response->result->code;
    }

    /**
     * will be depricated
     *
     * @param string $mediaContentKey
     * @param string|null $clientUserId
     * @param array $optParams
     * @return string
     * @throws ClientException
     */
    public function getMediaToken($mediaContentKey, $clientUserId = null, array $optParams = [])
    {
        if (!isset($optParams['security_key'])) {
            $optParams['security_key'] = $this->serviceAccount->getKey();
        }

        if (!isset($optParams['media_content_key'])) {
            $optParams['media_content_key'] = $mediaContentKey;
        }

        if (!empty($clientUserId)) {
            $optParams['client_user_id'] = $clientUserId;
        }

        if (!isset($optParams['expire_time'])) {
            $optParams['expire_time'] = 7200;
        }

        $response = $this->getResponseJSON(
            'POST',
            'media_auth/media_token/get_media_link_by_userid.json',
            [],
            $optParams
        );

        if (!isset($response->result->media_token)) {
            throw new ClientException('Response is invalid.');
        }

        return $response->result->media_token;
    }

    /**
     * @param string $source
     * @return string
     * @throws ClientException
     */
    public function getKollusEncrypt($source)
    {
        $postParams = ['source_string' => $source];
        $response = $this->getResponseJSON(
            'POST',
            'media_auth/media_token/get_kollus_encrypt.json',
            [],
            $postParams
        );

        if (!isset($response->result->encrypt_string)) {
            throw new ClientException('Response is invalid.');
        }

        return $response->result->encrypt_string;
    }

    /**
     * @param string $keyword
     * @return null|Container\KollusPlayer
     * @throws ClientException
     */
    public function getVersionsForKollus($keyword)
    {
        $response = $this->getResponseJSON(
            'GET',
            'account/version_for_kollus.json'
        );

        $kollusPlayers = [];

        if (isset($response->result->kollus_player_pc_windows)) {
            $fileDomain = null;
            $version = null;
            if (isset($response->result->kollus_player_pc_windows->agent)) {
                $kollusPlayers['player_pc_windows_agent'] = new Container\KollusPlayer(
                    $response->result->kollus_player_pc_windows->agent
                );
                $fileDomain = parse_url($kollusPlayers['player_pc_windows_agent']->getFileUrl(), PHP_URL_HOST);
            }

            if (isset($response->result->kollus_player_pc_windows->activex)) {
                $kollusPlayers['player_pc_windows_activex'] = new Container\KollusPlayer(
                    $response->result->kollus_player_pc_windows->activex
                );
                $version = str_replace(',', '.', $kollusPlayers['player_pc_windows_activex']->getVersion());
                if (!empty($fileDomain)) {
                    $kollusPlayers['player_pc_windows_activex']->setFileUrl(
                        'http://' . $fileDomain . '/public/kollus2/KollusPlayer-' .  $version . '.cap'
                    );
                }
            }
            if (isset($response->result->kollus_player_pc_windows->np)) {
                $kollusPlayers['player_pc_windows_np'] = new Container\KollusPlayer(
                    $response->result->kollus_player_pc_windows->np
                );
                if (!empty($fileDomain) && !empty($version)) {
                    $kollusPlayers['player_pc_windows_np']->setFileUrl(
                        'http://' . $fileDomain . '/public/kollus2/KollusPlayer-' .  $version . '.exe'
                    );
                }
            }
        }

        if (isset($response->result->kollus_player_pc_mac)) {
            $kollusPlayers['player_pc_mac'] = new Container\KollusPlayer(
                $response->result->kollus_player_pc_mac
            );
        }

        if (isset($response->result->kollus_player_mobile_android)) {
            $kollusPlayers['player_mobile_android'] = new Container\KollusPlayer(
                $response->result->kollus_player_mobile_android
            );
            $kollusPlayers['player_mobile_android']->setFileUrl(
                'market://details?id=com.kollus.media'
            );
        }

        if (isset($response->result->kollus_player_mobile_ios)) {
            $kollusPlayers['player_mobile_ios'] = new Container\KollusPlayer(
                ['version' => $response->result->kollus_player_mobile_ios]
            );
            $kollusPlayers['player_mobile_ios']->setFileUrl(
                'https://itunes.apple.com/app/id760006888'
            );
        }

        if (isset($response->result->kollus_uploader)) {
            $kollusPlayers['uploader'] = new Container\KollusPlayer(
                $response->result->kollus_uploader
            );
        }

        return isset($kollusPlayers[$keyword]) ? $kollusPlayers[$keyword] : null;
    }
}

<?php

namespace Kollus\Component\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Kollus\Component\Container;
use Firebase\JWT\JWT;

/**
 * Class VideoGatewayClient
 * @package Kollus\Component\Client
 */
class VideoGatewayClient extends AbstractClient
{
    /**
     * @param mixed|null $client
     * @return self
     * @throws ClientException
     */
    public function connect($client = null)
    {
        if (get_class($this->serviceAccount) != 'Kollus\\Component\\Container\\ServiceAccount') {
            throw new ClientException('Service account is required.');
        }

        $serviceAccountKey = $this->serviceAccount->getKey();
        if (empty($serviceAccountKey)) {
            throw new ClientException('Service account key is empty.');
        }

        if (is_null($client)) {
            $this->client = new HttpClient([
                'base_uri' => $this->schema . '://v.' . $this->domain . '/',
                'defaults' => [ 'allow_redirects' => false ],
                'verify' => false,
                'timeout' => 10
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
     * @param string|Container\MediaItem[] $mediaContentKey
     * @param string|null $clientUserId
     * @param array $optParams
     * @return string
     * @throws ClientException
     */
    public function getWebToken($mediaContentKey, $clientUserId = null, array $optParams = [])
    {
        $securityKey = isset($optParams['security_key']) ?
            $optParams['security_key'] : $this->serviceAccount->getSecurityKey();
        $mediaProfileKey = isset($optParams['media_profile_key']) ? $optParams['media_profile_key'] : null;
        $awtCode = isset($optParams['awt_code']) ? $optParams['awt_code'] : null;
        $expireTime = isset($optParams['expire_time']) ? (int)$optParams['expire_time'] : 7200;
        $isIntro = isset($optParams['is_intro']) ? $optParams['is_intro'] : null;
        $isSeekable = isset($optParams['is_seekable']) ? $optParams['is_seekable'] : null;

        $payload = (object)[];
        if (is_array($mediaContentKey)) {
            $payload->mc = [];

            foreach ($mediaContentKey as $mediaItem) {
                if (get_class($mediaItem) === 'Kollus\Component\Container\MediaItem') {
                    $mcClaim = (object) [];

                    if (empty($mediaItem->getMediaContentKey())) {
                        throw new ClientException('MediaItem is invalid');
                    } else {
                        $mcClaim->mckey = $mediaItem->getMediaContentKey();
                    }

                    if (!is_null($mediaProfileKey)) {
                        $mcClaim->mcpf = $mediaProfileKey;
                    } else {
                        if (!empty($mediaItem->getProfileKey())) {
                            $mcClaim->mcpf = $mediaItem->getProfileKey();
                        }
                    }

                    if (!empty($mediaItem->getIsIntro())) {
                        $mcClaim->intr = $mediaItem->getIsIntro();
                    }

                    if (!empty($mediaItem->getIsSeekable())) {
                        $mcClaim->seek = $mediaItem->getIsSeekable();
                    }

                    $payload->mc[] = $mcClaim;
                }
            }
        } else {
            $mcClaim = (object)['mckey' => $mediaContentKey];
            if (!is_null($mediaProfileKey)) {
                $mcClaim->mcpf = $mediaProfileKey;
            }

            if (!is_null($isIntro)) {
                $mcClaim->intr = $isIntro;
            }

            if (!is_null($isSeekable)) {
                $mcClaim->seek = $isSeekable;
            }
            $payload->mc = [$mcClaim];
        }

        if (!empty($clientUserId)) {
            $payload->cuid = $clientUserId;
        }

        if (!is_null($awtCode)) {
            $payload->awtc = $awtCode;
        }

        if (!empty($expireTime)) {
            $payload->expt = time() + $expireTime;
        }

        return JWT::encode($payload, $securityKey);
    }

    /**
     * @param string $mediaContentKey
     * @param string|null $clientUserId
     * @param array $optParams
     * @return string
     */
    public function getWebTokenURL($mediaContentKey, $clientUserId = null, array $optParams = [])
    {
        $modePath = isset($optParams['kind']) && !empty($optParams['kind']) ? $optParams['kind'] : 's';
        $getParams = [];

        if (isset($optParams['autoplay'])) {
            $getParams['autoplay'] = true;
        }

        if (isset($optParams['mute'])) {
            $getParams['mute'] = true;
        }

        $getParams['jwt'] = $this->getWebToken($mediaContentKey, $clientUserId, $optParams);
        $getParams['custom_key'] = $this->serviceAccount->getCustomKey();

        $queryString = '';
        if (count($getParams) > 0) {
            $queryString = http_build_query($getParams);
            if (!empty($queryString)) {
                $queryString = '?' . $queryString;
            }
        }

        return $this->getSchema() . '://' . $this->getVideoGateWayDomain() . '/' . $modePath . $queryString;
    }

    /**
     * @param string $mediaContentKey
     * @return string
     */
    public function getPosterURL($mediaContentKey)
    {
        $posterURL = $this->getSchema() . '://' . $this->getVideoGateWayDomain() . '/poster/' .
            $mediaContentKey .'.jpg' ;

        $response = $this->client->request('HEAD', $posterURL, ['allow_redirects' => false]);

        if ($response->getStatusCode() == 302) {
            $locations = $response->getHeader('location');

            if (isset($locations[0])) {
                $posterURL = $locations[0];
            }
        }

        // will depricated : poster url must use https.
        $posterURL = str_replace('http', 'https', $posterURL);

        return $posterURL;
    }

    /**
     * @param string $mediaContentKey
     * @param string|null $clientUserId
     * @param array $optParams
     * @throws ClientException
     * @return GuzzleResponse
     */
    public function getMediaFileHeaderResponse($mediaContentKey, $clientUserId = null, array $optParams = [])
    {
        $optParams['kind'] = 'sr';

        $tokenLink = $this->getWebTokenURL($mediaContentKey, $clientUserId, $optParams);

        $response = $this->client->request('HEAD', $tokenLink, ['allow_redirects' => false]);

        if ($response->getStatusCode() == 302) {
            $locations = $response->getHeader('location');

            if (!isset($locations[0])) {
                throw new ClientException('Locations is empty.');
            }
        } else {
            throw new ClientException('Status code is not 302.');
        }

        return $response;
    }
}

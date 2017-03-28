<?php

namespace Kollus\Component\Client;

use Kollus\Component\Container;
use Guzzlehttp\Client as HttpClient;

/**
 * Class AbstractClient
 * @package Kollus\Component\Client
 */
abstract class AbstractClient
{
    protected function __construct()
    {
    }

    /**
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * @return void
     */
    protected function __wakeup()
    {
    }

    /**
     * Singleton Instance
     *
     * @var AbstractClient[] $instance
     */
    protected static $instance;

    /**
     * @var string $defaultInstanceName
     */
    protected static $defaultInstanceName;

    /**
     * @param string|null $domain
     * @param int $version
     * @param string $languageKey
     * @param array $optParams
     * @return AbstractClient
     * @throws ClientException
     */
    public static function getInstance(
        $domain = null,
        $version = 0,
        $languageKey = 'korean',
        array $optParams = []
    ) {
        $class = get_called_class();
        if (!isset(static::$instance[$class])) {
            static::$instance[$class] = new static;
            if (empty($domain)) {
                throw new ClientException('Domain is empty.');
            }
        }

        if (empty(static::$defaultInstanceName)) {
            static::$defaultInstanceName = $class;
        }

        static::$instance[$class]->initialize($domain, $version, $languageKey, $optParams);

        return static::$instance[$class];
    }

    /**
     * @return AbstractClient
     * @throws ClientException
     */
    public static function getDefaultInstance()
    {
        $class = static::$defaultInstanceName;
        if (!isset(static::$instance[$class])) {
            throw new ClientException('Client is not exists.');
        }

        return static::$instance[$class];
    }

    /**
     * @return string
     */
    public static function getDefaultInstanceName()
    {
        return static::$defaultInstanceName;
    }

    /**
     * @param string $instanceName
     */
    public static function setDefaultInstanceName($instanceName)
    {
        static::$defaultInstanceName = $instanceName;
    }

    /**
     * @var string $domain
     */
    protected $domain;

    /**
     * @var int $version
     */
    protected $version;

    /**
     * @var string $schema
     */
    protected $schema = 'http';

    /**
     * @var string $languageKey
     */
    protected $languageKey;

    /**
     * @var array $optParams
     */
    protected $optParams = [];

    /**
     * @var Container\ServiceAccount $serviceAccount
     */
    protected $serviceAccount;

    /**
     * @var Container\LoginAccount $loginAccount
     */
    protected $loginAccount;

    /**
     * @var HttpClient $client
     */
    protected $client;

    /**
     * @param string $domain
     * @param int $version
     * @param string $languageKey
     * @param array $optParams
     */
    protected function initialize($domain, $version, $languageKey, array $optParams)
    {
        $this->domain = $domain;
        $this->version = $version;
        $this->languageKey = $languageKey;
        $this->optParams = $optParams;

        if (!isset($this->optParams['timeout'])) {
            $this->optParams['timeout'] = 5;
        }
    }

    /**
     * @param Container\ServiceAccount $serviceAccount
     */
    public function setServiceAccount(Container\ServiceAccount $serviceAccount)
    {
        $this->serviceAccount = $serviceAccount;
    }

    /**
     * @return Container\ServiceAccount
     */
    public function getServiceAccount()
    {
        return $this->serviceAccount;
    }

    /**
     * @param Container\LoginAccount $loginAccount
     */
    public function setLoginAccount(Container\LoginAccount $loginAccount)
    {
        $this->loginAccount = $loginAccount;
    }

    /**
     * @return Container\LoginAccount
     */
    public function getLoginAccount()
    {
        return $this->loginAccount;
    }

    /**
     * @return HttpClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param HttpClient $client
     */
    public function setClient(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getVideoGateWayDomain()
    {
        return 'v.' . $this->domain;
    }

    /**
     * @return array
     */
    public function getOptParams()
    {
        return $this->optParams;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param mixed|null $client
     * @return self
     */
    abstract public function connect($client = null);

    /**
     * @return self
     */
    abstract public function disconnect();
}

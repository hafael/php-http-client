<?php

namespace Hafael\HttpClient;

use Hafael\HttpClient\Api\Account;
use Hafael\HttpClient\Api\User;
use Hafael\HttpClient\Handler\Curl;
use Hafael\HttpClient\Handler\Http;
use Hafael\HttpClient\Contracts\RouteInterface;
use Hafael\HttpClient\Contracts\ClientInterface;
use Hafael\HttpClient\Exceptions\ClientException;

/**
 * @method Account account()
 */
class Client implements ClientInterface
{
    /**
     * @var string
     */
    const SANDBOX_ENDPOINT = 'https://staging-endpoint.com';

    /**
     * @var string
     */
    const PRODUCTION_ENDPOINT = 'https://production-endpoint.com';

    /**
     * @var array
     */
    const API_RESOURCES = [
        'account' => Account::class,
    ];

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $debugMode = false;
    
    /**
     * The client constructor
     * 
     * @param string $apiKey
     * @param string $baseUrl
     */
    public function __construct(string $apiKey, string $baseUrl = self::SANDBOX_ENDPOINT)
    {
        $this->setApiKey($apiKey);
        $this->setBaseUrl($baseUrl);
    }

    /**
     * @return Client
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return Client
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * @param bool $mode 
     * @return Client
     */
    public function setDebugMode($mode = true)
    {
        $this->debugMode = $mode;
        return $this;
    }

    /**
     * GET REQUEST
     * 
     * @method GET
     * @param RouteInterface $route
     * @param array $params
     * @param array $headers
     * @return string
     */
    public function get(RouteInterface $route, $params = [], $headers = [])
    {
        return $this->buildRequest($route, Http::GET, $params, $headers)
                    ->send();
    }

    /**
     * POST REQUEST
     * 
     * @method POST
     * @param RouteInterface $route
     * @param array $data
     * @param array $headers
     * @return string
     */
    public function post(RouteInterface $route, $data, $headers = [])
    {
        return $this->buildRequest($route, Http::POST, [], $data, $headers)
                    ->send();
    }

    /**
     * PUT REQUEST
     * 
     * @method PUT
     * @param RouteInterface $route
     * @param array $data
     * @param array $headers
     * @return string
     */
    public function put(RouteInterface $route, $data, $headers = [])
    {
        return $this->buildRequest($route, Http::PUT, [], $data, $headers)
                    ->send();
    }

    /**
     * PATCH REQUEST
     * 
     * @method PATCH
     * @param RouteInterface $route
     * @param array $data
     * @param array $headers
     * @return string
     */
    public function patch(RouteInterface $route, $data, $headers = [])
    {
        return $this->buildRequest($route, Http::PATCH, [], $data, $headers)
                    ->send();
    }

    /**
     * DELETE REQUEST
     * 
     * @method DELETE
     * @param RouteInterface $route
     * @param array $headers
     * @return string
     */
    public function delete(RouteInterface $route, $data = [], $headers = [])
    {
        return $this->buildRequest($route, Http::DELETE, [], $data, $headers)
                    ->send();
    }

    /**
     * Preparing request
     * 
     * @param Curl $resource
     * @param array $params
     * @param array $data
     * @param array $headers
     * @return Curl
     */
    public function preRequestScript(Curl $resource, $params = [], $data = [], $headers = [])
    {
        //$resource->addHeader('Cache-control: no-cache');
        //$resource->addHeader('Content-type: application/json');
        //$resource->setBasicAuthorization($this->getApiKey(), $this->getApiSecret());

        return $resource;
    }

    /**
     * Preparing request
     * 
     * @param RouteInterface $route
     * @param $method
     * @param array $params
     * @param array $data
     * @param array $headers
     * @return Curl
     */
    public function buildRequest(RouteInterface $route, $method, $params = [], $data = [], $headers = [])
    {
        $resource = new Curl();
        $resource->setMethod($method);
        $query = $this->query($params);

        $url = sprintf('%s%s%s', $this->baseUrl, $route->build(), $query);
        $resource->setUrl($url);

        $this->preRequestScript($resource, $params, $data, $headers);

        if(!empty($data)) {
            $resource->setBody($data);
        }

        if(!empty($headers))
        {
            $resource->addHeaders($headers);
        }

        $resource->setDebugMode($this->getDebugMode());

        return $resource;
    }

    /**
     * Parse query string from array
     * 
     * @param $params
     * @return string
     */
    public function query($params)
    {
        $query = '';
        if(! empty($params)) {
            $query = '?' . http_build_query($params);
        }
        return $query;
    }

    /**
     * Magic methods
     * 
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws ClientException
     */
    public function __call(string $name, array $args)
    {
        return $this->{$name};
    }

    /**
     * Magic methods
     * 
     * @param $name
     * @return mixed
     * @throws ClientException
     */
    public function __get($name)
    {
        if(!array_key_exists(strtolower($name), static::API_RESOURCES)) {
            throw new ClientException(sprintf('API Resource not exists: %s', $name));
        }

        $class = static::API_RESOURCES[$name];

        return new $class($this);
    }
}
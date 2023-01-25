<?php

namespace Hafael\Fitbank\Tests;

use Hafael\Fitbank\Handler\Http;
use Hafael\Fitbank\Api\Api;
use Hafael\Fitbank\Api\Account;
use Hafael\Fitbank\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = new Client();
    }

    /**
     * @test
     */
    public function constructShouldConfigureTheAttributes()
    {
        $this->assertAttributeSame('my-access-token', 'apiKey', $this->client);
        $this->assertAttributeSame('example.pixapi.com.br', 'apiSecret', $this->client);
        $this->assertAttributeSame('example', 'partnerId', $this->client);
        $this->assertAttributeSame('pixapi.com.br', 'bussinesUnitId', $this->client);
    }

    /**
     * @test
     */
    public function methodBuildRequestShouldInicializeTheCurlResource()
    {
        $route = new Route();
        $resource = $this->client->buildRequest($route, Http::GET);
        $this->assertEquals('object', gettype($resource));
    }

    /**
     * @test
     */
    public function queryTest()
    {
        $query = $this->client->query([]);
        $this->assertEquals('', $query);

        $query = $this->client->query(['query' => 'string']);
        $this->assertEquals("?query=string", $query);
    }

    /**
     * @test
     */
    public function apiInstancesTest()
    {
        $account = $this->client->account;

        $this->assertInstanceOf(Account::class, $account);
    }

    /**
     * @test
     */
    public function apiThrowClientException()
    {
        $name = 'invalidapiitem';
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage("Não foi possível instanciar a classe: $name");
        $this->client->$name;
    }
}
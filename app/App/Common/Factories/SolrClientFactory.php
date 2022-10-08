<?php

namespace App\Common\Factories;

use App\Common\Config\Config;
use Nyholm\Psr7\Factory\Psr17Factory;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Psr18Adapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

class SolrClientFactory
{
    public function factory(Config $config): Client
    {
        $configArray = [ 'endpoint' => [
            'localhost' => [
                'host' => $config->getSolrHost(),
                'port' => $config->getSolrPort(),
                'path' => $config->getSolrPath(),
                'core' => $config->getSolrCore(),
            ]
        ]];

        $httpClient = new Psr18Client(HttpClient::create([ 'timeout' => 120 ]));
        $factory = new Psr17Factory();
        $adapter = new Psr18Adapter($httpClient, $factory, $factory);
        return new Client($adapter, new EventDispatcher, $configArray);
    }
}

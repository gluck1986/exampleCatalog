<?php

namespace App\Common;

use App\Common\Config\Config;
use App\Common\Factories\MySqlPdoFactory;
use App\Common\Factories\RouterFactory;
use App\Common\Factories\SolrClientFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use PDO;
use Psr\Http\Message\ServerRequestInterface;
use Solarium\Client;

class App
{
    private Config $config;
    private Router $router;
    private ServerRequestInterface $request;
    private Client $solrClient;
    private PDO $pdo;

    public function __construct(Config $config, ServerRequestInterface $request)
    {
        $this->config = $config;
        $this->request = $request;
        $this->solrClient = (new SolrClientFactory())->factory($config);
        $this->pdo = MySqlPdoFactory::buildPdo($config);
        $this->router = RouterFactory::buildRouter($this);
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }


    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function run()
    {
        $response = $this->router->dispatch($this->request);
        (new SapiEmitter())->emit($response);
    }

    /**
     * @return Client
     */
    public function getSolrClient(): Client
    {
        return $this->solrClient;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}

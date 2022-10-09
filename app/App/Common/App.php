<?php

namespace App\Common;

use App\Common\Config\Config;
use App\Common\Factories\MySqlPdoFactory;
use App\Common\Factories\RouterFactory;
use App\Common\Factories\SolrClientFactory;
use App\Repository\Mappers\ProductSolrMapper;
use App\Repository\ProductRepository;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;
use League\Route\Router;
use PDO;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Solarium\Client;

class App
{
    private readonly Router $router;
    private readonly ContainerInterface $container;

    public function __construct(Config $config, private readonly ServerRequestInterface $request)
    {
        $cli = (new SolrClientFactory())->factory($config);
        $this->container = new ServiceManager([
            'services' => [
                Config::class => $config,
                Client::class =>  $cli,
                PDO::class =>  MySqlPdoFactory::buildPdo($config),
            ],
            'abstract_factories' => [
                ReflectionBasedAbstractFactory::class,
            ],
        ]);

        $this->router = RouterFactory::buildRouter($this);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function run(): void
    {
        $response = $this->router->dispatch($this->request);
        (new SapiEmitter())->emit($response);
    }
}

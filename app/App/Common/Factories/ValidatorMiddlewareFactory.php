<?php

namespace App\Common\Factories;

use App\Common\Config\Config;
use League\OpenAPIValidation\PSR15\ValidationMiddleware;
use League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Psr\Http\Server\MiddlewareInterface;

class ValidatorMiddlewareFactory
{

    public function __construct(private readonly string $specPath)
    {
    }

    public static function make(Config $config): self
    {
        return new self($config->getSpecPath());
    }

    public function factory(): MiddlewareInterface
    {
        $builder = new ValidationMiddlewareBuilder();
        $builder->fromYamlFile($this->specPath);

        return $builder->getValidationMiddleware();
    }
}

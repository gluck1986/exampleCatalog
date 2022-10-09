<?php


namespace App\Common\Factories;

use App\Common\Config\Config;
use Dotenv\Dotenv;

class ConfigFactory
{
    public static function make(string $basePath): Config
    {
        if (file_exists($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->load();
        }

        return new Config(
            basePath: $basePath,
            solrHost: self::getEnvStr('solr_host', 'localhost'),
            solrPort: empty(getenv('solr_port')) ? 8983 : (int)getenv('solr_port'),
            solrPath: self::getEnvStr('solr_path', '/'),
            solrCore: self::getEnvStr('solr_core'),
            myHost: self::getEnvStr('mysql_host', 'localhost'),
            myUser: self::getEnvStr('mysql_user'),
            myPort: empty(getenv('mysql_port')) ? 3306 : (int)getenv('mysql_port'),
            myPass: self::getEnvStr('mysql_pass'),
            myDbName: self::getEnvStr('mysql_db'),
            specPath: dirname(__DIR__, 3) . '/spec/spec.yaml',
        );
    }

    private static function getEnvStr(string $key, string $default = ''): string
    {
        return empty(getenv($key)) ? $default : (string)getenv($key);
    }
}

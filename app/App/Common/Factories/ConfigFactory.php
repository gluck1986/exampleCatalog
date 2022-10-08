<?php


namespace App\Common\Factories;

use App\Common\Config\Config;
use Dotenv\Dotenv;

class ConfigFactory
{
    public static function build($basePath): Config
    {
        if (file_exists($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->load();
        }

        return new Config(
            basePath: $basePath,
            solrHost: getenv('solr_host') ?? 'localhost',
            solrPort: empty(getenv('solr_port')) ? 8983 : (int)getenv('solr_port'),
            solrPath: getenv('solr_path') ?? '/',
            solrCore: getenv('solr_core') ?? '',
            myHost:getenv('mysql_host') ?? 'localhost',
            myUser:getenv('mysql_user') ?? '',
            myPort: empty(getenv('mysql_port')) ? 3306 : (int)getenv('mysql_port'),
            myPass:getenv('mysql_pass') ?? '',
            myDbName:getenv('mysql_db') ?? '',
        );
    }
}

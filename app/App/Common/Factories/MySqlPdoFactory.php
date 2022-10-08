<?php

namespace App\Common\Factories;

use App\Common\Config\Config;
use PDO;

class MySqlPdoFactory
{
    public static function buildPdo(Config $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s',
            $config->getMyHost(),
            $config->getMyPort(),
            $config->getMyDbName(),
        );
        return new PDO(
            $dsn,
            $config->getMyUser(),
            $config->getMyPass(),
        );
    }
}

<?php

namespace App\Common\Factories;

use App\Common\Config\Config;
use PDO;

class MySqlPdoFactory
{
    public static function buildPdo(Config $config): PDO
    {
        return new PDO(
            sprintf(
                'mysql:host=%s;port=%d;dbname=%s',
                $config->getMyHost(),
                $config->getMyPort(),
                $config->getMyDbName(),
            ),
            $config->getMyUser(),
            $config->getMyPass(),
        );
    }
}

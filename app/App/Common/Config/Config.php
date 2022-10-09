<?php


namespace App\Common\Config;

class Config
{
    public function __construct(
        private readonly string $basePath,
        private readonly string $solrHost,
        private readonly int $solrPort,
        private readonly string $solrPath,
        private readonly string $solrCore,
        private readonly string $myHost,
        private readonly string $myUser,
        private readonly int $myPort,
        private readonly string $myPass,
        private readonly string $myDbName,
        private readonly string $specPath,
    ) {
    }

    public function getMyDbName(): string
    {
        return $this->myDbName;
    }

    public function getMyHost(): string
    {
        return $this->myHost;
    }

    public function getMyUser(): string
    {
        return $this->myUser;
    }

    public function getMyPort(): int
    {
        return $this->myPort;
    }

    public function getMyPass(): string
    {
        return $this->myPass;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getSolrHost(): string
    {
        return $this->solrHost;
    }

    public function getSolrPort(): int
    {
        return $this->solrPort;
    }

    public function getSolrPath(): string
    {
        return $this->solrPath;
    }

    public function getSolrCore(): string
    {
        return $this->solrCore;
    }

    public function getSpecPath(): string
    {
        return $this->specPath;
    }
}

<?php


namespace App\Common\Config;

class Config
{
    public function __construct(
        private string $basePath,
        private string $solrHost,
        private int $solrPort,
        private string $solrPath,
        private string $solrCore,
        private string $myHost,
        private string $myUser,
        private string $myPort,
        private string $myPass,
        private string $myDbName,
        private string $specPath,
    ) {
    }

    /**
     * @return string
     */
    public function getMyDbName(): string
    {
        return $this->myDbName;
    }

    /**
     * @return string
     */
    public function getMyHost(): string
    {
        return $this->myHost;
    }

    /**
     * @return string
     */
    public function getMyUser(): string
    {
        return $this->myUser;
    }

    /**
     * @return string
     */
    public function getMyPort(): string
    {
        return $this->myPort;
    }

    /**
     * @return string
     */
    public function getMyPass(): string
    {
        return $this->myPass;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function getSolrHost(): string
    {
        return $this->solrHost;
    }

    /**
     * @return int
     */
    public function getSolrPort(): int
    {
        return $this->solrPort;
    }

    /**
     * @return string
     */
    public function getSolrPath(): string
    {
        return $this->solrPath;
    }

    /**
     * @return string
     */
    public function getSolrCore(): string
    {
        return $this->solrCore;
    }

    public function getSpecPath()
    {
        return $this->specPath;
    }
}

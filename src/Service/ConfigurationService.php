<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\Service;

class ConfigurationService
{
    /**
     * @var array
     */
    private $config = [];

    public function __construct()
    {
    }

    /**
     * Sets the config.
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Returns the config.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getProfile(string $name)
    {
        if (!array_key_exists('profiles', $this->config)) {
            return null;
        }
        if (!array_key_exists($name, $this->config['profiles'])) {
            return null;
        }

        return $this->config['profiles'][$name];
    }

    public function getBackend(string $name)
    {
        if (!array_key_exists('backends', $this->config)) {
            return null;
        }
        if (!array_key_exists($name, $this->config['backends'])) {
            return null;
        }

        return $this->config['backends'][$name];
    }
}

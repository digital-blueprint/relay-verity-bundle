<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

/**
 * @internal
 */
class VerityProviderInterfaceService
{
    /**
     * @var array<class-string,VerityProviderInterface>
     */
    private array $services;

    public function __construct()
    {
        $this->services = [];
    }

    public function addService(VerityProviderInterface $service): void
    {
        $this->services[$service::class] = $service;
    }

    /**
     * Gets the vertity Service of the bucket object.
     */
    public function getService($serviceClass): VerityProviderInterface
    {
        $vertityService = $this->services[$serviceClass] ?? null;
        if ($vertityService === null) {
            throw new \RuntimeException("$serviceClass not found");
        }
        assert($vertityService instanceof VerityProviderInterface);

        return $vertityService;
    }
}

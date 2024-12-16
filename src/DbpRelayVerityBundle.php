<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle;

use Dbp\Relay\VerityBundle\Service\VerityProviderInterfaceServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DbpRelayVerityBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        VerityProviderInterfaceServiceCompilerPass::register($container);
    }
}

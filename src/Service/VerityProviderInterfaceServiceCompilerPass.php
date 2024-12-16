<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Service;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class VerityProviderInterfaceServiceCompilerPass implements CompilerPassInterface
{
    private const TAG = 'dbp.relay.veritybundle.service';

    public static function register(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(VerityProviderInterface::class)->addTag(self::TAG);
        $container->addCompilerPass(new VerityProviderInterfaceServiceCompilerPass());
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(VerityProviderInterfaceService::class)) {
            return;
        }
        $definition = $container->findDefinition(VerityProviderInterfaceService::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addService', [new Reference($id)]);
        }
    }
}

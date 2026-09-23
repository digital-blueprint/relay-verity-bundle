<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests;

use Dbp\Relay\CoreBundle\TestUtils\CoreTestKernelTrait;
use Dbp\Relay\VerityBundle\DbpRelayVerityBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use CoreTestKernelTrait;

    protected function registerAdditionalBundles(): iterable
    {
        yield new DbpRelayVerityBundle();
    }

    protected function configureAdditionalContainer(ContainerConfigurator $container): void
    {
        $container->extension('dbp_relay_verity', [
            'backends' => [
                'dummy' => [
                    'validator' => 'Dbp\Relay\VerityBundle\Service\DummyAPI',
                ],
            ],
            'profiles' => [
                'unit_test' => [
                    'name' => 'Testing the Check',
                    'rule' => 'a.validity == true',
                    'checks' => [
                        'a' => [
                            'backend' => 'dummy',
                            'config' => '{}',
                        ],
                    ],
                ],
            ],
        ]);
    }
}

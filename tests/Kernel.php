<?php

declare(strict_types=1);

namespace Dbp\Relay\ValidationBundle\Tests;

use ApiPlatform\Symfony\Bundle\ApiPlatformBundle;
use Dbp\Relay\CoreBundle\DbpRelayCoreBundle;
use Dbp\Relay\ValidationBundle\DbpRelayValidationBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new TwigBundle();
        yield new NelmioCorsBundle();
        yield new MonologBundle();
        yield new ApiPlatformBundle();
        yield new DbpRelayValidationBundle();
        yield new DbpRelayCoreBundle();
    }

    protected function configureRoutes(RoutingConfigurator $routes)
    {
        $routes->import('@DbpRelayCoreBundle/Resources/config/routing.yaml');
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader)
    {
        $container->import('@DbpRelayCoreBundle/Resources/config/services_test.yaml');
        $container->extension('framework', [
            'test' => true,
            'secret' => '',
            'annotations' => false,
        ]);

        $container->extension('dbp_relay_validation', [
            'backends' => [
                'dummy' => [
                    'url' => 'https://dummy.url/',
                    'validator' => 'Dbp\Relay\ValidationBundle\Tests\Helper\DummyAPI',
                    'maxsize' => 8,
                ],
            ],
            'profiles' => [
                'unit_test' => [
                    'name' => 'Testing the Check',
                    'rule' => 'a.validity == true',
                    'checks' => [
                        'a' => [
                            'backend' => 'dummy',
                            'flavour' => 'auto',
                        ],
                    ],
                ],
            ],
        ]);
    }
}

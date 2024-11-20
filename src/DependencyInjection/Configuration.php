<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dbp_relay_verity');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('backends')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('url')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('validator')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('maxsize')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('profiles')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('rule')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('checks')
                                ->isRequired()
    //                            ->cannotBeEmpty()
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('backend')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('config')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

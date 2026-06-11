<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\DependencyInjection;

use Dbp\Relay\CoreBundle\Authorization\AuthorizationConfigDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROLE_USER = 'ROLE_USER';

    private function getAuthNode(): NodeDefinition
    {
        return AuthorizationConfigDefinition::create()
            ->addRole(self::ROLE_USER, 'false', 'Returns true if the user is allowed to use the verity API.')
            ->getNodeDefinition();
    }

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
                            ->scalarNode('validator')
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
            ->append($this->getAuthNode())
        ->end();

        return $treeBuilder;
    }
}

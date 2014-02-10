<?php

namespace Alex\BehatLauncherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root('alex_behat_launcher')
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifTrue(function($v) { return !isset($v['projects']) || !count($v['projects']); })
                ->then(function($v) {
                    $v['projects'] = array('Default' => array('path' => '.'));

                    return $v;
                })
            ->end()
            ->children()
                ->scalarNode('connection_id')->defaultValue('doctrine.dbal.default_connection')->end()
                ->arrayNode('projects')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('path')->end()
                            ->arrayNode('properties')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')->end()
                                        ->scalarNode('config')->end()
                                        ->scalarNode('env')->end()
                                        ->scalarNode('form_type')->end()
                                        ->arrayNode('form_options')
                                            ->prototype('variable')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}

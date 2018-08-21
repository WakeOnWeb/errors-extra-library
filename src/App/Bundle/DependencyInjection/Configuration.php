<?php

namespace WakeOnWeb\ErrorsExtraLibrary\App\Bundle\DependencyInjection;

use Psr\Log\LogLevel;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('wakeonweb_errors_extra_library');
        $rootNode
            ->children()
                ->scalarNode('force_format')->defaultValue(null)->end()
                ->arrayNode('exception')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('http_status_codes')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('show_messages')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('log_levels')
                            ->prototype('enum')
                                ->values($this->getPSR3LogLevels())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }

    /**
     * @return string[]
     */
    private function getPSR3LogLevels()
    {
        return (new \ReflectionClass(LogLevel::class))->getConstants();
    }
}

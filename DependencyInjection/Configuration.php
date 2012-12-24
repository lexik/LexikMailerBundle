<?php

namespace Lexik\Bundle\MailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lexik_mailer');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('admin_email')
                    ->isRequired()
                ->end()

                ->scalarNode('base_layout')
                    ->cannotBeEmpty()
                    ->defaultValue('LexikMailerBundle::layout.html.twig')
                ->end()

                ->arrayNode('templating_extensions')
                    ->defaultValue(array())
                    ->prototype('scalar')
                    ->end()
                ->end()

                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('email_entity')
                            ->cannotBeEmpty()
                            ->defaultValue('Lexik\Bundle\MailerBundle\Entity\Email')
                        ->end()
                        ->scalarNode('annotation_driver')
                            ->cannotBeEmpty()
                            ->defaultValue('Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation')
                        ->end()
                        ->scalarNode('message_factory')
                            ->cannotBeEmpty()
                            ->defaultValue('Lexik\Bundle\MailerBundle\Message\MessageFactory')
                        ->end()
                        ->scalarNode('message_renderer')
                            ->cannotBeEmpty()
                            ->defaultValue('Lexik\Bundle\MailerBundle\Message\MessageRenderer')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

<?php

namespace Lexik\Bundle\MailerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LexikMailerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('lexik_mailer.base_layout', $config['base_layout']);
        $container->setParameter('lexik_mailer.admin_email', $config['admin_email']);
        $container->setParameter('lexik_mailer.allowed_headers', $config['allowed_headers']);

        foreach ($config['classes'] as $name => $class) {
            $container->setParameter(sprintf('lexik_mailer.%s.class', $name), $class);
        }

        $templating = $container->findDefinition('lexik_mailer.templating');

        foreach ($config['templating_extensions'] as $extensionsId) {
            $templating->addMethodCall('addExtension', array(new Reference($extensionsId)));
        }

        // signer configuration
        $container->setParameter('lexik_mailer.signer', $config['signer']);

        // DKIM
        $container->setParameter('lexik_mailer.dkim.private_key_path', $config['dkim']['private_key_path']);
        $container->setParameter('lexik_mailer.dkim.domain', $config['dkim']['domain']);
        $container->setParameter('lexik_mailer.dkim.selector', $config['dkim']['selector']);
    }
}

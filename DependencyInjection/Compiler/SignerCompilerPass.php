<?php

namespace Lexik\Bundle\MailerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * SignerCompilerPass
 * 
 * @author Sébastien Dieunidou <sebastien@bedycasa.com>
 */
class SignerCompilerPass implements CompilerPassInterface
{
    /**
     * Process
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('lexik_mailer.signer_factory')) {
            $definition = $container->getDefinition('lexik_mailer.signer_factory');

            foreach ($container->findTaggedServiceIds('lexik_mailer.signer') as $id => $attributes) {
                if (!empty($attributes[0]['label'])) {
                    $definition->addMethodCall('addSigner', array($attributes[0]['label'], new Reference($id)));
                }
            }
        }
    }
}

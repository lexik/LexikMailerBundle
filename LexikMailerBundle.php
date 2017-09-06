<?php

namespace Lexik\Bundle\MailerBundle;

use Lexik\Bundle\MailerBundle\DependencyInjection\Compiler\SignerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * LexikMailerBundle.
 */
class LexikMailerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SignerCompilerPass());
    }
}

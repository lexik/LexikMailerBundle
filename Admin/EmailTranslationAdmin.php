<?php

namespace Lexik\Bundle\MailerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * EmailTranslationAdmin
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class EmailTranslationAdmin extends Admin
{
    protected $parentAssociationMapping = 'email';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('list');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'lang',
                'language',
                array(
                    'required'          => true,
                    'preferred_choices' => array('fr')
                )
            )
            ->add('fromAddress')
            ->add('fromName')
            ->add('subject')
            ->add(
                'body',
                null,
                array(
                    'attr' => array('rows' => 20, 'data-editor' => 'html')
                )
            )
            ->add(
                'bodyText',
                null,
                array(
                    'attr' => array('rows' => 20)
                )
            );
    }
}

<?php

namespace Lexik\Bundle\MailerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * LayoutTranslationAdmin
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class LayoutTranslationAdmin extends Admin
{
    protected $parentAssociationMapping = 'layout';

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
                [
                    'required'          => true,
                    'preferred_choices' => ['fr']
                ]
            )
            ->add(
                'body',
                'textarea',
                ['required' => true]
            );
    }
}

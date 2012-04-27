<?php

namespace Lexik\Bundle\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class LayoutType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('reference', null, array(
            'read_only'  => $options['edit']
        ))
                ->add('description')
                ->add('translation', new LayoutTranslationType(), array(
                    'property_path' => false,
                    'data'          => $options['data_translation'],
                    'with_language' => $options['edit'],
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'            => 'Lexik\Bundle\MailerBundle\Entity\Layout',
            'data_translation'      => null,
            'edit'                  => false,
            'preferred_languages'   => array(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mailer_layout';
    }
}
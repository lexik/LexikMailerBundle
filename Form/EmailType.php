<?php

namespace Lexik\Bundle\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('reference', null, array(
            'read_only'  => $options['edit']
        ))
                ->add('layout', 'entity', array(
                    'required'      => false,
                    'empty_value'   => '',
                    'class'         => 'Lexik\Bundle\MailerBundle\Entity\Layout',
                ))
                ->add('description')
                ->add('bcc')
                ->add('spool', null, array(
                    'required'  => false,
                ))
                ->add('translation', new EmailTranslationType(), array(
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
            'data_class'            => 'Lexik\Bundle\MailerBundle\Entity\Email',
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
        return 'mailer_email';
    }
}
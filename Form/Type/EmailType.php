<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', null, array(
                'read_only'  => $options['edit']
            ))
            ->add('layout', 'entity', array(
                'required'    => false,
                'empty_value' => '',
                'class'       => $options['layout_entity'],
            ))
            ->add('headers', 'collection', array(
                'type'         => 'lexik_mailer_header',
                'allow_add'    => true,
                'allow_delete' => true,
            ))
            ->add('description')
            ->add('bcc')
            ->add('spool', null, array(
                'required'  => false,
            ))
            ->add('translation', 'mailer_email_translation', array(
                'mapped'        => false,
                'data'          => $options['data_translation'],
                'with_language' => $options['edit'],
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Lexik\Bundle\MailerBundle\Entity\Email',
            'layout_entity'         => 'Lexik\Bundle\MailerBundle\Entity\Layout',
            'data_translation'      => null,
            'edit'                  => false,
            'preferred_languages'   => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mailer_email';
    }
}

<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'read_only'     => $options['edit'],
                'property_path' => 'entity.reference',
                'label'         => 'lexik_mailer.email.reference',
            ))
            ->add('layout', 'entity', array(
                'required'      => false,
                'empty_value'   => '',
                'class'         => $options['layout_entity'],
                'property_path' => 'entity.layout',
                'label'         => 'lexik_mailer.email.layout',
            ))
            ->add('headers', 'collection', array(
                'type'          => 'lexik_mailer_header',
                'allow_add'     => true,
                'allow_delete'  => true,
                'property_path' => 'entity.headers',
                'label'         => 'lexik_mailer.email.headers',
            ))
            ->add('description', 'textarea', array(
                'property_path' => 'entity.description',
                'required'      => false,
                'label'         => 'lexik_mailer.email.description',
            ))
            ->add('bcc', 'text', array(
                'property_path' => 'entity.bcc',
                'required'      => false,
                'label'         => 'lexik_mailer.email.bcc',
            ))
            ->add('spool', 'checkbox', array(
                'required'      => false,
                'property_path' => 'entity.spool',
                'required'      => false,
                'label'         => 'lexik_mailer.email.spool',
            ))
            ->add('translation', 'mailer_email_translation', array(
                'data'          => $options['data_translation'],
                'with_language' => $options['edit'],
                'label'         => 'lexik_mailer.email.translation',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Lexik\Bundle\MailerBundle\Form\Model\EntityTranslationModel',
            'layout_entity'         => 'Lexik\Bundle\MailerBundle\Entity\Layout',
            'data_translation'      => null,
            'edit'                  => false,
            'preferred_languages'   => array(),
            'translation_domain'  => 'LexikMailerBundle',
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

<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('layout', EntityType::class, array(
                'required'      => false,
                'empty_value'   => '',
                'class'         => $options['layout_entity'],
                'property_path' => 'entity.layout',
                'label'         => 'lexik_mailer.email.layout',
            ))
            ->add('headers', CollectionType::class, array(
                'type'          => HeaderType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'property_path' => 'entity.headers',
                'label'         => 'lexik_mailer.email.headers',
            ))
            ->add('description', TextareaType::class, array(
                'property_path' => 'entity.description',
                'required'      => false,
                'label'         => 'lexik_mailer.email.description',
            ))
            ->add('bcc', TextType::class, array(
                'property_path' => 'entity.bcc',
                'required'      => false,
                'label'         => 'lexik_mailer.email.bcc',
            ))
            ->add('spool', CheckboxType::class, array(
                'property_path' => 'entity.spool',
                'label'         => 'lexik_mailer.email.spool',
            ))
                'required'      => false,
                'property_path' => 'entity.useFallbackLocale',
                'label'         => 'lexik_mailer.email.use_fallback_locale'
            ))
            ->add('translation', 'mailer_email_translation', array(
            ->add('useFallbackLocale', 'checkbox', array(
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
    public function getBlockPrefix()
    {
        return 'mailer_email';
    }
}

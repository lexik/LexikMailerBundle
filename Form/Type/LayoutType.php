<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class LayoutType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [
                'attr' => [
                    'read_only' => $options['edit'],
                ],
                'property_path' => 'entity.reference',
                'label' => 'lexik_mailer.layout.reference',
            ])
            ->add('description', TextareaType::class, [
                'property_path' => 'entity.description',
                'required' => false,
                'label' => 'lexik_mailer.layout.description',
            ])
            ->add('defaultLocale', TextType::class, [
                'property_path' => 'entity.defaultLocale',
                'required' => false,
                'label' => 'lexik_mailer.layout.default_locale',
            ])
            ->add('translation', LayoutTranslationType::class, [
                'data' => $options['data_translation'],
                'with_language' => $options['edit'],
                'label' => 'lexik_mailer.layout.translation',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Lexik\Bundle\MailerBundle\Form\Model\EntityTranslationModel',
            'data_translation' => null,
            'edit' => false,
            'preferred_languages' => [],
            'translation_domain' => 'LexikMailerBundle',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mailer_layout';
    }
}

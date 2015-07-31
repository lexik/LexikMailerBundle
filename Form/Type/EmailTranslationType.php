<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EmailTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', 'text', array(
                'label' => 'lexik_mailer.translations.subject',
            ))
            ->add('body', null, array(
                'attr'  => array('rows' => 20),
                'label' => 'lexik_mailer.translations.body',
            ))
            ->add('bodyText', null, array(
                'attr'  => array('rows' => 20),
                'label' => 'lexik_mailer.translations.body_text',
            ))
            ->add('fromAddress', 'text', array(
                'label' => 'lexik_mailer.translations.from_address',
            ))
            ->add('fromName', 'text', array(
                'label' => 'lexik_mailer.translations.from_name',
            ))
        ;

        if ($options['with_language']) {
            $builder->add('lang', 'language', array(
                'preferred_choices' => $options['preferred_languages'],
                'label'             => 'lexik_mailer.translations.language',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'          => 'Lexik\Bundle\MailerBundle\Entity\EmailTranslation',
            'with_language'       => true,
            'preferred_languages' => array('en', 'fr', 'es', 'de', 'it', 'pt', 'ja', 'zh'),
            'translation_domain'  => 'LexikMailerBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mailer_email_translation';
    }
}

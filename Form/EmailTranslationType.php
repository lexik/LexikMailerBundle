<?php

namespace Lexik\Bundle\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $builder->add('subject')
                ->add('body', null, array(
                    'attr' => array('rows' => 30)
                ))
                ->add('fromAddress')
                ->add('fromName');

        if ($options['with_language']) {
            $builder->add('lang', 'language', array(
                'preferred_choices' => array('en', 'fr', 'es', 'de', 'it', 'pt', 'ja', 'zh')
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'data_class'    => 'Lexik\Bundle\MailerBundle\Entity\EmailTranslation',
            'with_language' => true,
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
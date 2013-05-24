<?php

namespace Lexik\Bundle\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class LayoutTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('body', null, array(
            'attr' => array('rows' => 20)
        ));

        if ($options['with_language']) {
            $builder->add('lang', 'language', array(
                'preferred_choices' => $options['preferred_languages'],
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
            'data_class'          => 'Lexik\Bundle\MailerBundle\Entity\LayoutTranslation',
            'with_language'       => true,
            'preferred_languages' => array('en', 'fr', 'es', 'de', 'it', 'pt', 'ja', 'zh'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mailer_layout_translation';
    }
}

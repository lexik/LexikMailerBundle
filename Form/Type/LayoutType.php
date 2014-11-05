<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('reference', null, array(
                'read_only'  => $options['edit']
            ))
            ->add('description')
            ->add('translation', 'mailer_layout_translation', array(
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
            'data_class'          => 'Lexik\Bundle\MailerBundle\Entity\Layout',
            'data_translation'    => null,
            'edit'                => false,
            'preferred_languages' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mailer_layout';
    }
}

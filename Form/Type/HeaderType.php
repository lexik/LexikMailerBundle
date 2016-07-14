<?php

namespace Lexik\Bundle\MailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * HeaderType
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class HeaderType extends AbstractType
{
    /**
     * @var array
     */
    protected $allowedHeaders;

    /**
     * @param array $allowedHeaders
     */
    public function __construct(array $allowedHeaders)
    {
        $this->allowedHeaders = $allowedHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', ChoiceType::class, array(
                'required' => true,
                'choices'  => $options['key_choices'],
                'multiple' => false,
                'expanded' => false,
            ))
            ->add('value', TextType::class, array(
                'required'    => true,
                'constraints' => array(
                    new NotBlank(),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'key_choices'         => count($this->allowedHeaders) ? array_combine($this->allowedHeaders, $this->allowedHeaders) : array(),
            'translation_domain'  => 'LexikMailerBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lexik_mailer_header';
    }
}

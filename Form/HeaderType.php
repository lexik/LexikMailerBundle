<?php

namespace Lexik\Bundle\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('key', 'choice', array(
                'required' => true,
                'choices'  => array_combine(
                    $this->allowedHeaders,
                    $this->allowedHeaders
                ),
                'multiple' => false,
                'expanded' => false,
            ))
            ->add('value', 'text', array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lexik_mailer_header';
    }
}

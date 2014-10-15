<?php

namespace Lexik\Bundle\MailerBundle\Admin;

use Lexik\Bundle\MailerBundle\Entity\Email;
use Lexik\Bundle\MailerBundle\Entity\EmailTranslation;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * EmailAdmin
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class EmailAdmin extends Admin
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $email = new Email();
        $email->addTranslation(new EmailTranslation($this->locale));

        return $email;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('reference')
            ->add('layout')
            ->add('description');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('reference', 'text', ['required' => true])
            ->add('layout', null, [
                'required' => true,
                'attr'     => [
                    'data-sonata-select2' => 'false',
                    'class'               => 'form-control'
                ]
            ])
            ->add('description', 'textarea', ['required' => false])
            ->add('bcc')
            ->add('spool', null, [
                'required'  => false,
            ])
            ->end()
            ->with('Headers')
                ->add($formMapper->create('headers', 'sonata_type_native_collection', [
                    'type'         => 'lexik_mailer_header',
                    'allow_add'    => true,
                    'allow_delete' => true,
                ]))
            ->end()
            ->with('Translations')
            ->add(
                'translations',
                'sonata_type_collection',
                [
                    'cascade_validation' => true,
                    'by_reference'       => false,
                ],
                [
                    'edit'       => 'inline',
                    'admin_code' => 'lexik_mailer.admin.email_translation'
                ]
            )
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('reference')
            ->add('layout')
            ->add('description')
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show'   => [],
                        'edit'   => [],
                        'delete' => [],
                    ]
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('reference')
            ->add('layout')
        ;
    }
}

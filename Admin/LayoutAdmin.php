<?php

namespace Lexik\Bundle\MailerBundle\Admin;

use Lexik\Bundle\MailerBundle\Entity\Layout;
use Lexik\Bundle\MailerBundle\Entity\LayoutTranslation;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * LayoutAdmin
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class LayoutAdmin extends Admin
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
        $layout = new Layout();
        $layout->addTranslation(new LayoutTranslation($this->locale));

        return $layout;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('reference')
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
            ->add('description', 'textarea', ['required' => false])
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
                    'admin_code' => 'lexik_mailer.admin.layout_translation'
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
            ->add('reference');
    }
}

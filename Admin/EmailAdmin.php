<?php

namespace Lexik\Bundle\MailerBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Lexik\Bundle\MailerBundle\Entity\Email;
use Lexik\Bundle\MailerBundle\Entity\EmailTranslation;
use Lexik\Bundle\MailerBundle\Form\Type\HeaderType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
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
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
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
            ->add('reference', 'text', array('required' => true))
            ->add(
                'layout',
                null,
                array(
                    'required' => true,
                    'attr'     => array(
                        'data-sonata-select2' => 'false',
                        'class'               => 'form-control'
                    )
                )
            )
            ->add('description', 'textarea', array('required' => false))
            ->add('bcc')
            ->add(
                'spool',
                null,
                array(
                    'required' => false,
                )
            )
            ->end()
            ->with('Headers')
            ->add(
                $formMapper->create(
                    'headers',
                    'sonata_type_native_collection',
                    array(
                        'entry_type'   => HeaderType::class,
                        'allow_add'    => true,
                        'allow_delete' => true,
                    )
                )
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
                array(
                    'actions' => array(
                        'edit'   => array(),
                        'delete' => array(),
                    )
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('reference')
            ->add('layout');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $id = $this->getRequest()->get('id');

        /** @var Email $object */
        $object = $this->getObject($id);

        $createMenuItem = $menu->addChild(
            $this->trans('create_translation'),
            array(
                'route'           => 'admin_lexik_mailer_email_emailtranslation_create',
                'routeParameters' => array(
                    'id' => $id
                )
            )
        );

        $createMenuItem->setLinkAttribute('class', 'lexik-mailer-create');

        /** @var EmailTranslation $translation */
        foreach ($object->getTranslations() as $translation) {
            $menu->addChild(
                $translation->getLang(),
                array(
                    'route'           => 'admin_lexik_mailer_email_emailtranslation_edit',
                    'routeParameters' => array('id' => $id, 'childId' => $translation->getId()),
                    'routeAbsolute'   => false
                )
            );
        }
    }
}

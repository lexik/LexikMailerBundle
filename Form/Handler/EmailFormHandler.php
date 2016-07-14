<?php

namespace Lexik\Bundle\MailerBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\MailerBundle\Entity\Email;
use Lexik\Bundle\MailerBundle\Entity\EmailTranslation;
use Lexik\Bundle\MailerBundle\Form\Model\EntityTranslationModel;
use Lexik\Bundle\MailerBundle\Form\Type\EmailType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class EmailFormHandler implements FormHandlerInterface
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $factory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param FormFactoryInterface $factory
     * @param EntityManager        $em
     * @param string               $defaultLocale
     */
    public function __construct(FormFactoryInterface $factory, EntityManager $em, $defaultLocale)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm($email = null, $lang = null)
    {
        $edit = ($email !== null);
        $this->locale = $lang ? : $this->defaultLocale;

        if ($edit) {
            $translation = $email->getTranslation($this->locale);
        } else {
            $email = new Email();
            $translation = new EmailTranslation($this->defaultLocale);
            $translation->setEmail($email);
        }

        $model = new EntityTranslationModel($email, $translation);

        return $this->factory->create(EmailType::class, $model, array(
            'data_translation' => $translation,
            'edit'             => $edit,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function processForm(FormInterface $form, Request $request)
    {
        $valid = false;
        $form->handleRequest($request);

        if ($form->isValid()) {
            $model = $form->getData();
            $model->getEntity()->addTranslation($model->getTranslation());

            $this->em->persist($model->getEntity());
            $this->em->flush();

            $valid = true;
        }

        return $valid;
    }
}

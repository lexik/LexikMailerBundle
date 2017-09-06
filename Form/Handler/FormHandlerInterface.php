<?php

namespace Lexik\Bundle\MailerBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface FormHandlerInterface
{
    /**
     * Returns the current translation locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Create a new form instance.
     *
     * @param mixed  $object
     * @param string $lang
     *
     * @return FormInterface
     */
    public function createForm($object = null, $lang = null);

    /**
     * Submit and validate the form.
     *
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return bool
     */
    public function processForm(FormInterface $form, Request $request);
}

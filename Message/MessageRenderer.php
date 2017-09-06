<?php

namespace Lexik\Bundle\MailerBundle\Message;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;
use Lexik\Bundle\MailerBundle\Twig\Loader\EmailLoader;

/**
 * Render each parts of an email.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class MessageRenderer
{
    /**
     * @var \Twig_Environment
     */
    private $templating;

    /**
     * @var EmailLoader
     */
    private $emailLoader;

    /**
     * Construct.
     *
     * @param \Twig_Environment                                  $templating
     * @param \Lexik\Bundle\MailerBundle\Twig\Loader\EmailLoader $emailLoader
     *
     * @internal param array $defaultOptions
     */
    public function __construct(\Twig_Environment $templating, EmailLoader $emailLoader)
    {
        $this->templating = $templating;
        $this->emailLoader = $emailLoader;

        $this->templating->enableStrictVariables();
    }

    /**
     * Load all templates from the email.
     *
     * @param EmailInterface $email
     */
    public function loadTemplates(EmailInterface $email)
    {
        $this->emailLoader->setEmailTemplates($email);
    }

    /**
     * Render a template previously loaded.
     *
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    public function renderTemplate($view, array $parameters = [])
    {
        return $this->templating->render($view, $parameters);
    }

    /**
     * Enable or not strict variables.
     *
     * @param bool $strict
     */
    public function setStrictVariables($strict)
    {
        if ((bool) $strict) {
            $this->templating->enableStrictVariables();
        } else {
            $this->templating->disableStrictVariables();
        }
    }
}

<?php

namespace Lexik\Bundle\MailerBundle\Tools;

use Lexik\Bundle\MailerBundle\Message\MessageRenderer;
use Lexik\Bundle\MailerBundle\Model\EmailInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class PreviewGenerator
{
    /**
     * @var MessageRenderer
     */
    private $renderer;

    /**
     * @var string
     */
    private $defaultEmail;

    /**
     * @var array
     */
    private $templates;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param MessageRenderer $renderer
     * @param string          $defaultEmail
     */
    public function __construct(MessageRenderer $renderer, $defaultEmail)
    {
        $this->renderer = $renderer;
        $this->defaultEmail = $defaultEmail;
    }

    /**
     * @param string $name
     *
     * @return null|string
     */
    public function get($name)
    {
        return isset($this->templates[$name]) ? $this->templates[$name] : null;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param EmailInterface $email
     * @param string         $locale
     */
    public function getTemplatesPreview(EmailInterface $email, $locale)
    {
        $email->setLocale($locale);

        $this->renderer->loadTemplates($email);
        $this->renderer->setStrictVariables(false);

        $this->templates['fromName'] = $email->getFromName($this->defaultEmail);
        $this->templates['subject'] = $email->getSubject();
        $this->templates['content'] = $email->getBody();

        $this->errors = [
            'subject' => null,
            'from_name' => null,
            'html_content' => null,
        ];

        $suffix = $email->getChecksum();

        foreach ($this->errors as $template => $error) {
            try {
                $this->renderer->renderTemplate(sprintf('%s_%s', $template, $suffix));
            } catch (\Twig_Error $e) {
                $this->errors[$template] = $e->getRawMessage();
            }
        }

        $this->renderer->setStrictVariables(true);
    }
}

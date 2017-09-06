<?php

namespace Lexik\Bundle\MailerBundle\Twig\Loader;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;

/**
 * Custom Email template loader.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class EmailLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface, \ Twig_SourceContextLoaderInterface
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var array
     */
    protected $updateDates = [];

    /**
     * @param array $templates
     */
    public function __construct(array $templates = [])
    {
        $this->templates = $templates;
    }

    /**
     * @param string $name
     * @param string $template
     */
    public function setTemplate($name, $template)
    {
        $this->templates[$name] = $template;
    }

    /**
     * Load all templates for the given email (also load the related layout).
     *
     * @param EmailInterface $email
     */
    public function setEmailTemplates(EmailInterface $email)
    {
        if ($email->getLayout()) {
            // call of getLayoutBody set locale on layout
            $body = $email->getLayoutBody();

            // now we can compute correctly the key cache
            $layoutSuffix = $email->getLayout()->getChecksum();

            $this->setTemplate(sprintf('layout_%s', $layoutSuffix), $body);
            $this->updateDates[sprintf('layout_%s', $layoutSuffix)] = $email->getLayout()->getLastModifiedTimestamp();

            $content = strtr('{% extends \'<layout>\' %}{% block content %}<content>{% endblock %}', [
                '<layout>' => sprintf('layout_%s', $layoutSuffix),
                '<content>' => $email->getBody(),
            ]);
        } else {
            $content = $email->getBody();
        }

        $emailSuffix = $email->getChecksum();

        $this->setTemplate(sprintf('html_content_%s', $emailSuffix), $content);
        $this->setTemplate(sprintf('text_content_%s', $emailSuffix), $email->getBodyText());
        $this->setTemplate(sprintf('subject_%s', $emailSuffix), $email->getSubject());
        $this->setTemplate(sprintf('from_name_%s', $emailSuffix), $email->getFromName());
        $this->setTemplate(sprintf('from_address_%s', $emailSuffix), $email->getFromAddress());

        // keep updated at to be able to check if the template is fresh
        $this->updateDates[sprintf('html_content_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('text_content_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('subject_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('from_name_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('from_address_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        @trigger_error(sprintf('Calling "getSource" on "%s" is deprecated since 1.27. Use getSourceContext() instead.', get_class($this)), E_USER_DEPRECATED);

        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return new \Twig_Source($this->templates[$name], $name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return isset($this->templates[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        if (!isset($this->templates[$name])) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        if (isset($this->updateDates[$name]) && $time < $this->updateDates[$name]) {
            return false;
        }

        return true;
    }
}

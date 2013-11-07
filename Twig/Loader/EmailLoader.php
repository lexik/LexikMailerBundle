<?php

namespace Lexik\Bundle\MailerBundle\Twig\Loader;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;

use \Twig_Error_Loader;

/**
 * Custom Email template loader.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class EmailLoader extends \Twig_Loader_Array
{
    /**
     * @var array
     */
    protected $updateDates = array();

    /**
     * Load all templates for the given email (also load the related layout).
     *
     * @param EmailInterface $email
     */
    public function setEmailTemplates(EmailInterface $email)
    {
        if ($email->getLayout()) {
            $layoutSuffix = $email->getLayout()->getChecksum();

            $this->setTemplate(sprintf('layout_%s', $layoutSuffix), $email->getLayoutBody());
            $this->updateDates[sprintf('layout_%s', $layoutSuffix)] = $email->getLayout()->getLastModifiedTimestamp();

            $content = strtr('{% extends \'<layout>\' %}{% block content %}<content>{% endblock %}', array(
                '<layout>'  => sprintf('layout_%s', $layoutSuffix),
                '<content>' => $email->getBody(),
            ));

        } else {
            $content = $email->getBody();
        }

        $emailSuffix = $email->getChecksum();

        $this->setTemplate(sprintf('html_content_%s', $emailSuffix), $content);
        $this->setTemplate(sprintf('text_content_%s', $emailSuffix), $email->getBodyText());
        $this->setTemplate(sprintf('subject_%s', $emailSuffix), $email->getSubject());
        $this->setTemplate(sprintf('from_name_%s', $emailSuffix), $email->getFromName());

        // keep updated at to be able to check if the template is fresh
        $this->updateDates[sprintf('html_content_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('text_content_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('subject_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
        $this->updateDates[sprintf('from_name_%s', $emailSuffix)] = $email->getLastModifiedTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        if ( isset($this->updateDates[$name]) && $time < $this->updateDates[$name] ) {
            return false;
        }

        return true;
    }
}

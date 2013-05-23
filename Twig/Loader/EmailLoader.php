<?php

namespace Lexik\Bundle\MailerBundle\Twig\Loader;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;

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
            $layoutSuffix = md5($email->getLayout()->getReference());

            $this->setTemplate(sprintf('layout_%s', $layoutSuffix), $email->getLayoutBody());
            $this->updateDates[sprintf('layout_%s', $layoutSuffix)] = $email->getLayout()->getUpdatedAt()->format('U');

            $content = strtr('{% extends \'<layout>\' %} {% block content %} <content> {% endblock %}', array(
                '<layout>'  => sprintf('layout_%s', $layoutSuffix),
                '<content>' => $email->getBody(),
            ));

        } else {
            $content = $email->getBody();
        }

        $emailSuffix = md5($email->getReference());

        $this->setTemplate(sprintf('content_%s', $emailSuffix), $content);
        $this->setTemplate(sprintf('subject_%s', $emailSuffix), $email->getSubject());
        $this->setTemplate(sprintf('from_name_%s', $emailSuffix), $email->getFromName());

        // keep updated at to be bale to check if the template is fresh
        $this->updateDates[sprintf('content_%s', $emailSuffix)] = $email->getUpdatedAt()->format('U');
        $this->updateDates[sprintf('subject_%s', $emailSuffix)] = $email->getUpdatedAt()->format('U');
        $this->updateDates[sprintf('from_name_%s', $emailSuffix)] = $email->getUpdatedAt()->format('U');
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

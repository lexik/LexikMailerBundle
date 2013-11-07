<?php

namespace Lexik\Bundle\MailerBundle\Message;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;
use Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation;
use Lexik\Bundle\MailerBundle\Exception\NoTranslationException;
use Lexik\Bundle\MailerBundle\Message\ReferenceNotFoundMessage;
use Lexik\Bundle\MailerBundle\Message\NoTranslationMessage;
use Lexik\Bundle\MailerBundle\Message\UndefinedVariableMessage;
use Lexik\Bundle\MailerBundle\Message\TwigErrorMessage;

use Doctrine\ORM\EntityManager;

/**
 * Create some swift messages from email templates.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class MessageFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var MessageRender
     */
    private $renderer;

    /**
     * @var Annotation
     */
    private $annotationDriver;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $emails;

    /**
     * Constructor.
     *
     * @param EntityManager   $entityManager
     * @param MessageRenderer $renderer
     * @param Annotation      $driver
     * @param array           $defaultOptions
     */
    public function __construct(EntityManager $entityManager, MessageRenderer $renderer, Annotation $annotationDriver, $defaultOptions)
    {
        $this->em = $entityManager;
        $this->renderer = $renderer;
        $this->annotationDriver = $annotationDriver;
        $this->options = array_merge($this->getDefaultOptions(), $defaultOptions);
        $this->emails = array();
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'email_class'    => '',
            'admin_email'    => '',
            'default_locale' => 'en',
        );
    }

    /**
     * Find an email template and create a swift message.
     *
     * @param string $reference
     * @param mixed  $to
     * @param array  $parameters
     * @param string $locale
     * @return \Swift_Message
     */
    public function get($reference, $to, array $parameters = array(), $locale = null)
    {
        if (!isset($this->emails[$reference])) {
            $this->emails[$reference] = $this->em->getRepository($this->options['email_class'])->findOneByReference($reference);
        }

        $email = $this->emails[$reference];

        if ($email instanceof EmailInterface) {
            return $this->generateMessage($email, $to, $parameters, $locale);
        } elseif (null === $email) {
            return $this->generateExceptionMessage($reference);
        } else {
            throw new \RuntimeException();
        }
    }

    /**
     * Create a swift message
     *
     * @param EmailInterface $email
     * @param mixed          $to
     * @param array          $parameters
     * @param string         $locale
     * @return \Swift_Message
     */
    public function generateMessage(EmailInterface $email, $to, array $parameters = array(), $locale = null)
    {
        if (null == $locale) {
            $locale = $this->options['default_locale'];
        }

        // Check for annotations
        if (is_object($to)) {
            $name = $this->annotationDriver->getName($to);
            $to = $this->annotationDriver->getEmail($to);

            if (null !== $name && '' !== $name) {
                $to = array($to => $name);
            }
        }

        try {
            $email->setLocale($locale);
            $this->renderer->loadTemplates($email);

            $message = \Swift_Message::newInstance()
                ->setSubject($this->renderTemplate('subject', $parameters, $email->getChecksum()))
                ->setFrom($email->getFromAddress($this->options['admin_email']), $this->renderTemplate('from_name', $parameters, $email->getChecksum()))
                ->setTo($to)
                ->setBody($this->renderTemplate('html_content', $parameters, $email->getChecksum()), 'text/html');

            $textContent = $this->renderTemplate('text_content', $parameters, $email->getChecksum());

            if (null !== $textContent && '' !== $textContent) {
                $message->addPart($textContent, 'text/plain');
            }

            foreach ($email->getBccs() as $bcc) {
                $message->addBcc($bcc);
            }

        } catch (NoTranslationException $e) {
            $message = new NoTranslationMessage($email->getReference(), $locale);
            $message->setFrom($this->options['admin_email']);
            $message->setTo($this->options['admin_email']);

        } catch (\Twig_Error_Runtime $e) {
            $message = new UndefinedVariableMessage($e->getMessage(), $email->getReference());
            $message->setFrom($this->options['admin_email']);
            $message->setTo($this->options['admin_email']);

        } catch (\Twig_Error $e) {
            $message = new TwigErrorMessage($e->getRawMessage(), $email->getReference());
            $message->setFrom($this->options['admin_email']);
            $message->setTo($this->options['admin_email']);
        }

        return $message;
    }

    /**
     * Render template
     *
     * @param string $view
     * @param array  $parameters
     * @param string $checksum
     * @return string
     */
    protected function renderTemplate($view, array $parameters, $checksum)
    {
        $view = sprintf('%s_%s', $view, $checksum);

        return $this->renderer->renderTemplate($view, $parameters);
    }

    /**
     * Create swift message when Email is not found.
     *
     * @param string $reference
     * @return ReferenceNotFoundMessage
     */
    protected function generateExceptionMessage($reference)
    {
        $traces = debug_backtrace(false);

        $file = null;
        $line = null;

        foreach ($traces as $trace) {
            if (isset($trace['function']) && $trace['function'] == 'get' && isset($trace['class']) && $trace['class'] == __CLASS__) {
                $file = $trace['file'];
                $line = $trace['line'];
                break;
            }
        }

        $message = new ReferenceNotFoundMessage($reference, $file, $line);
        $message->setFrom($this->options['admin_email']);
        $message->setTo($this->options['admin_email']);

        return $message;
    }
}

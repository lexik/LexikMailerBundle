<?php

namespace Lexik\Bundle\MailerBundle\Message;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;
use Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation;
use Lexik\Bundle\MailerBundle\Events\SendListener;
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
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Lexik\Bundle\MailerBundle\Message\MessageRender
     */
    private $renderer;

    /**
     * @var Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation
     */
    private $annotationDriver;

    /**
     * @var \Swift_Events_EventDispatcher
     */
    private $swiftDispatcher;

    /**
     * @var array
     */
    private $options;

    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $emailRepository;

    /**
     * __construct
     *
     * @param EntityManager $entityManager
     * @param MessageRenderer $renderer
     * @param Annotation $driver
     * @param Swift $dispatcher
     * @param array $defaultOptions
     */
    public function __construct(EntityManager $entityManager, MessageRenderer $renderer, Annotation $annotationDriver, \Swift_Events_EventDispatcher $swiftDispatcher, $defaultOptions)
    {
        $this->em = $entityManager;
        $this->renderer = $renderer;
        $this->annotationDriver = $annotationDriver;
        $this->swiftDispatcher = $swiftDispatcher;
        $this->options = array_merge($this->getDefaultOptions(), $defaultOptions);

        $this->emailRepository = $this->em->getRepository($this->options['email_class']);
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'email_class'       => '',
            'admin_email'       => '',
            'default_locale'    => 'en',
        );
    }

    /**
     * Find an email template and create a swift message.
     *
     * @param string $reference
     * @param mixed $to
     * @param array $parameters
     * @param string $locale
     * @return Swift_Message
     */
    public function get($reference, $to, array $parameters = array(), $locale = null)
    {
        $email = $this->emailRepository->findOneByReference($reference);

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
     * @param mixed $to
     * @param array $parameters
     * @param string $locale
     * @return Swift_Message
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

            if (isset($name)) {
                $to = array($to => $name);
            }
        }

        try {
            $email->setLocale($locale);
            $this->renderer->loadTemplates($email);

            $swiftEmail = \Swift_Message::newInstance()
                ->setSubject($this->renderTemplate('subject', $parameters, $email->getReference()))
                ->setFrom($email->getFromAddress($this->options['admin_email']), $this->renderTemplate('from_name', $parameters, $email->getReference()))
                ->setTo($to)
                ->setBody($this->renderTemplate('content', $parameters, $email->getReference()), 'text/html');

            foreach ($email->getBccs() as $bcc) {
                $swiftEmail->addBcc($bcc);
            }

            return $swiftEmail;

        } catch (NoTranslationException $e) {
            $message = new NoTranslationMessage($email->getReference(), $locale);
            $this->bindExceptionListener($message);

            return $message;

        } catch (\Twig_Error $e) {
            $message = new TwigErrorMessage($e->getRawMessage(), $email->getReference());

            return $message->setFrom($this->options['admin_email'])
                ->setTo($this->options['admin_email']);
        }
    }

    /**
     * Render template
     *
     * @param string $view
     * @param array $parameters
     * @param string $type
     * @return string
     */
    protected function renderTemplate($view, array $parameters = array(), $reference = null)
    {
        $template = '';

        try {
            $template = $this->renderer->renderTemplate($view, $parameters);

        } catch (\Twig_Error_Runtime $e) {
            $message = new UndefinedVariableMessage($e->getMessage(), $reference);
            $this->bindExceptionListener($message);
        }

        return $template;
    }

    /**
     * Create swift message when Email is not found
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

    /**
     * Bind an exception message to be sent along the main mail by swift dispatcher
     *
     * @param \Swift_Message $message
     */
    protected function bindExceptionListener(\Swift_Message $message)
    {
        $message->setFrom($this->options['admin_email'])
            ->setTo($this->options['admin_email']);

        $this->swiftDispatcher->bindEventListener(new SendListener($message));
    }
}
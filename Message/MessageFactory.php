<?php

namespace Lexik\Bundle\MailerBundle\Message;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\MailerBundle\Exception\ReferenceNotFoundException;
use Lexik\Bundle\MailerBundle\Model\EmailInterface;
use Lexik\Bundle\MailerBundle\Model\EmailDestinationInterface;
use Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation;
use Lexik\Bundle\MailerBundle\Exception\NoTranslationException;
use Lexik\Bundle\MailerBundle\Message\ReferenceNotFoundMessage;
use Lexik\Bundle\MailerBundle\Message\NoTranslationMessage;
use Lexik\Bundle\MailerBundle\Message\UndefinedVariableMessage;
use Lexik\Bundle\MailerBundle\Message\TwigErrorMessage;
use Lexik\Bundle\MailerBundle\Signer\SignerFactory;

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
    protected $em;

    /**
     * @var MessageRenderer
     */
    protected $renderer;

    /**
     * @var Annotation
     */
    protected $annotationDriver;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $emails;

    /**
     * @var SignerFactory
     */
    protected $signer;

    /**
     * Constructor.
     *
     * @param EntityManager                                        $entityManager
     * @param MessageRenderer                                      $renderer
     * @param \Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation $annotationDriver
     * @param array                                                $defaultOptions
     * @param SignerFactory                                        $signer
     *
     * @internal param \Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation $driver
     */
    public function __construct(EntityManager $entityManager, MessageRenderer $renderer, Annotation $annotationDriver, $defaultOptions, SignerFactory $signer)
    {
        $this->em = $entityManager;
        $this->renderer = $renderer;
        $this->annotationDriver = $annotationDriver;
        $this->options = array_merge($this->getDefaultOptions(), $defaultOptions);
        $this->emails = array();
        $this->signer = $signer;
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
            'fallback_locale' => false,
        );
    }

    /**
     * Find an email from database
     *
     * @param  string $reference
     *
     * @throws ReferenceNotFoundException
     *
     * @return  EmailInterface|null
     */
    public function getEmail($reference)
    {
        if (!isset($this->emails[$reference])) {
            $this->emails[$reference] = $this->em->getRepository($this->options['email_class'])->findOneByReference($reference);
        }

        $email = $this->emails[$reference];

        if (!$email instanceof EmailInterface) {
            throw new ReferenceNotFoundException($reference, sprintf('Reference "%s" does not exist for email.', $reference));
        }

        return $email;
    }

    /**
     * Find an email template and create a swift message.
     *
     * @param string $reference
     * @param mixed  $to
     * @param array  $parameters
     * @param string $locale
     *
     * @throws \RuntimeException
     *
     * @return \Swift_Message
     */
    public function get($reference, $to, array $parameters = array(), $locale = null)
    {
        try {
            $email = $this->getEmail($reference);
            return $this->generateMessage($email, $to, $parameters, $locale);
        } catch (ReferenceNotFoundException $e) {
            return $this->generateExceptionMessage($reference);
        }
    }

    /**
     * Create a swift message
     *
     * @param EmailInterface $email
     * @param mixed          $to
     * @param array          $parameters
     * @param string         $locale
     *
     * @return \Swift_Message
     */
    public function generateMessage(EmailInterface $email, $to, array $parameters = array(), $locale = null)
    {
        if (null === $locale) {
            $locale = $this->options['default_locale'];
        }

        if (is_object($to)) {
            $to = $this->getRecipient($to);
        }

        $fromEmail = $this->renderFromAddress($email, $parameters);
        $fromName = $this->renderTemplate('from_name', $parameters, $email->getChecksum());

        try {
            $email->setLocale($locale);
            $this->renderer->loadTemplates($email);

            $message = $this->createMessageInstance()
                            ->setSubject($this->renderTemplate('subject', $parameters, $email->getChecksum()))
                            ->setFrom($fromEmail, $fromName)
                            ->setTo($to)
                            ->setBody($this->renderTemplate('html_content', $parameters, $email->getChecksum()), 'text/html');

            $textContent = $this->renderTemplate('text_content', $parameters, $email->getChecksum());

            if (null !== $textContent && '' !== $textContent) {
                $message->addPart($textContent, 'text/plain');
            }

            foreach ($email->getBccs() as $bcc) {
                $message->addBcc($bcc);
            }

            if (count($email->getHeaders()) > 0) {
                $headers = $message->getHeaders();
                foreach ($email->getHeaders() as $header) {
                    if (is_array($header) && isset($header['key'], $header['value'])) {
                        $headers->addTextHeader($header['key'], $header['value']);
                    }
                }
            }

        } catch (NoTranslationException $e) {
            $message = new NoTranslationMessage($email->getReference(), $locale);
            $message->setFrom($fromEmail, $fromName);
            $message->setTo($this->options['admin_email']);

        } catch (\Twig_Error_Runtime $e) {
            $message = new UndefinedVariableMessage($e->getMessage(), $email->getReference());
            $message->setFrom($fromEmail, $fromName);
            $message->setTo($this->options['admin_email']);

        } catch (\Twig_Error $e) {
            $message = new TwigErrorMessage($e->getRawMessage(), $email->getReference());
            $message->setFrom($fromEmail, $fromName);
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

    /**
     * Create Swiftf message instance
     *
     * @return \Swift_Message
     */
    protected function createMessageInstance()
    {
        $hasSigner = $this->signer->hasSigner();
        $class     = $hasSigner ? '\Swift_SignedMessage' : '\Swift_Message';

        $message = new $class();

        if ($hasSigner) {
            $message->attachSigner($this->signer->createSigner());
        }

        return $message;
    }

    /**
     * Render email from address
     *
     * @param  EmailInterface $email
     * @param  array          $parameters
     *
     * @return string
     */
    protected function renderFromAddress(EmailInterface $email, array $parameters = array())
    {
        if (null === $email->getFromAddress()) {
            return $this->options['admin_email'];
        }

        return $this->renderTemplate('from_address', $parameters, $email->getChecksum());
    }

    /**
     * @param object $to
     * @return array|string
     */
    protected function getRecipient($to)
    {
        if($to instanceof EmailDestinationInterface) {
            $name = $to->getRecipientName();
            $to = $to->getRecipientEmail();
        } else {
            $name = $this->annotationDriver->getName($to);
            $to = $this->annotationDriver->getEmail($to);
        }

        if (null !== $name && '' !== $name) {
            $to = array($to => $name);
        }

        return $to;
    }
}

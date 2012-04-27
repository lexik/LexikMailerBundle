<?php

namespace Lexik\Bundle\MailerBundle\Message;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class TwigErrorMessage extends \Swift_Message
{
    /**
     * Construct.
     *
     * @param string $message
     * @param string $reference
     */
    public function __construct($message, $reference)
    {
        parent::__construct();

        $body = <<<EOF
An error occured while trying to send email: {$reference}
{$message}
EOF;

        $this->setSubject('An exception occured')->setBody($body);
    }
}

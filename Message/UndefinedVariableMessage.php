<?php

namespace Lexik\Bundle\MailerBundle\Message;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class UndefinedVariableMessage extends ErrorMessage
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
An error occurred while trying to send email: {$reference}
{$message}
EOF;

        $this->setSubject('An exception occurred')->setBody($body);
    }
}

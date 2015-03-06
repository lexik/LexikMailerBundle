<?php

namespace Lexik\Bundle\MailerBundle\Message;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class ReferenceNotFoundMessage extends \Swift_Message implements ErrorMessageInterface
{
    /**
     * Construct.
     *
     * @param string $reference
     * @param string $file
     * @param string $line
     */
    public function __construct($reference, $file, $line)
    {
        parent::__construct();

        $body = <<<EOF
An error occurred while trying to send an email.
You tried to use a reference that does not exist : "{$reference}"
in "{$file}" at line {$line}
EOF;

        $this->setSubject('An exception occurred')->setBody($body);
    }
}

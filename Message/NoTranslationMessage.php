<?php

namespace Lexik\Bundle\MailerBundle\Message;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class NoTranslationMessage extends \Swift_Message implements ErrorMessageInterface
{
    /**
     * Construct
     *
     * @param string $reference
     * @param string $locale
     */
    public function __construct($reference, $locale)
    {
        parent::__construct();

        $body = <<<EOF
You have sent an email in the wrong language.
Reference : {$reference}
Language: {$locale}
EOF;

        $this->setSubject('An exception occurred')->setBody($body);
    }
}

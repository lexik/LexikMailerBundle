<?php

namespace Lexik\Bundle\MailerBundle\Message;

/**
 * Class Mailer
 * @package Lexik\Bundle\MailerBundle\Message
 */
class Mailer extends \Swift_Mailer
{
    /**
     * {@inheritDoc}
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if ($message instanceof TemplateDisabledMessage) {
            return 0;
        }

        return parent::send($message, $failedRecipients);
    }
}

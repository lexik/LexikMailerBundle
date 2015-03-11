<?php

namespace Lexik\Bundle\MailerBundle\Message;

/**
 * @author Yannick Snobbert <yannicksnobbert@gmail.com>
 */
class ErrorMessage extends \Swift_Message
{
    /**
     * {@inheritDoc}
     */
    public function addCc($address, $name = null)
    {
        //Avoid cc in error message mail
    }

    /**
     * {@inheritDoc}
     */
    public function setCc($addresses, $name = null)
    {
        //Avoid cc in error message mail
    }

    /**
     * {@inheritDoc}
     */
    public function addBcc($address, $name = null)
    {
        //Avoid bcc in error message mail
    }

    /**
     * {@inheritDoc}
     */
    public function setBcc($addresses, $name = null)
    {
        //Avoid bcc in error message mail
    }
}

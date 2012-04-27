<?php

namespace Lexik\Bundle\MailerBundle\Events;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Listen send event.
 *
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class SendListener implements \Swift_Events_SendListener
{
    /**
     * @var Swift_Message
     */
    private $message;

    /**
     * @var boolean
     */
    private $alreadySend = false;

    /**
     * Construct
     *
     * @param Swift_Message $message
     */
    public function __construct(\Swift_Message $message)
    {
        $this->message = $message;
    }

    /**
     * Invoked immediately before the Message is sent.
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
    }

    /**
     * Invoked immediately after the Message is sent.
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        if (!$this->alreadySend) {
            $this->alreadySend = true;
            $evt->getSource()->send($this->message);
        }
    }
}
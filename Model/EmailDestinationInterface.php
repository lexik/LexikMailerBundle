<?php

namespace Lexik\Bundle\MailerBundle\Model;

/**
 * EmailDestinationInterface to be applied to user entities
 *
 */
interface EmailDestinationInterface
{
    /**
     * Returns the name of person email is destined for
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Returns the email address destination
     *
     * @return string
     */
    public function getRecipientEmail();
}

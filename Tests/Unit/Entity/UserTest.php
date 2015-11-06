<?php

namespace Lexik\Bundle\MailerBundle\Tests\Entity;

use Lexik\Bundle\MailerBundle\Model\EmailDestinationInterface;

class UserTest implements EmailDestinationInterface
{
    public function getRecipientName()
    {
        return 'User';
    }

    public function getRecipientEmail()
    {
        return 'user@example.net';
    }
}

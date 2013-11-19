<?php

namespace Lexik\Bundle\MailerBundle\Tests\Entity;

use Lexik\Bundle\MailerBundle\Entity\Layout;
use Lexik\Bundle\MailerBundle\Entity\Email;
use Lexik\Bundle\MailerBundle\Entity\EmailTranslation;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTranslation()
    {
        $trans = new EmailTranslation('fr');

        $email = new Email();
        $email->addTranslation($trans);

        $layout = new Layout();
        $email->setLayout($layout);

        $this->assertSame($email->getTranslation('fr'), $trans);
        $this->assertSame($email->getTranslation('fr_FR'), $trans);
        $this->assertEquals($email->getTranslation('en')->getLang(), 'en');
        $this->assertEquals($email->getTranslation('es_ES')->getLang(), 'es');
        $this->assertSame($email->getLayout(), $layout);

        $this->setExpectedException('InvalidArgumentException');
        $email->getTranslation('foo');
    }

    public function testGetBccs()
    {
        $email = new Email();
        $email->setBcc('lex@lexik.fr');
        $this->assertEquals(array('lex@lexik.fr'), $email->getBccs());

        $email2 = new Email();
        $email2->setBcc('lex@lexik.fr;');
        $this->assertEquals(array('lex@lexik.fr'), $email2->getBccs());

        $email->setBcc('lex@lexik.fr; lex2@lexik.fr');
        $bccs = $email->getBccs();
        $this->assertEquals(2, count($bccs));
        $this->assertEquals('lex@lexik.fr', $bccs[0]);
        $this->assertEquals('lex2@lexik.fr', $bccs[1]);

        $email->setBcc(null);
        $this->assertEquals(array(), $email->getBccs());
    }
}

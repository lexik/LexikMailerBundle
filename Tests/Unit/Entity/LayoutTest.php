<?php

namespace Lexik\Bundle\MailerBundle\Tests\Entity;

use Lexik\Bundle\MailerBundle\Entity\Layout;
use Lexik\Bundle\MailerBundle\Entity\LayoutTranslation;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class LayoutTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTranslation()
    {
        $trans = new LayoutTranslation('fr');

        $layout = new Layout();
        $layout->addTranslation($trans);

        $this->assertSame($layout->getTranslation('fr'), $trans);
        $this->assertSame($layout->getTranslation('fr_FR'), $trans);
        $this->assertEquals($layout->getTranslation('en')->getLang(), 'en');
        $this->assertEquals($layout->getTranslation('es_ES')->getLang(), 'es');

        $this->setExpectedException('InvalidArgumentException');
        $layout->getTranslation('foo');
    }
}

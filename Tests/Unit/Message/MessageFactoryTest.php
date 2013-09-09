<?php

namespace Lexik\Bundle\MailerBundle\Tests\Message;

use Lexik\Bundle\MailerBundle\Twig\Loader\EmailLoader;

use Lexik\Bundle\MailerBundle\Message\MessageRenderer;
use Lexik\Bundle\MailerBundle\Message\MessageFactory;
use Lexik\Bundle\MailerBundle\Tests\Unit\BaseUnitTestCase;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class MessageFactoryTest extends BaseUnitTestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        $this->em = $this->getMockSqliteEntityManager();
        $this->createSchema($this->em);
        $this->loadFixtures($this->em);
    }

    public function testGetValidMessages()
    {
        $factory = $this->createMessageFactory();

        $message = $factory->get('rabbids-template', 'dude@email.fr', array('name' => 'dude', 'title' => 'message:'));
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(array('dude@email.fr' => null), $message->getTo());
        $this->assertEquals('lapin crétins', $message->getSubject());
        $this->assertEquals('blablabla message: dude aime les lapins crétins. blablabla', $message->getBody());
        $this->assertEquals(array('lapins@email.fr' => 'lapin'), $message->getFrom());

        $message = $factory->get('rabbids-template', 'dude@email.fr', array('name' => 'dude', 'title' => 'message:'), 'en');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(array('dude@email.fr' => null), $message->getTo());
        $this->assertEquals('raving rabbids', $message->getSubject());
        $this->assertEquals('blublublu message: dude likes raving rabbids. blublublu', $message->getBody());
        $this->assertEquals(array('rabbids@email.fr' => 'rabbid'), $message->getFrom());
    }

    public function testGetErrorMessages()
    {
        $factory = $this->createMessageFactory();

        // invalid reference
        $file = __FILE__;
        $body = <<<EOF
An error occured while trying to send an email.
You tried to use a reference that does not exist : "this-reference-does-not-exixt"
in "{$file}" at line 60
EOF;

        $message = $factory->get('this-reference-does-not-exixt', 'chuk@email.fr', array('name' => 'chuck'));
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(array('admin@email.fr' => null), $message->getTo());
        $this->assertEquals('An exception occured', $message->getSubject());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(array('admin@email.fr' => null), $message->getFrom());

        // no translation found
        $body = <<<EOF
You have sent an email in the wrong language.
Reference : rabbids-template
Language: de
EOF;

        $message = $factory->get('rabbids-template', 'chuk@email.fr', array('name' => 'chuck'), 'de');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(array('admin@email.fr' => null), $message->getTo());
        $this->assertEquals('An exception occured', $message->getSubject());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(array('admin@email.fr' => null), $message->getFrom());

        // wrong twig variable
        $body = <<<EOF
An error occured while trying to send email: rabbids-template
Unexpected "}"
EOF;

        $message = $factory->get('rabbids-template', 'chuk@email.fr', array('name' => 'chuck'), 'es');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(array('admin@email.fr' => null), $message->getTo());
        $this->assertEquals('An exception occured', $message->getSubject());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(array('admin@email.fr' => null), $message->getFrom());
    }

    protected function createMessageFactory()
    {
        $options = array(
            'email_class'    => 'Lexik\Bundle\MailerBundle\Entity\Email',
            'admin_email'    => 'admin@email.fr',
            'default_locale' => 'fr',
        );

        $loader = new EmailLoader(array());
        $templating = new \Twig_Environment($loader, array());
        $renderer = new \Lexik\Bundle\MailerBundle\Message\MessageRenderer($templating, $loader);

        $reder = new \Doctrine\Common\Annotations\AnnotationReader();
        $annotationDriver = new \Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation($reder);

        return new MessageFactory($this->em, $renderer, $annotationDriver, $options);
    }
}

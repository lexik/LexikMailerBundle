<?php

namespace Lexik\Bundle\MailerBundle\Tests\Message;

use Lexik\Bundle\MailerBundle\Message\MessageFactory;
use Lexik\Bundle\MailerBundle\Tests\Entity\UserTest;
use Lexik\Bundle\MailerBundle\Tests\Unit\BaseUnitTestCase;
use Lexik\Bundle\MailerBundle\Twig\Loader\EmailLoader;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class MessageFactoryTest extends BaseUnitTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
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

        $message = $factory->get('rabbids-template', 'dude@email.fr', ['name' => 'dude', 'title' => 'message:']);
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(['dude@email.fr' => null], $message->getTo());
        $this->assertEquals('lapin crétins', $message->getSubject());
        $this->assertEquals('blablabla message: dude aime les lapins crétins. blablabla', $message->getBody());
        $this->assertEquals(['lapins@email.fr' => 'lapin'], $message->getFrom());

        $message = $factory->get('rabbids-template', 'dude@email.fr', ['name' => 'dude', 'title' => 'message:'], 'en');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals(['dude@email.fr' => null], $message->getTo());
        $this->assertEquals('raving rabbids', $message->getSubject());
        $this->assertEquals('blublublu message: dude likes raving rabbids. blublublu', $message->getBody());
        $this->assertEquals(['rabbids@email.fr' => 'rabbid'], $message->getFrom());
    }

    public function testGetErrorMessages()
    {
        $factory = $this->createMessageFactory();

        // invalid reference
        $file = __FILE__;
        $body = <<<EOF
An error occurred while trying to send an email.
You tried to use a reference that does not exist : "this-reference-does-not-exist"
in "{$file}" at line 61
EOF;

        $message = $factory->get('this-reference-does-not-exist', 'chuk@email.fr', ['name' => 'chuck']);
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertInstanceOf('\Lexik\Bundle\MailerBundle\Message\ErrorMessageInterface', $message);
        $this->assertEquals(['admin@email.fr' => null], $message->getTo());
        $this->assertEquals('An exception occurred', $message->getSubject());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(['admin@email.fr' => null], $message->getFrom());

        // no translation found
        $body = <<<EOF
You have sent an email in the wrong language.
Reference : rabbids-template
Language: de
EOF;

        $message = $factory->get('rabbids-template', 'chuk@email.fr', ['name' => 'chuck'], 'de');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertInstanceOf('\Lexik\Bundle\MailerBundle\Message\ErrorMessageInterface', $message);
        $this->assertEquals(['admin@email.fr' => null], $message->getTo());
        $this->assertEquals('An exception occurred', $message->getSubject());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(['admin@email.fr' => null], $message->getFrom());

        // wrong twig variable
        $body = <<<EOF
An error occurred while trying to send email: rabbids-template
Unexpected "}".
EOF;

        $message = $factory->get('rabbids-template', 'chuk@email.fr', ['name' => 'chuck'], 'es');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertInstanceOf('\Lexik\Bundle\MailerBundle\Message\ErrorMessageInterface', $message);
        $this->assertEquals(['admin@email.fr' => null], $message->getTo());
        $this->assertEquals('An exception occurred', $message->getSubject());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(['admin@email.fr' => null], $message->getFrom());
    }

    public function testMessageHeaders()
    {
        $factory = $this->createMessageFactory();
        $message = $factory->get('test-headers', 'chuk@email.fr', ['name' => 'chuck'], 'fr');

        $this->assertEquals(true, $message->getHeaders()->has('X-SuperHeader'));
        $this->assertEquals('TestValue', $message->getHeaders()->get('X-SuperHeader')->getFieldBody());

        $this->assertEquals(true, $message->getHeaders()->has('X-MegaHeader'));
        $this->assertEquals('TestValue', $message->getHeaders()->get('X-MegaHeader')->getFieldBody());

        $this->assertEquals(false, $message->getHeaders()->has('X-Malformed-Header'));
    }

    protected function createMessageFactory()
    {
        $options = [
            'email_class' => 'Lexik\Bundle\MailerBundle\Entity\Email',
            'admin_email' => 'admin@email.fr',
            'default_locale' => 'fr',
        ];

        $loader = new EmailLoader([]);
        $templating = new \Twig_Environment($loader, []);
        $renderer = new \Lexik\Bundle\MailerBundle\Message\MessageRenderer($templating, $loader);

        $reder = new \Doctrine\Common\Annotations\AnnotationReader();
        $annotationDriver = new \Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation($reder);

        $signerFactory = new \Lexik\Bundle\MailerBundle\Signer\SignerFactory([]);

        return new MessageFactory($this->em, $renderer, $annotationDriver, $options, $signerFactory);
    }

    public function testGetEmail()
    {
        $factory = $this->createMessageFactory();

        $email = $factory->getEmail('rabbids-template');
        $this->assertInstanceOf('Lexik\Bundle\MailerBundle\Model\EmailInterface', $email);
        $this->assertEquals($email->getReference(), 'rabbids-template');

        $this->setExpectedException('Lexik\Bundle\MailerBundle\Exception\ReferenceNotFoundException');
        $factory = $this->createMessageFactory();
        $factory->getEmail('this-reference-does-not-exist');
    }

    public function testGetRecipient()
    {
        $factory = $this->createMessageFactory();

        $class = new \ReflectionClass($factory);
        $method = $class->getMethod('getRecipient');
        $method->setAccessible(true);

        $user = new UserTest();
        $recipient = $method->invokeArgs($factory, [$user]);
        $this->assertEquals(['user@example.net' => 'User'], $recipient);
    }

    public function testFallbackLanguage()
    {
        $factory = $this->createMessageFactory();

        $message = $factory->get('rabbids-template', 'test@email.local', ['name' => 'test', 'title' => 'test'], 'nl');

        $this->assertEquals('fafafa test test houdt van Raving Rabbids fafafa', $message->getBody());
    }
}

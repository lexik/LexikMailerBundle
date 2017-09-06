<?php

namespace Lexik\Bundle\MailerBundle\Tests\Entity;

use Lexik\Bundle\MailerBundle\Entity\Layout;
use Lexik\Bundle\MailerBundle\Entity\LayoutTranslation;
use Symfony\Component\Validator\Validation;

class LayoutTranslationTest extends \PHPUnit_Framework_TestCase
{
    private $validator;

    private $layout;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $this->layout = $this->getMock(Layout::class);
    }

    public function testSuccessAddingFrenchTranslationLayout()
    {
        $entity = new LayoutTranslation('fr');
        $entity->setBody('blablabla {{title}} {% block content %}{% endblock %} blablabla');
        $entity->setLayout($this->layout);

        $errors = $this->validator->validate($entity);

        $this->assertEquals(0, count($errors));
    }

    public function testSuccessAddingChineseTranslationLayout()
    {
        $entity = new LayoutTranslation('zh_CN');
        $entity->setBody('blablabla {{title}} {% block content %}{% endblock %} blablabla');
        $entity->setLayout($this->layout);

        $errors = $this->validator->validate($entity);

        $this->assertEquals(0, count($errors));
    }
}

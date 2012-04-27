<?php

namespace Lexik\Bundle\MailerBundle\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;

/**
 * Driver to find values of annotated properties.
 *
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class Annotation
{
    /**
     * @var Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * Construct
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Get email from a class
     *
     * @param mixed $obj
     * @return string
     */
    public function getEmail($obj)
    {
        return $this->findValue($obj, 'Lexik\Bundle\MailerBundle\Mapping\Annotation\Address');
    }

    /**
     * Get name from a class
     *
     * @param mixed $obj
     * @return string
     */
    public function getName($obj)
    {
        return $this->findValue($obj, 'Lexik\Bundle\MailerBundle\Mapping\Annotation\Name');
    }

    /**
     * Get value
     *
     * @param mixed $obj
     * @param string $className
     * @return string
     */
    private function findValue($obj, $className)
    {
        $reflClass = new \ReflectionClass($obj);

        // Find on property
        foreach ($reflClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($this->reader->getPropertyAnnotation($property, $className)) {
                return $obj->$property;
            }
        }

        // Find on method
        foreach ($reflClass->getMethods() as $method) {
            if ($this->reader->getMethodAnnotation($method, $className)) {
                return $obj->{$method->getName()}();
            }
        }
    }
}
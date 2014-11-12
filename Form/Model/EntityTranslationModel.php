<?php

namespace Lexik\Bundle\MailerBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class EntityTranslationModel
{
    /**
     * @var mixed
     *
     * @Assert\Valid()
     */
    private $entity;

    /**
     * @var mixed
     *
     * @Assert\Valid()
     */
    private $translation;

    /**
     * @param mixed $entity
     * @param mixed $translation
     */
    public function __construct($entity, $translation)
    {
        $this->entity = $entity;
        $this->translation = $translation;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
    }

    /**
     * @return mixed
     */
    public function getTranslation()
    {
        return $this->translation;
    }
}

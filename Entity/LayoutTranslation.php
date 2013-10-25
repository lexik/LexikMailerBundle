<?php

namespace Lexik\Bundle\MailerBundle\Entity;

use Lexik\Bundle\MailerBundle\Entity\Layout;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lexik_layout_translation")
 *
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class LayoutTranslation
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank()
     * @Assert\Length(max=2)
     */
    protected $lang;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var Lexik\Bundle\MailerBundle\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Layout", inversedBy="translations")
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $layout;

    /**
     * __construct
     *
     * @param string $lang
     */
    public function __construct($lang = null)
    {
        $this->lang = $lang;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set lang
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get layout
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set layout
     *
     * @param Layout $layout
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Display language
     *
     * @return string
     */
    public function displayLanguage()
    {
        return Locale::getDisplayLanguage($this->getLang());
    }
}

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
     * @Assert\MaxLength(2)
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
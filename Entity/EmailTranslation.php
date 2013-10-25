<?php

namespace Lexik\Bundle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lexik_email_translation")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EmailTranslation
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
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $body;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="body_text", nullable=true)
     */
    protected $bodyText;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="from_address", nullable=true)
     * @Assert\Email()
     */
    protected $fromAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="from_name", nullable=true)
     */
    protected $fromName;

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
     * @var \Lexik\Bundle\MailerBundle\Entity\Email
     *
     * @ORM\ManyToOne(targetEntity="Email", inversedBy="translations")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    protected $email;

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
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
     * Get bodyText
     *
     * @return string
     */
    public function getBodyText()
    {
        return $this->bodyText;
    }

    /**
     * Set bodyText
     *
     * @param string $bodyText
     */
    public function setBodyText($bodyText)
    {
        $this->bodyText = $bodyText;
    }

    /**
     * Get from address
     *
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * Set from address
     *
     * @param string $fromAddress
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;
    }

    /**
     * Get from name
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Set from name
     *
     * @param string $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
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
     * Get email
     *
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param Email $email
     */
    public function setEmail(Email $email)
    {
        $this->email = $email;
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

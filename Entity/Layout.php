<?php

namespace Lexik\Bundle\MailerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\MailerBundle\Exception\NoTranslationException;
use Lexik\Bundle\MailerBundle\Model\LayoutInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="lexik_layout")
 * @DoctrineAssert\UniqueEntity("reference")
 *
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class Layout implements LayoutInterface
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
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    protected $reference;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LayoutTranslation", mappedBy="layout", cascade={"all"})
     */
    protected $translations;

    /**
     * The locale to use to get the right layout content.
     *
     * @var string
     */
    private $locale;

    /**
     * The default locale which can be used as a fallback in case there
     * is no locale for the selected email context.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $defaultLocale;

    /**
     * Translation object for the current $this->locale value.
     *
     * @var \Lexik\Bundle\MailerBundle\Entity\LayoutTranslation
     */
    private $currentTranslation;

    /**
     * __construct.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set reference.
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get translations.
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add a translation.
     *
     * @param LayoutTranslation $translation
     */
    public function addTranslation(LayoutTranslation $translation)
    {
        $this->translations->add($translation);
        $translation->setLayout($this);
    }

    /**
     * Remove a translation.
     *
     * @param LayoutTranslation $translation
     */
    public function removeTranslation(LayoutTranslation $translation)
    {
        $this->translations->removeElement($translation);
        $translation->setLayout(null);
    }

    /**
     * Get LayoutTranslation for a given lang, if not exist it will be created.
     *
     * @param string $lang
     *
     * @return \Lexik\Bundle\MailerBundle\Entity\LayoutTranslation
     */
    public function getTranslation($lang)
    {
        // Check if locale given
        if (strpos($lang, '_')) {
            $parts = explode('_', $lang);
            $lang = array_shift($parts);
        }

        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLang() === $lang) {
                return $translation;
            }
        }

        $translation = new LayoutTranslation($lang);
        $translation->setLayout($this);

        return $translation;
    }

    /**
     * __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->reference ?: '';
    }

    /**
     * Set the current translation.
     *
     * @throws NoTranslationException
     */
    protected function setCurrentTranslation($fallback = false)
    {
        if (!($this->currentTranslation instanceof LayoutTranslation) || $this->currentTranslation->getLang() !== $this->locale) {
            $i = 0;
            $end = count($this->translations);
            $found = false;

            while ($i < $end && !$found) {
                $found = ($this->translations[$i]->getLang() === $this->locale);
                ++$i;
            }

            if ($found) {
                $this->currentTranslation = $this->translations[$i - 1];
            } else {
                if ($fallback && $this->getDefaultLocale()) {
                    $this->setLocale($this->getDefaultLocale());
                    $this->setCurrentTranslation();
                } else {
                    throw new NoTranslationException($this->locale, sprintf('No "%s" translation for layout "%s".', $this->locale, $this->reference));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale, $fallback = false)
    {
        $this->locale = $locale;

        $this->setCurrentTranslation($fallback);
    }

    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;
    }

    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->currentTranslation->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModifiedTimestamp()
    {
        $date = $this->currentTranslation->getUpdatedAt();

        if (!$date instanceof \DateTime) {
            $date = new \DateTime('now');
        }

        return $date->format('U');
    }

    /**
     * {@inheritdoc}
     */
    public function getChecksum()
    {
        return md5(sprintf('%s_%s', $this->locale, $this->getReference()));
    }
}

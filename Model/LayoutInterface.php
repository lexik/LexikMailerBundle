<?php

namespace Lexik\Bundle\MailerBundle\Model;

/**
 * Layout interface.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface LayoutInterface
{
    /**
     * Returns the layout template reference.
     *
     * @return string
     */
    public function getReference();

    /**
     * Set the locale to use for the layout content.
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Returns the layout's body according to the layout locale.
     *
     * @return string
     */
    public function getBody();

    /**
     * Returns the timestamp of the last modification date.
     *
     * @return int
     */
    public function getLastModifiedTimestamp();
    
    /**
     * Return checksum of layout
     * 
     * @return string
     */
    public function getChecksum();
}

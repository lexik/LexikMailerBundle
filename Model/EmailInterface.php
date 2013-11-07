<?php

namespace Lexik\Bundle\MailerBundle\Model;

/**
 * EmailInterface in order to be used with the MessageFactory.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
interface EmailInterface
{
    /**
     * Returns the email template reference.
     *
     * @return string
     */
    public function getReference();

    /**
     * Set the locale to use for the email content.
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Returns the email's subject according to the email locale.
     *
     * @return string
     */
    public function getSubject();

    /**
     * Returns the email's html body according to the email locale.
     *
     * @return string
     */
    public function getBody();

    /**
     * Returns the email's textual body according to the email locale.
     *
     * @return string
     */
    public function getBodyText();

    /**
     * Returns the layout's body for this email according to the email locale.
     *
     * @return string
     */
    public function getLayoutBody();

    /**
     * Returns the email address of the sender according to the email locale.
     *
     * @param string $default
     * @return string
     */
    public function getFromAddress($default);

    /**
     * Returns the name of the sender according to the email locale.
     *
     * @return string
     */
    public function getFromName();

    /**
     * Return all BCCs
     *
     * @return array
     */
    public function getBccs();

    /**
     * Returns the timestamp of the last modification date.
     *
     * @return int
     */
    public function getLastModifiedTimestamp();
    
    /**
     * Return checksum of email
     * 
     * @return string
     */
    public function getChecksum();
}

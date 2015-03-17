<?php

namespace Lexik\Bundle\MailerBundle\Exception;

/**
 * @author Yannick Snobbert <yannick.snobbert@gmail.comr>
 */
class ReferenceNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $reference;

    /**
     * @param string $reference
     * @param string $message
     * @param int    $code
     * @param        \Exception
     */
    public function __construct($reference, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->reference = $reference;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}

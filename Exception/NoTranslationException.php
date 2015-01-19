<?php

namespace Lexik\Bundle\MailerBundle\Exception;

/**
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class NoTranslationException extends \Exception
{
    /**
     * @var string
     */
    private $lang;

    public function __construct($lang, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->lang = $lang;

        parent::__construct($message, $code, $previous);
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
}

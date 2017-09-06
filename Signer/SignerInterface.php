<?php

namespace Lexik\Bundle\MailerBundle\Signer;

/**
 * Signer interface.
 *
 * @author Sébastien Dieunidou <sebastien@bedycasa.com>
 */
interface SignerInterface
{
    /**
     * Create  and return a signature for a swift message.
     *
     * @return \Swift_Signer
     */
    public function createSignature();
}

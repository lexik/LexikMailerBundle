<?php

namespace Lexik\Bundle\MailerBundle\Signer;

/**
 * Signer interface
 */
interface SignerInterface
{
    /**
     * Create  and return a signature for a swift message
     * 
     * @return \Swift_Signer
     */
    public function createSignature();
}
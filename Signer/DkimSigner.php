<?php

namespace Lexik\Bundle\MailerBundle\Signer;

/**
 * Create a DKIM signature for a Swift_Message
 * 
 * @author Sébastien Dieunidou <sebastien@bedycasa.com>
 */
class DkimSigner implements SignerInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     * 
     * @param array $defaultOptions
     */
    public function __construct($defaultOptions)
    {
        $this->options = array_merge($this->getDefaultOptions(), $defaultOptions);
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'private_key_path' => '',
            'domain'           => '',
            'selector'         => ''
        );
    }
    
    /**
     * Get private key
     * 
     * @return string
     * 
     * @throws \Exception
     */
    protected function getPrivateKey()
    {
        if (empty($this->options['private_key_path'])) {
            throw new \Exception('Missing private_key_path for generate a DKIM signature');
        }
        
        $privateKey = @file_get_contents($this->options['private_key_path']);
        if (false === $privateKey) {
            throw new \Exception(sprintf('Can\'t read private key in file %s', $this->options['private_key_path']));
        }
        
        return $privateKey;
    }
    
    /**
     * Create  and return a signature for a swift message
     * 
     * @return \Swift_Signer
     */
    public function createSignature()
    {
        return new \Swift_Signers_DKIMSigner($this->getPrivateKey(), $this->options['domain'], $this->options['selector']);
    }
}
<?php

namespace Lexik\Bundle\MailerBundle\Signer;

/**
 * Create a DKIM signature for a Swift_Message.
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
     * Constructor.
     *
     * @param array $defaultOptions
     */
    public function __construct($defaultOptions)
    {
        $this->options = array_merge($this->getDefaultOptions(), $defaultOptions);
    }

    /**
     * Get default options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'private_key_path' => '',
            'domain' => '',
            'selector' => '',
        ];
    }

    /**
     * Get private key.
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getPrivateKey()
    {
        if (empty($this->options['private_key_path'])) {
            throw new \RuntimeException('Missing private_key_path for generate a DKIM signature.');
        }

        if (!is_readable($this->options['private_key_path'])) {
            throw new \RuntimeException(sprintf('Private key file does not exists or is not readable: %s', $this->options['private_key_path']));
        }

        $privateKey = file_get_contents($this->options['private_key_path']);

        if (false === $privateKey) {
            throw new \RuntimeException(sprintf('Can\'t read private key in file %s', $this->options['private_key_path']));
        }

        return $privateKey;
    }

    /**
     * Create  and return a signature for a swift message.
     *
     * @return \Swift_Signer
     */
    public function createSignature()
    {
        return new \Swift_Signers_DKIMSigner($this->getPrivateKey(), $this->options['domain'], $this->options['selector']);
    }
}

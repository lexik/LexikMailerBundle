<?php

namespace Lexik\Bundle\MailerBundle\Signer;

/**
 * Signer Factory.
 *
 * @author Sébastien Dieunidou <sebastien@bedycasa.com>
 */
class SignerFactory
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $signers;

    /**
     * Constructor.
     *
     * @param array $defaultOptions
     */
    public function __construct($defaultOptions)
    {
        $this->options = array_merge($this->getDefaultOptions(), $defaultOptions);
        $this->signers = [];
    }

    /**
     * Get default options.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'signer' => null,
        ];
    }

    /**
     * Return true if the message need to be signed.
     *
     * @return bool
     */
    public function hasSigner()
    {
        return null !== $this->options['signer'];
    }

    /**
     * Add a signer.
     *
     * @param string          $signerName
     * @param SignerInterface $signer
     */
    public function addSigner($signerName, SignerInterface $signer)
    {
        $this->signers[$signerName] = $signer;
    }

    /**
     * Get a signer.
     *
     * @param string $signerName
     *
     * @throws \Exception
     *
     * @return SignerInterface
     */
    protected function getSigner($signerName)
    {
        if (empty($this->signers[$signerName])) {
            throw new \Exception(sprintf('Signer %s doesnt exist', $signerName));
        }

        return $this->signers[$signerName];
    }

    /**
     * Create signer.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function createSigner()
    {
        if (!$this->hasSigner()) {
            return false;
        }

        return $this->getSigner($this->options['signer'])->createSignature();
    }
}

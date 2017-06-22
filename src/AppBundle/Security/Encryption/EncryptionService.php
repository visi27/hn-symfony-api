<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Security\Encryption;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Psr\Log\LoggerInterface;

class EncryptionService
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EncryptionService constructor.
     *
     * @param string          $key
     * @param LoggerInterface $logger
     */
    public function __construct($key, LoggerInterface $logger)
    {
        $this->key = $key;
        $this->logger = $logger;
    }

    /**
     * @param $plainText
     *
     * @return string
     */
    public function encrypt($plainText)
    {
        $cryptoKey = $this->loadEncryptionKey();

        return Crypto::encrypt($plainText, $cryptoKey);
    }

    public function decrypt($encryptedText)
    {
        $cryptoKey = $this->loadEncryptionKey();
        try {
            $plainText = Crypto::decrypt($encryptedText, $cryptoKey);
        } catch (WrongKeyOrModifiedCiphertextException $ex) {
            $this->logger->critical('Error decrypting data');
            $this->logger->critical($ex->getMessage());
            $plainText = '';
        }

        return $plainText;
    }

    /**
     * @return Key
     */
    private function loadEncryptionKey()
    {
        return Key::loadFromAsciiSafeString($this->key);
    }
}

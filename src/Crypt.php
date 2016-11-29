<?php
/**
 * Inspired by CodeIgniter Encrypt
 * https://github.com/EllisLab/CodeIgniter/blob/develop/system/libraries/Encrypt.php
 */

namespace G4\Crypto;

use G4\Crypto\Adapter;

class Crypt
{

    /**
     * @var Adapter
     */
    private $adapter;
    /**
     * @var string
     */
    private $encryptedMessage;

    /**
     * @var string
     */
    private $encryptionKey = '';

    /**
     * @var string
     */
    private $hashedEncryptionKey;

    /**
     * @var int
     */
    private $initVectorSize;

    /**
     * Mcrypt cipher consts
     *
     * @var string
     */
    private $mcryptCipher = MCRYPT_RIJNDAEL_256;

    /**
     * Mcrypt mode consts
     *
     * @var string
     */
    private $mcryptMode = MCRYPT_MODE_CBC;

    /**
     * @var string
     */
    private $message;


    /**
     * Determine if mcrypt extension is installed
     * Sets Initialization Vector size
     *
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->initVectorSize = $this->adapter->getIvSize($this->mcryptCipher, $this->mcryptMode);
    }

    /**
     * Decode message
     *
     * @param string $encryptedMessage
     * @return string
     */
    public function decode($encryptedMessage)
    {
        $this->encryptedMessage = $encryptedMessage;

        $this->decrypt();

        return $this->message;
    }

    /**
     * Encode message
     *
     * @param string $message
     * @return string
     */
    public function encode($message)
    {
        $this->message = $message;

        $this->encrypt();

        return $this->encryptedMessage;
    }

    /**
     * Set encryption key
     *
     * @param string $encryptionKey
     * @return $this
     */
    public function setEncryptionKey($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
        $this->hashedEncryptionKey = md5($this->encryptionKey);

        return $this;
    }

    /**
     * Decrypt message using mcrypt lib
     */
    private function decrypt()
    {
        $this->message = $this->base64url_decode($this->encryptedMessage);

        $cipher = new Cipher($this->message, $this->hashedEncryptionKey);

        $this->message = $cipher->removeCipherNoise();

        if ($this->initVectorSize > strlen($this->message)) {
            $this->message = false;

            return;
        }

        $initVector = substr($this->message, 0, $this->initVectorSize);

        $this->message = substr($this->message, $this->initVectorSize);

        $this->message = rtrim(
            $this->adapter->decrypt(
                $this->mcryptCipher,
                $this->hashedEncryptionKey,
                $this->message,
                $this->mcryptMode,
                $initVector
            ),
            "\0"
        );
    }

    /**
     * Encrypt message using mcrypt lib
     */
    private function encrypt()
    {
        $initVector = $this->adapter->createIv($this->initVectorSize, MCRYPT_RAND);

        $this->encryptedMessage = $initVector . $this->adapter->encrypt(
                $this->mcryptCipher,
                $this->hashedEncryptionKey,
                $this->message,
                $this->mcryptMode,
                $initVector
            );

        $cipher = new Cipher($this->encryptedMessage, $this->hashedEncryptionKey);

        $this->encryptedMessage = $this->base64url_encode($cipher->addCipherNoise());
    }

    /**
     * Modified base64_encode which is url friendly (removes + and / characters from result string)
     *
     * @param $data
     * @return string
     */
    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Modified base64_decode (see base64_encode)
     *
     * @param $data
     * @return string
     */
    function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}
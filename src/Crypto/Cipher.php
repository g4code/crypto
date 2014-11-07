<?php
/**
 * Modeled on CodeIgniter Encrypt
 * https://github.com/EllisLab/CodeIgniter/blob/develop/system/libraries/Encrypt.php
 *
 * Adds permuted noise to the IV + encrypted data to protect
 * against Man-in-the-middle attacks on CBC mode ciphers
 * http://www.ciphersbyritter.com/GLOSSARY.HTM#IV
 */

namespace G4\Crypto;

class Cipher
{
    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $key;

    /**
     * Noised message
     *
     * @var string
     */
    private $str;

    /**
     * Sets message data and encryption key
     *
     * @param string $data
     * @param string $key
     */
    public function __construct($data, $key)
    {
        $this->data = $data;
        $this->key  = sha1($key);
        $this->str  = '';
    }

    /**
     * Adds permuted noise to the IV + encrypted data to protect
     * against Man-in-the-middle attacks on CBC mode ciphers
     *
     * @return string
     */
    public function addCipherNoise()
    {
        for ($i = 0, $j = 0, $ld = strlen($this->data), $lk = strlen($this->key); $i < $ld; ++$i, ++$j) {

            if ($j >= $lk) {
                $j = 0;
            }

            $this->str .= chr((ord($this->data[$i]) + ord($this->key[$j])) % 256);
        }
        return $this->str;
    }

    /**
     * Removes permuted noise from the IV + encrypted data
     *
     * @return string
     */
    public function removeCipherNoise()
    {
        for ($i = 0, $j = 0, $ld = strlen($this->data), $lk = strlen($this->key); $i < $ld; ++$i, ++$j) {

            if ($j >= $lk) {
                $j = 0;
            }

            $temp = ord($this->data[$i]) - ord($this->key[$j]);

            if ($temp < 0) {
                $temp += 256;
            }

            $this->str .= chr($temp);
        }
        return $this->str;
    }
}
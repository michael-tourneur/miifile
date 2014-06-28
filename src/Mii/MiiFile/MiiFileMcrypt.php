<?php namespace Mii\MiiFile;

use  Mii\MiiFile\Interfaces\MiiFileEncryptInterface;

class MiiFileMcrypt implements MiiFileEncryptInterface{

  const CIPHER = MCRYPT_RIJNDAEL_128; // Rijndael-128 is AES
  const MODE   = MCRYPT_MODE_CBC;

  /* Cryptographic key of length 16, 24 or 32. NOT a password! */
  protected $key;

  public function __construct($key) {
    $this->key = $key;
  }

  public function encrypt($fileSource) {
    $fileDecrypted = file_get_contents($fileSource);

    $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
    $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_RANDOM);
    $fileEncrypted = mcrypt_encrypt(self::CIPHER, $this->key, $fileDecrypted, self::MODE, $iv);
    $fileEncrypted = base64_encode($iv.$fileEncrypted);

    $nfile = fopen($fileSource, 'w');
    fwrite($nfile, $fileEncrypted);
    fclose($nfile);
    return $fileEncrypted;
  }

  public function decrypt($fileSource) {
    $fileEncrypted = file_get_contents($fileSource);

    $fileEncrypted = base64_decode($fileEncrypted);
    $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
    if (strlen($fileEncrypted) < $ivSize) {
        throw new Exception('Missing initialization vector');
    }

    $iv = substr($fileEncrypted, 0, $ivSize);
    $fileEncrypted = substr($fileEncrypted, $ivSize);
    $fileDecrypted = mcrypt_decrypt(self::CIPHER, $this->key, $fileEncrypted, self::MODE, $iv);
    return rtrim($fileDecrypted, "\0");
  }

  public function canEncrypt() {
    if(empty($this->key)) return false;
    return true;
  }
}

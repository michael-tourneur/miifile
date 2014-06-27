<?php namespace Mii\MiiFile\Interfaces;

interface MiiFileEncryptInterface {

  public function encrypt($plaintext);
  public function decrypt($ciphertext);
  
}

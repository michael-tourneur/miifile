<?php namespace Mii\MiiFile\Interfaces;

interface MiiFileEncryptInterface {

  public function encrypt($fileDecrypted);
  public function decrypt($fileEncrypted);
  public function canEncrypt();

}

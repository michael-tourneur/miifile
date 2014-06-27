<?php namespace Mii\MiiFile;

use  Mii\MiiFile\Interfaces\MiiFileEncryptInterface;

class MiiFile{

  protected $miiFileEncrypt;

  public function __construct(MiiFileEncryptInterface $miiFileEncrypt){
    $this->miiFileEncrypt = $miiFileEncrypt;
  }

  public function greeting(){
    return "What up dawg";
  }

}

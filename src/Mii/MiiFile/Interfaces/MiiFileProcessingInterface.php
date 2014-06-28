<?php namespace Mii\MiiFile\Interfaces;

interface MiiFileProcessingInterface {
  public function open($ressource);
  public function save($path);
  public function crop();
  public function resize($width, $height);

}

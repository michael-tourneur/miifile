<?php namespace Mii\MiiFile;

use Mii\MiiFile\Interfaces\MiiFileProcessingInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;
use Imagine\Image\Point;

class MiiFileProcessingImagine implements MiiFileProcessingInterface {

  protected $imagine;
  protected $image;

  public function __construct($driver) {
    $class = 'Imagine\\'.$driver.'\Imagine';
    $this->imagine = new $class();
  }

  public function open($ressource) {
    $this->image = $this->imagine->open($ressource);
    return $this;
  }

  public function save($path) {
    $this->image->save($path);
    return $this;
  }

  public function crop() {

  }

  public function resize($width, $height) {
    $this->image->resize(new Box($width, $height));
    return $this;
  }

}

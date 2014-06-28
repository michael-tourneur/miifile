<?php namespace Mii\MiiFile;


trait MiiFileTrait {

  public function link($title = null, $attributes = array(), $secure = null) {
    return (isset($this->id)) ? \App::__get('miiFile')->link($this, $title, $attributes, $secure) : null;
  }

  public function show($attrs = array()) {
    return(isset($this->id)) ? \App::__get('miiFile')->show($this, $attrs) : null;
  }

}

<?php
return array(
    /*
     * Eloquent Model class to save file informations.
     */
    'model' => 'FileUpload',

   /*
    * Private key to encrypt/descrypt your files.
    * If empty your file won't be encrypted.
    */
    'key' => '',

  /*
   * Do you want to encrypt your files? true or false
   */
   'encrypt' => true,

  /*
   * Default destination of your files.
   * Default path is public/upload
   * Do not start by a slash.
   */
   'destination' => 'upload'

);

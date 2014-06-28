<?php
return array(
    /*
     * Driver to process images.
     * 'Gd', 'Imagick', 'Gmagick'
     */
    'driver' => 'Gd',

    /*
     * Eloquent Model class to save file informations.
     */
    'model' => 'FileUpload',

   /*
    * Private key to encrypt/descrypt your files.
    * If empty your file won't be encrypted.
    */
    'key' => 'dasdadasdasdasd8as87d86asdas',

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

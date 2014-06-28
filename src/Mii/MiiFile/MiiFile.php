<?php namespace Mii\MiiFile;

use Mii\MiiFile\Interfaces\MiiFileEncryptInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MiiFile{

  protected $miiFileEncrypt;
  protected $model;

  private $options = array();
  private $files = array();
  private $excludes = array();

  public function __construct(MiiFileEncryptInterface $miiFileEncrypt){
    $this->miiFileEncrypt = $miiFileEncrypt;

    $this->model = ($model = \Config::get('mii-file::model'))
      ? '\\'.ltrim($model, '\\')
      : null;

    $this->options = array(
      'destination'  => \Config::get('mii-file::destination'),
      'encrypt'      => (bool) \Config::get('mii-file::encrypt'),
      'width'        => 0, //auto
      'height'       => 0, //auto
    );
  }

  /**
    * handle files (single & multiple input) uploaded though a form.
    *
    * @param array $rename
    * @return void
    */
  public function handle($rename = array()){
    foreach($_FILES as $input => $file) {
      if(\Input::hasFile($input)) {
        $uploadedFile = \Input::file($input);
        // Handle single file
        if($uploadedFile instanceof UploadedFile){
          $this->add($uploadedFile, function() use ($uploadedFile, $rename){
            return (array_key_exists($input, $rename)) ? $rename[$input] : false;
          });
        }
        // Handle multi files
        elseif(is_array($uploadedFile)) {
          foreach($uploadedFile as $file) {
            if($uploadedFile instanceof UploadedFile){
              $this->add($uploadedFile, function() use ($uploadedFile, $rename){
                return (array_key_exists($input, $rename)) ? $rename[$input] : false;
              });
            }
          }
        }
      }
    }
    return $this;
  }

  /**
    * Add file to process
    *
    * @param Application $app
    * @param string $rename
    * @return void
    */
  public function add(UploadedFile $file, $rename = false){
      $this->files[] = array(
        'file' => $file,
        'name' => (is_string($rename)) ? $rename :  $file->getClientOriginalName(),
      );
      return $this;
  }

  /**
    * Save files
    *
    * @param array $options
    * @return void
    */
  public function save($options = array()){
    $this->prepare($options);
    $filesSaved = array();
    foreach($this->files as $file) {
      $destinationPath = public_path().$this->options['destination'];
			$ext = '.'.$file['file']->getClientOriginalExtension();
			$fileName = str_replace($ext, '', $file['name']);
      $source = $destinationPath.$fileName.$ext;
      //save on server
      $file['file']->move($destinationPath, $fileName.$ext);
      //encrypt
      if($this->options['encrypt']) $this->encrypt($source);
      //save database
      $model = $this->createModel();
      $fileSaved = $model->fill(array(
        'name'       => $fileName.$ext,
        'source'     => $this->options['destination'].$fileName.$ext,
        'encrypted'  => $this->options['encrypt']
      ))->save();
      $status = ($fileSaved) ? 'success' : 'fails';
      $filesSaved[$status][] = $model;
    }
    return $filesSaved;
  }

  public function encrypt($source) {
    return $this->miiFileEncrypt->encrypt($source);
  }

  public function decrypt($source) {
    return $this->miiFileEncrypt->decrypt($source);
  }


  /**
    * Download file
    *
    * @return void
    */
  public function download($file) {
    $model = $this->getModel();
    if($file instanceof $model){
      if($file->encrypted) {
        $contents = $this->decrypt(public_path().'/'.$file->source);
        $response = \Response::make($contents, 200);
        $response->header('Content-type', 'application/octet-stream');
        $response->header('Content-Disposition', 'attachment; filename="'.$file->name);
        return $response;
      }

      return \Response::download(public_path().'/'.$file->source, $file->name);
    }
    throw new \Exception('$file is not an instance of '.$this->model);
  }

  /**
    * Show file
    *
    * @return void
    */
  public function show($file, $attrs = array()) {

  }

  /**
    * Link file
    *
    * @return void
    */
  public function link($file, $title = null, $attributes = array(), $secure = null) {
    $model = $this->getModel();
    if($file instanceof $model){
      $destination = str_replace($file->name , '', $file->source);
      return link_to('files/'.$file->id.'_'.$file->name, $title, $attributes, $secure);
    }
    throw new \Exception('MiiFile misconfigured.');
  }

  /**
    * Check if request target a file
    *
    * @return false or instance of \Illuminate\Database\Eloquent\Model
    */
  public function hasFile($request) {
      if($request instanceof \Illuminate\Http\Request){
        $url = explode('_', $request->path());
        if(isset($url[0]) && $fileId = (int) str_replace('files/', '', $url[0])) {
          $model = $this->createModel();
          return $model->findOrFail($fileId);
        }
        return false;
      }
      throw new \Exception('$request is not an instance of Illuminate\Http\Request');
  }

  /**
    * Prepare options from user
    *
    * @param array $options
    * @return void
    */
  private function prepare($options) {
    $this->options = array_merge($this->options, $options);
    //Do not encrypt if can not encrypt
    $this->options['encrypt'] = $this->miiFileEncrypt->canEncrypt();
    //Check if has an slash at the begining of the destination string
    if( !(strpos('/', $this->options['destination']) === 0))
      $this->options['destination'] = '/'.$this->options['destination'];
    //Check if has an slash at the endf of the destination string
    if( !(strpos('/', $this->options['destination']) === strlen($this->options['destination'])))
      $this->options['destination'] = $this->options['destination'].'/';
  }

  /**
   * Create a new instance of the model.
   *
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function createModel()
  {
    $model = $this->getModel();
    return new $model;
  }

  public function getModel() {
    if(is_null($this->model)) throw new \Exception('MiiFile is misconfigured. Please set the model.');
    if(! class_exists($this->model)) throw new \Exception('Class '.$this->model.' doesn\'t exist.');
    return $this->model;
  }
}

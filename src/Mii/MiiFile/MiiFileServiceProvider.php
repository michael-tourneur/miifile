<?php namespace Mii\MiiFile;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class MiiFileServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('mii/mii-file');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['miiFile'] = $this->app->share(function($app)
		{
			return new MiiFile(
				\App::make('Mii\MiiFile\Interfaces\MiiFileEncryptInteface')
			);
		});

		$this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();
			$loader->alias('MiiFile', 'Mii\MiiFile\Facades\MiiFile');
		});

		$this->app->bind('Mii\MiiFile\Interfaces\MiiFileEncryptInteface', function()
		{
			$key = \Config::get('mii-file::key');
			return new MiiFileMcrypt($key);
		});

		$this->app->before(function($request) {
			if($file = $this->app['miiFile']->hasFile($request)) {
				return $this->app['miiFile']->download($file);
			}
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('miiFile');
	}

}

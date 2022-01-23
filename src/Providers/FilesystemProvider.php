<?php

namespace Infira\Umpy\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\Storage;
use Infira\Umpy\Support\Filesystem\LocalFilesystemAdapter;

class FilesystemProvider extends ServiceProvider
{
	public function register() {}
	
	
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Storage::extend('umpyLocal', function ($app, $config)
		{
			return new LocalFilesystemAdapter($config);
		});
	}
}

<?php

namespace Infira\Umpy\Providers;

use Illuminate\Support\ServiceProvider;
use Infira\Umpy\Foundation\Console\Make\MakeFacade;
use Infira\Umpy\Foundation\Console\Db\{Install, Config, Update};
use Infira\Umpy\Foundation\Console\Make\MakeSupport;
use Infira\Umpy\Foundation\Console\Make\MakeService;

class ConsoleCommandsServiceProvider extends ServiceProvider
{
	/**
	 * The commands to be registered.
	 *
	 * @var array
	 */
	protected $commands = [
		'MakeFacade'      => 'command.make.facade',
		'MakeSupport'     => 'command.make.support',
		'MakeService'     => 'command.make.service',
		'InstallDb'       => 'command.db.install',
		'InstallDbUpdate' => 'command.db.update',
	];
	
	/**
	 * @var \Illuminate\Config\Repository
	 */
	private $config;
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		if (!$this->app->runningInConsole()) {
			return;
		}
		$this->mergeConfigFrom($this->app->configPath('umpy.php'), 'umpy');
		$this->config = $this->app->get('config');
		
		$this->registerCommands($this->commands);
	}
	
	protected function registerCommands(array $commands)
	{
		foreach (array_keys($commands) as $command) {
			$this->{"register{$command}Command"}();
		}
		
		$this->commands(array_values($commands));
	}
	
	protected function registerMakeSupportCommand()
	{
		$this->app->singleton('command.make.support', function ($app)
		{
			return new MakeSupport($app['files']);
		});
	}
	
	protected function registerMakeFacadeCommand()
	{
		$this->app->singleton('command.make.facade', function ($app)
		{
			return new MakeFacade($app['files']);
		});
	}
	
	protected function registerMakeServiceCommand()
	{
		$this->app->singleton('command.make.service', function ($app)
		{
			return new MakeService($app['files']);
		});
	}
	
	protected function registerInstallDbCommand()
	{
		$this->app->singleton('command.db.install', function ($app)
		{
			return new Install(new Config($this->config->get('umpy.console.db')));
		});
	}
	
	protected function registerInstallDbUpdateCommand()
	{
		$this->app->singleton('command.db.update', function ($app)
		{
			return new Update(new Config($this->config->get('umpy.console.db')));
		});
	}
	
	
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}
}

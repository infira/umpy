<?php

namespace Infira\Umpy\console;

use Illuminate\Console\Command;

class InstallDb extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'idb:install {--w|view}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install Db';
	
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		if ($this->option('view')) {
			$this->installViews();
		}
		
		return 0;
	}
	
	private function installViews()
	{
		debug("asdasd");
	}
}

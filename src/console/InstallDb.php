<?php

namespace Infira\Umpy\console;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Infira\Umpy\console\installer\View;
use Infira\Poesis\Connection;
use Infira\Umpy\console\installer\Trigger;

abstract class InstallDb extends \Infira\Umpy\console\Command
{
	/**
	 * @var \Infira\Poesis\Connection
	 */
	public $db;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'idb:install {--w|view} {--t|trigger}';
	
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
	public function handle(): int
	{
		$this->configureUmpyCommand();
		if (!$this->db) {
			$this->error('Db connection is not initialized');
		}
		if ($this->option('view')) {
			$view = $this->installViews(new View($this));
			if ($view !== null) {
				$view->install();
			}
		}
		if ($this->option('trigger')) {
			$trigger = $this->installTriggers(new Trigger($this));
			if ($trigger !== null) {
				$trigger->install();
			}
		}
		
		return CommandAlias::SUCCESS;
	}
	
	protected function setDb(Connection $db)
	{
		$this->db = $db;
	}
	
	protected abstract function configureUmpyCommand();
	
	protected abstract function installViews(View $view): ?View;
	
	protected abstract function installTriggers(Trigger $view): ?Trigger;
}

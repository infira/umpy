<?php

namespace Infira\Umpy\console\db;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Infira\Utils\File;
use Infira\Umpy\console\helper\SqlFileCollector;
use Infira\Utils\Variable;

abstract class Install extends Command
{
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
	
	protected ?SqlFileCollector $triggers;
	protected ?SqlFileCollector $views;
	
	public function handle(): int
	{
		$this->views    = $this->newSqlFileCollector();
		$this->triggers = $this->newSqlFileCollector();
		$this->configureUmpy();
		if (!$this->db) {
			$this->error('Db connection is not initialized');
		}
		if ($this->option('view')) {
			$this->installViews();
		}
		if ($this->option('trigger')) {
			$this->installTriggers();
		}
		
		return CommandAlias::SUCCESS;
	}
	
	private function installViews()
	{
		if (!$this->views->getFiles()) {
			$this->info("No views to install");
			
			return;
		}
		$vars = $this->views->getVariables();
		foreach ($this->views->getFiles() as $fn) {
			if (strtolower(File::getExtension($fn)) == 'php') {
				require_once $fn;
				$func = str_replace(['.sql', '.php'], '', File::getFileNameWithoutExtension($fn));
				if (!function_exists($func)) {
					$this->error('View php file must contain function ' . $func);
				}
				$q = $func();
				if (gettype($q) != 'string') {
					$this->error("View function $func must return string");
				}
				$this->db->complexQuery(Variable::assign($vars, $q));
			}
			else {
				$this->db->fileQuery($fn, $vars);
			}
		}
		$this->info('Views installed');
	}
	
	private function installTriggers()
	{
		if (!$this->triggers->getFiles()) {
			$this->info("No views to install");
			
			return;
		}
		$vars = $this->triggers->getVariables();
		foreach ($this->triggers->getFiles() as $fn) {
			$con     = File::getContent($fn);
			$queries = explode("[TSP]", $con);
			foreach ($queries as $q) {
				$this->db->query(Variable::assign($vars, $q));
			}
			$this->msg('<info>installed trigger: </info>' . $fn);
		}
	}
}

<?php

namespace Infira\Umpy\Foundation\Console\Db;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Infira\Utils\Variable;

class Install extends Command
{
	protected $signature   = 'db:install {--w|view} {--t|trigger}';
	protected $description = 'Install db';
	
	public function runDb(): int
	{
		if ($this->option('view')) {
			$this->installViews();
		}
		if ($this->option('trigger')) {
			$this->installTriggers();
		}
		
		return SymfonyCommand::SUCCESS;
	}
	
	private function installViews()
	{
		$views = $this->collectSqlFiles(...$this->config->getViewsPaths());
		if (!$views) {
			$this->info("No views to install");
			
			return;
		}
		$vars = $this->config->getVariables();
		foreach ($views as $file) {
			if ($file->getExtension() == 'php') {
				require_once $file;
				$func = str_replace(['.sql', '.php'], '', $file->getFilenameWithoutExtension());
				if (!function_exists($func)) {
					$this->error('View php file must contain function ' . $func);
				}
				$q = $func();
				if (gettype($q) != 'string') {
					$this->error("View function $func must return string");
				}
				$this->db->complexQuery(Variable::assign($vars, $q));
				$this->installedMsg($q);
			}
			else {
				$this->db->fileQuery($file, $vars);
				$this->installedMsg($file);
			}
		}
		$this->info('Views installed');
	}
	
	private function installTriggers()
	{
		$triggers = $this->collectSqlFiles(...$this->config->getTriggersPaths());
		if (!$triggers) {
			$this->info("No triggers to install");
			
			return;
		}
		$vars = $this->config->getVariables();
		foreach ($triggers as $file) {
			$con     = $file->getContents();
			$queries = explode("[TSP]", $con);
			foreach ($queries as $q) {
				$this->db->query(Variable::assign($vars, $q));
			}
			$this->msg('<info>installed trigger: </info>' . $file);
		}
	}
	
	private function installedMsg(string $file)
	{
		$this->msg('<fg=#00aaff>Installed view</>: ' . $file);
	}
	
	private function collectSqlFiles(string ...$paths): array
	{
		return $this->collectFiles(['*.sql', '*.sql.php'], true, ...$paths);
	}
}

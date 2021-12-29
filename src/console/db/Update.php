<?php

namespace Infira\Umpy\console\db;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Infira\Umpy\console\helper\SqlFileCollector;
use Infira\Poesis\orm\Model;
use Infira\Utils\File;
use Infira\Utils\Variable;

abstract class Update extends Command
{
	protected                  $signature   = 'idb:update {--r|reset} {--f|flush}';
	protected                  $description = 'Update Db';
	protected SqlFileCollector $updates;
	
	public function handle(): int
	{
		$this->updates = $this->newSqlFileCollector();
		$this->configureUmpy();
		if (!$this->db) {
			$this->error('Db connection is not initialized');
		}
		$this->update();
		
		return CommandAlias::SUCCESS;
	}
	
	private function update()
	{
		$files = $this->updates->getFiles();
		if (!$files) {
			$this->error('No files to update');
			exit;
		}
		
		$vars = $this->updates->getVariables();
		foreach ($files as $file) {
			$this->runSqlFile($file, $vars);
		}
	}
	
	private function runSqlFile(string $file, array $vars)
	{
		$lines = file($file);
		
		// Loop through each line
		$templine = "";
		$queries  = [];
		if ($this->input->getOption('reset') or $this->input->getOption('flush')) {
			$this->getDbModel()->voidLog()->truncate();
			if ($this->input->getOption('flush')) {
				return;
			}
		}
		foreach ($lines as $line) {
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 1) == '#') {
				continue;
			}
			
			
			// Add this line to the current segment
			$templine .= $line;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';') {
				// Perform the query
				$q = trim($templine);
				// Reset temp variable to empty
				$templine = '';
				if (trim($q)) {
					$queries[] = $q;
				}
			}
		}
		$isSystem = 1; //later when CMS is implemented this is needed
		if ($queries) {
			$dbUpdates = $this->getDbModel()->select()->getValueAsKey("hash");
			
			foreach ($queries as $updateNr => $rawQuery) {
				$ok   = true;
				$hash = md5(str_replace(["\n\t", ' '], '', $rawQuery) . $isSystem . $updateNr);
				if (isset($dbUpdates[$hash])) {
					if ($dbUpdates[$hash]["installed"] == 1) {
						$ok = false;
					}
				}
				if ($ok === true) {
					$Db = $this->getDbModel();
					$Db->voidLog();
					$Db->hash($hash);
					$Db->updateNr($updateNr);
					//$Db->isSystem ($isSystem);
					$Db->installed(1);
					$Db->rawQuery($rawQuery);
					$query = Variable::assign($vars, $rawQuery);
					$Db->sqlQuery($query);
					if (str_starts_with($query, "phpScript:")) {
						$ex     = explode(':', $query);
						$script = $this->getPhpScriptLocation($ex[1]);
						if (!$this->input->getOption('reset')) {
							$this->runPhpScript($script);
						}
						$Db->phpScriptFileName($script);
						$Db->phpScript(File::getContent($script));
						$this->msg('<fg=#cc00ff>PHP script</>: ' . $script);
					}
					else {
						if (!$this->input->getOption('reset')) {
							$this->db->query($query);
						}
						$this->msg('<fg=#00aaff>SQL query</>: ' . $query);
					}
					$Db->insert();
				}
			}
		}
		$this->info('Everything is up to date');
	}
	
	private function runPhpScript($script): void
	{
		if (!file_exists($script)) {
			$this->errorExit("phpScript('$script') does not exist");
		}
		require_once $script;
	}
	
	abstract protected function getPhpScriptLocation(string $script): string;
	
	abstract protected function getDbModel(): Model;
}

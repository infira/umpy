<?php

namespace Infira\Umpy\console\db;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Infira\Umpy\console\helper\Collector;
use Infira\Umpy\console\helper\SqlFileCollector;
use Infira\Poesis\orm\Model;

abstract class Update extends Command
{
	protected                  $signature   = 'idb:update';
	protected                  $description = 'Update Db';
	protected SqlFileCollector $updates;
	
	public function handle(): int
	{
		$this->configureUmpy();
		if (!$this->db) {
			$this->error('Db connection is not initialized');
		}
		$this->update();
		
		return CommandAlias::SUCCESS;
	}
	
	private function update()
	{
		$this->updates = $this->newSqlFileCollector();
		$files         = $this->updates->getFiles();
		if (!$files) {
			$this->error('No files to update');
			exit;
		}
		
		foreach ($files as $file) {
			$this->runSqlFile($file);
		}
	}
	
	private function runSqlFile(string $file)
	{
		$lines = file($file);
		
		// Loop through each line
		$templine = "";
		$queries  = [];
		if ($this->input->getOption('reset') or $this->input->getOption('flush')) {
			Db::TSqlUpdates()->voidLog()->truncate();
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
			$dbUpdates = Db::TSqlUpdates()->select()->getValueAsKey("hash");
			
			foreach ($queries as $updateNr => $rawQuery) {
				$ok   = true;
				$hash = md5(str_replace(["\n\t", ' '], '', $rawQuery) . $isSystem . $updateNr);
				if (isset($dbUpdates[$hash])) {
					if ($dbUpdates[$hash]["installed"] == 1) {
						$ok = false;
					}
				}
				$void = false;
				if (substr($rawQuery, 0, 7) == "--void:") {
					$void = true;
				}
				addExtraErrorInfo('hash', $hash);
				addExtraErrorInfo('$rawQuery', $rawQuery);
				if ($ok === true) {
					$Db = Db::TSqlUpdates();
					$Db->voidLog();
					$Db->hash($hash);
					$Db->updateNr($updateNr);
					//$Db->isSystem ($isSystem);
					$Db->installed(1);
					$Db->rawQuery($rawQuery);
					$query = Variable::assign($this->vars, $rawQuery);
					$Db->sqlQuery($query);
					addExtraErrorInfo('$query', $query);
					if (substr($query, 0, 10) == "phpScript:") {
						$fileName   = substr($query, 10, -1);
						$scriptFile = $this->phpScriptPath . $fileName;
						if (!$this->input->getOption('reset')) {
							$this->runPhpScript($scriptFile);
						}
						$Db->phpScriptFileName($scriptFile);
						$Db->phpScript(File::getContent($scriptFile));
						$this->message('<fg=#cc00ff>PHP script</>: ' . $scriptFile);
					}
					else {
						if (!$this->input->getOption('reset')) {
							Db::realQuery($query);
						}
						$this->message('<fg=#00aaff>SQL query</>: ' . $query);
					}
					$Db->insert();
				}
			}
		}
		$this->info('Everything is up to date');
	}
	
	abstract protected function getDbModel():Model;
}

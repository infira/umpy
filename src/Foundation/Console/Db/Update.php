<?php

namespace Infira\Umpy\Foundation\Console\Db;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Finder\SplFileInfo;
use Wolo\Str;

class Update extends Command
{
	protected $signature   = 'db:update {--r|reset} {--f|flush}';
	protected $description = 'Update Db';
	
	public function runDb(): int
	{
		$files = $this->collectFiles(['*.sql'], false, ...$this->config->getUpdatePaths());
		if (!$files) {
			$this->error('No files to update');
			exit;
		}
		
		$vars = $this->config->getVariables();
		foreach ($files as $file) {
			$this->runSqlFile($file, $vars);
		}
		
		return CommandAlias::SUCCESS;
	}
	
	private function runSqlFile(SplFileInfo $file, array $vars)
	{
		$lines = file($file->getRealPath());
		
		// Loop through each line
		$templine = "";
		$queries  = [];
		if ($this->input->getOption('reset') or $this->input->getOption('flush')) {
			$this->config->getUpdatesDbModel()->voidLog()->truncate();
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
			$dbUpdates = $this->config->getUpdatesDbModel()->select()->getValueAsKey("hash");
			
			foreach ($queries as $updateNr => $rawQuery) {
				$ok   = true;
				$hash = md5(str_replace(["\n\t", ' '], '', $rawQuery) . $isSystem . $updateNr);
				if (isset($dbUpdates[$hash])) {
					if ($dbUpdates[$hash]["installed"] == 1) {
						$ok = false;
					}
				}
				if ($ok === true) {
					$Db = $this->config->getUpdatesDbModel();
					$Db->voidLog();
					$Db->hash($hash);
					$Db->updateNr($updateNr);
					//$Db->isSystem ($isSystem);
					$Db->installed(1);
					$Db->rawQuery($rawQuery);
					$query = Str::vars($rawQuery, $vars);
					$Db->sqlQuery($query);
					if (preg_match('/phpScript:(.*);/m', $query, $matches)) {
						$script = $this->getPhpScriptLocation($matches[1], $file->getPath());
						if (!$this->input->getOption('reset')) {
							$this->runPhpScript($script);
						}
						$Db->phpScriptFileName($script->getFilename());
						$Db->phpScript($script->getContents());
						$this->msg('<fg=#cc00ff>PHP script</>: ' . $script->getFilename());
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
	
	private function runPhpScript(SplFileInfo $script): void
	{
		require_once $script->getRealPath();
	}
	
	protected function getPhpScriptLocation(string $script, string $workingPath): SplFileInfo
	{
		if (!str_ends_with($workingPath, '/')) {
			$workingPath .= '/';
		}
		if (!str_starts_with($script, '/')) {
			$script = realpath($workingPath) . '/scripts/' . $script;
		}
		if (!file_exists($script)) {
			$this->errorExit("UpdateScript('$script') does not exists");
		}
		
		return new SplFileInfo($script, $workingPath, basename($workingPath));
	}
}

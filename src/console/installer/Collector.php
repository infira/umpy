<?php

namespace Infira\Umpy\console\installer;

use Infira\Utils\Dir;
use Infira\Umpy\console\InstallDb;
use Infira\Utils\Variable;

class Collector
{
	private $dbFiles   = [];
	private $voidFiles = [];
	private $variables = [];
	
	/**
	 * @var \Infira\Umpy\console\InstallDb
	 */
	protected InstallDb $cmd;
	
	public function __construct(InstallDb $cmd)
	{
		$this->cmd = $cmd;
	}
	
	public function addPath(string $path)
	{
		if (!is_dir($path)) {
			$this->cmd->error("Must be corret path($path)");
			
			return false;
		}
		$this->addFiles([$path]);
	}
	
	public function addFile(string $file)
	{
		if (!file_exists($file)) {
			$this->cmd->error("File($file) does not exists");
			
			return false;
		}
		$this->addFiles([$file]);
	}
	
	public function void(string $file)
	{
		$this->voidFiles[] = $file;
	}
	
	public function addVariable(string $name, $value): void
	{
		$this->variables[$name] = $value;
	}
	
	public function setVariables(array $vars): void
	{
		array_walk($vars, function ($value, $name)
		{
			$this->addVariable($name, $value);
		});
	}
	
	private function addFiles(array $files)
	{
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->addFiles(Dir::getContent($file, ['sql']));
			}
			elseif (is_file($file)) {
				if (!in_array($file, $this->voidFiles)) {
					$realExtension = pathinfo($file)['extension'];
					if ($realExtension == 'sql') {
						$this->dbFiles[] = $file;
					}
					else {
						$ex  = explode('.', $file);
						$ext = strtolower(join('.', array_slice($ex, -2)));
						if ($ext == 'sql.php') {
							$this->dbFiles[] = $file;
						}
					}
				}
			}
			else {
				$this->cmd->error('File is not file or path(' . $file . ') not found');
			}
		}
	}
	
	protected function getFiles(): array
	{
		return $this->dbFiles;
	}
	
	protected function getVariables(): array
	{
		return $this->variables;
	}
	
	
	protected function parseQuery(string $query)
	{
		return Variable::assign($this->getVariables(), $query);
	}
	
}

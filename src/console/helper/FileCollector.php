<?php

namespace Infira\Umpy\console\helper;

use Infira\Umpy\console\Command;

abstract class FileCollector
{
	protected array $dbFiles   = [];
	protected array $voidFiles = [];
	protected array $variables = [];
	
	/**
	 * @var Command
	 */
	protected Command $cmd;
	
	public function __construct(Command $cmd)
	{
		$this->cmd = $cmd;
	}
	
	public function addPath(string $path)
	{
		if (!is_dir($path)) {
			$this->cmd->getOutput()->error("Must be corret path($path)");
			
			return false;
		}
		$this->addFiles([$path]);
	}
	
	public function addFile(string $file)
	{
		if (!file_exists($file)) {
			$this->cmd->getOutput()->error("File($file) does not exists");
			
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
	
	public function getFiles(): array
	{
		return $this->dbFiles;
	}
	
	public function getVariables(): array
	{
		return $this->variables;
	}
	
	abstract public function addFiles(array $files);
}

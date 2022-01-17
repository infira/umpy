<?php

namespace Infira\Umpy\Foundation\Console\Make;

use Symfony\Component\Console\Input\InputOption;

class MakeFacade extends GeneratorCommand
{
	protected $name        = 'make:facade';
	protected $description = 'Create new facade class';
	protected $type        = 'Facade';
	
	protected function getStubName(): string
	{
		return 'facade';
	}
	
	protected function getDefaultNamespace($rootNamespace): string
	{
		return $rootNamespace . '\Facades';
	}
	
	protected function getReplacements(): array
	{
		return ['{{ accessor }}' => $this->option('accessor') ?: strtolower($this->getNameInput())];
	}
	
	protected function getOptions(): array
	{
		return [
			['accessor', 'a', InputOption::VALUE_OPTIONAL, 'Facade accessor'],
		];
	}
}
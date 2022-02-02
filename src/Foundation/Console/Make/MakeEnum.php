<?php

namespace Infira\Umpy\Foundation\Console\Make;

class MakeEnum extends GeneratorCommand
{
	protected $name        = 'make:enum';
	protected $description = 'Create new enum class';
	protected $type        = 'Enum';
	
	protected function getStubName(): string
	{
		return 'enum';
	}
	
	protected function getDefaultNamespace($rootNamespace): string
	{
		return $rootNamespace . '\Enum';
	}
}
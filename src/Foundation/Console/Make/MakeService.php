<?php

namespace Infira\Umpy\Foundation\Console\Make;

class MakeService extends GeneratorCommand
{
	protected $name        = 'make:service';
	protected $description = 'Create new service class';
	protected $type        = 'Service';
	
	protected function getStubName(): string
	{
		return 'service';
	}
	
	protected function getDefaultNamespace($rootNamespace): string
	{
		return $rootNamespace . '\Services';
	}
}
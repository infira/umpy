<?php

namespace Infira\Umpy\Foundation\Console\Make;

class MakeSupport extends GeneratorCommand
{
	protected $name        = 'make:support';
	protected $description = 'Create new support class';
	protected $type        = 'Support';
	
	protected function getStubName(): string
	{
		return 'support';
	}
	
	protected function getDefaultNamespace($rootNamespace): string
	{
		return $rootNamespace . '\Support';
	}
}
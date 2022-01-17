<?php

namespace Infira\Umpy\Foundation\Console\Make;

abstract class GeneratorCommand extends \Illuminate\Console\GeneratorCommand
{
	protected function getStub(): string
	{
		return __DIR__ . '/stubs/' . $this->getStubName() . '.stub';
	}
	
	abstract protected function getStubName(): string;
	
	protected function buildClass($name): string
	{
		return $this->replaceVariables(parent::buildClass($name));
	}
	
	private function replaceVariables(string $stub): string
	{
		$replace = $this->getReplacements();
		if (!$replace) {
			return $stub;
		}
		
		return str_replace(
			array_keys($replace), array_values($replace), $stub
		);
	}
	
	protected function getReplacements(): array
	{
		return [];
	}
}
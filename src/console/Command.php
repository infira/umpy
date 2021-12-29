<?php

namespace Infira\Umpy\console;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Infira\console\ConsoleOutput;

/**
 * @mixin ConsoleOutput
 */
abstract class Command extends \Illuminate\Console\Command
{
	public function run(InputInterface $input, OutputInterface $output)
	{
		$output = new ConsoleOutput($input);
		parent::run($input, $output);
	}
	
	public function __call($method, $parameters)
	{
		if (method_exists($this->output, $method)) {
			return $this->output->$method(...$parameters);
		}
		$this->output->error("Unknown method('$method')");
	}
	
	public function error($string)
	{
		$this->output->error($string);
		exit;
	}
	
	protected function success(): int
	{
		return CommandAlias::SUCCESS;
	}
	
	protected abstract function configureUmpy();
}

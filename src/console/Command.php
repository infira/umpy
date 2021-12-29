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
		$output = $this->getOutput();
		if (method_exists($output, $method)) {
			return $output->$method(...$parameters);
		}
		$output->error("Unknown method('$method')");
	}
	
	protected function success(): int
	{
		return CommandAlias::SUCCESS;
	}
	
	public function getOutput(): ConsoleOutput
	{
		return $this->output->getOutput();
	}
	
	public function errorExit(string $msg): void
	{
		$this->getOutput()->error($msg);
		exit;
	}
	
	protected abstract function configureUmpy();
}

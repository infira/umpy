<?php

namespace Infira\Umpy\Foundation\Console;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Infira\console\ConsoleOutput;
use Illuminate\Config\Repository;

/**
 * @mixin ConsoleOutput
 */
abstract class Command extends \Illuminate\Console\Command
{
	/**
	 * @var \Illuminate\Config\Repository
	 */
	protected Repository $config;
	
	/**
	 * @param \Illuminate\Config\Repository $config
	 */
	public function __construct(Repository $config)
	{
		$this->config = $config;
		parent::__construct();
	}
	
	public function run(InputInterface $input, OutputInterface $output)
	{
		$output = new ConsoleOutput($input);
		
		return parent::run($input, $output);
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
	
	public function handle(): int
	{
		$this->configureUmpy();
		
		return $this->runUmpy();
	}
	
	protected abstract function configureUmpy();
	
	protected abstract function runUmpy();
}
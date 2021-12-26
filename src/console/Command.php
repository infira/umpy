<?php

namespace Infira\Umpy\console;

use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

abstract class Command extends \Illuminate\Console\Command
{
	protected function message(string $msg)
	{
		$this->output->writeln($msg);
	}
	
	protected function blink($msg)
	{
		$outputStyle = new OutputFormatterStyle('red', '#ff0', ['bold', 'blink']);
		$this->output->getFormatter()->setStyle('fire', $outputStyle);
		$this->output->writeln("<fire>$msg</>");
	}
	
	protected function success(): int
	{
		return CommandAlias::SUCCESS;
	}
}

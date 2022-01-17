<?php

namespace Infira\Umpy\Foundation\Console\Db;

use Infira\Poesis\Connection;
use Symfony\Component\Finder\Finder;

/**
 * @property Config $config;
 */
abstract class Command extends \Infira\Umpy\Foundation\Console\Command
{
	/**
	 * @var \Infira\Poesis\Connection
	 */
	public Connection $db;
	
	protected function configureUmpy()
	{
		$this->db = $this->config->getDb();
		if (!$this->db) {
			$this->errorExit('db not defined');
		}
	}
	
	protected function runUmpy(): int
	{
		return $this->runDb();
	}
	
	protected abstract function runDb();
	
	
	/**
	 * You can use patterns (delimited with / sign), globs or simple strings.
	 *
	 *     $finder->name('*.php')
	 *     $finder->name('/\.php$/') // same as above
	 *     $finder->name('test.php')
	 *     $finder->name(['test.py', 'test.php'])
	 *
	 * @param string|string[] $namePatterns A pattern (a regexp, a glob, or a string) or an array of patterns
	 * @param string          ...$paths
	 * @return \Symfony\Component\Finder\SplFileInfo[]
	 */
	protected function collectFiles(array $namePatterns = null, bool $recursive = false, string ...$paths): array
	{
		$into   = [];
		$finder = (new Finder())->in($paths);
		if ($namePatterns) {
			$finder = $finder->name($namePatterns);
		}
		foreach ($finder as $file) {
			if ($file->isDir()) {
				if ($recursive) {
					return $this->collectFiles($namePatterns, $recursive, $file->getPath());
				}
			}
			else {
				$realExtension = $file->getExtension();
				if ($realExtension == 'sql') {
					$into[] = $file;
				}
				else {
					$ex  = explode('.', $file->getFilename());
					$ext = strtolower(join('.', array_slice($ex, -2)));
					if ($ext == 'sql.php') {
						$into[] = $file;
					}
				}
			}
		}
		
		return $into;
	}
}

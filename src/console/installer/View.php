<?php

namespace Infira\Umpy\console\installer;

use Infira\Utils\File;

class View extends Collector
{
	public function install()
	{
		foreach ($this->getFiles() as $fn) {
			if (strtolower(File::getExtension($fn)) == 'php') {
				require_once $fn;
				$func = str_replace(['.sql', '.php'], '', File::getFileNameWithoutExtension($fn));
				if (!function_exists($func)) {
					$this->cmd->error('View php file must contain function ' . $func);
				}
				$q = $func();
				if (gettype($q) != 'string') {
					$this->cmd->error("View function $func must return string");
				}
				$this->cmd->db->complexQuery($this->parseQuery($q));
			}
			else {
				$this->cmd->db->fileQuery($fn, $this->getVariables());
			}
		}
	}
}

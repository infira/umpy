<?php

namespace Infira\Umpy\console\installer;

use Infira\Utils\File;

class Trigger extends Collector
{
	public function install()
	{
		foreach ($this->getFiles() as $fn) {
			$con     = File::getContent($fn);
			$queries = explode("[TSP]", $con);
			foreach ($queries as $q) {
				$this->cmd->db::realQuery($this->parseQuery($q));
			}
			$this->cmd->message('<info>installed trigger: </info>' . $fn);
		}
	}
}

<?php

namespace Infira\Umpy\console\db;

use Infira\Poesis\Connection;
use Infira\Umpy\console\helper\SqlFileCollector;

abstract class Command extends \Infira\Umpy\console\Command
{
	/**
	 * @var \Infira\Poesis\Connection
	 */
	public Connection $db;
	
	protected function setDb(Connection $db)
	{
		$this->db = $db;
	}
	
	protected function newSqlFileCollector(): SqlFileCollector
	{
		return new SqlFileCollector($this);
	}
}

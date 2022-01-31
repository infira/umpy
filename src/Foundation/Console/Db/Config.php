<?php

namespace Infira\Umpy\Foundation\Console\Db;

use Illuminate\Config\Repository;
use Infira\Poesis\Connection;
use Infira\Umpy\Support\Path;
use Infira\Poesis\Model;

class Config extends Repository
{
	public function getDb(): ?Connection
	{
		$db = $this->get('db');
		if ($db) {
			return $db();
		}
		
		return null;
	}
	
	public function getVariables(): array
	{
		return $this->get('variables', []);
	}
	
	public function getViewsPaths(): array
	{
		return $this->get('views', [Path::dbViews()]);
	}
	
	public function getTriggersPaths(): array
	{
		return $this->get('triggers', [Path::dbTriggers()]);
	}
	
	public function getUpdatePaths(): array
	{
		return $this->get('updates', [Path::dbUpdates()]);
	}
	
	public function getUpdatesDbModel(): ?Model
	{
		return $this->get('updatesModel', null);
	}
}
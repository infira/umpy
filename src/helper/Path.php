<?php

namespace Infira\Umpy\helper;

use Infira\Utils\Dir;

class Path
{
	protected static function root(string $rootPath, string $file): string
	{
		return Dir::fixPath(base_path($rootPath)) . $file;
	}
	
	public static function dbViews(string $path = ''): string
	{
		return self::root('database/views', $path);
	}
	
	public static function dbTriggers(string $path = ''): string
	{
		return self::root('database/triggers', $path);
	}
}
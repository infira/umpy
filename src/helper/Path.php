<?php

namespace Infira\Umpy\helper;

use Infira\Utils\Dir;

class Path
{
	protected static function rootPath(string $rootPath, string $file): string
	{
		return Dir::fixPath(base_path($rootPath)) . $file;
	}
	
	public static function root(string $path = ''): string
	{
		return self::rootPath('', $path);
	}
	
	public static function dbViews(string $path = ''): string
	{
		return self::rootPath('database/views', $path);
	}
	
	public static function dbTriggers(string $path = ''): string
	{
		return self::rootPath('database/triggers', $path);
	}
	
	public static function dbUpdates(string $path = ''): string
	{
		return self::rootPath('database/updates', $path);
	}
}
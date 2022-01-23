<?php

namespace Infira\Umpy\Support;

class Str extends \Illuminate\Support\Str
{
	public static function classBasename(string $class): string
	{
		$ex = explode("\\", $class);
		
		return end($ex);
	}
}
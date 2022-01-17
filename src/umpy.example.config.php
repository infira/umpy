<?php

use App\Support\Db;

return [
	'console' => [
		'db' => [
			'db'           => function (): \Infira\Poesis\Connection
			{
				//
			},
			'views'        => ['path to views folder'],
			'triggers'     => ['path to views folder'],
			'updates'      => ['path to updates folder'],
			'variables'    => [
				'fileVar' => 'var value',
			],
			'updatesModel' => Db::SqlUpdates(),
		],
	],
];

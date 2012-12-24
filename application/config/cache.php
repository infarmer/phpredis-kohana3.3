<?php defined('SYSPATH') or die('No direct script access.');
return array
(
	'redis' => array(
		'driver'             => 'redis',
		'port'               => 6379,
		'host'               => 'localhost',
		'db_num'             => 0,
		'igbinary_serialize' => true,
	),
);

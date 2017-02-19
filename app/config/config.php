<?php

return new \Phalcon\Config([
	'env' => 'develop', // production or develop
	'version' => require(__DIR__ . '/version.php'),
	'database' => [
		'production' => [
			'database01' => [
				'host' => '192.168.1.10:27017,192.168.1.11:27017',
				'dbname' => 'database01',
				'username' => 'dbuser01',
				'password' => 'dbpassword01',
				'replicaSet' => 'replicaSetName',
				'uriOptions' => [
					'readPreference' => 'secondaryPreferred',
				],
			],
			'database02' => [
				'host' => '192.168.1.10:27017,192.168.1.11:27017',
				'dbname' => 'database02',
				'username' => 'dbuser02',
				'password' => 'dbpassword02',
				'replicaSet' => 'replicaSetName',
				'uriOptions' => [
					'readPreference' => 'secondaryPreferred',
				],
			],
		],
		'develop' => [
			'database01' => [
				'host' => '192.168.1.10:27017,192.168.1.11:27017',
				'dbname' => 'dev_database01',
				'username' => 'dbuser01',
				'password' => 'dbpassword01',
				'replicaSet' => 'replicaSetName',
				'uriOptions' => [
					'readPreference' => 'secondaryPreferred',
				],
			],
			'database02' => [
				'host' => '192.168.1.10:27017,192.168.1.11:27017',
				'dbname' => 'dev_database02',
				'username' => 'dbuser02',
				'password' => 'dbpassword02',
				'replicaSet' => 'replicaSetName',
				'uriOptions' => [
					'readPreference' => 'secondaryPreferred',
				],
			],
		],
	],
	'application' => [
		'controllersDir' => __DIR__ . '/../controllers/',
		'modelsDir'      => __DIR__ . '/../models/',
		'viewsDir'       => __DIR__ . '/../views/',
		'pluginsDir'     => __DIR__ . '/../plugins/',
		'libraryDir'     => __DIR__ . '/../library/',
		'cacheDir'       => __DIR__ . '/../cache/',
		'vendorDir'      => __DIR__ . '/../vendor/',
		'dataDir'        => __DIR__ . '/../data/',
		'baseUri'        => '/',
	],
	'session' => [
		'production' => [
			'sessionDir' => __DIR__ . '/../data/session/',
			'name' => 'sid_xxxx',
			'lifetime' => 3600 * 2,
			'path' => '/',
			'domain' => null,
			'secure' => true,
		],
		'develop' => [
			'sessionDir' => __DIR__ . '/../data/session/',
			'name' => 'sid_xxxx',
			'lifetime' => 3600 * 2,
			'path' => '/',
			'domain' => null,
			'secure' => false,
		],
	],
]);


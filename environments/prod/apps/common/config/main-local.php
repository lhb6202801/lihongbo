<?php
return [
    'components' => [
        'db_schema_cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => ROOT_DIR.'/runtimes/common/cache/db_schema'
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=reb',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 300,
            'schemaCache' => 'db_schema_cache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'api cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'class' => 'yii\redis\Connection',
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 1,
            ]
        ],
        'mongodb' => [
            'class' => 'vistart\mongodb\Connection',
            'dsn' => 'mongodb://localhost:27017/location',
        ],
    ],
];

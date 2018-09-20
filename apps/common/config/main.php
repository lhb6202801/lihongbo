<?php
return [
    'timeZone'=>'Asia/Shanghai',
    'vendorPath' => VENDOR_DIR,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => ROOT_DIR.'/runtimes/common/cache/default'
        ],
        'setting' => [
            'class' => 'wh\setting\Component'
        ],
    ],
];

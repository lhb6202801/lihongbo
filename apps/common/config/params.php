<?php
return [
    'adminEmail' => 'vadmin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    //七牛空间
    'qiniu' => [
        'image' => [
            'url' => 'http://cdn.hbw365.cn/',
            //'url' => 'http://offmda8sp.bkt.clouddn.com',
            'access_key' => 'xM-ql2OecyqNcrsxzl3mFFEZLoVWcM3ejms4z3MD',
            'secret_key' => 'DJF3xNXbIWMDDlvv2P4FrzO3-cN_uYR0zM8lwoMr',
            'bucket' => 'image'
        ],
        'message' => [
            //'url' => 'http://7xrhs6.com1.z0.glb.clouddn.com/',
            'url' => 'http://oojfkljer.bkt.clouddn.com',
            'access_key' => 'u8xZL9WHP-mI6z5ZBlejfI2Wepg2ZX9sbR0pWk9V',
            'secret_key' => '9unHAYSQwgVq_B5iwda18hb1xd7TAqH5ndI6_8fv',
            'bucket' => 'message'
        ],
    ],
    //容联云通讯,短信接口配置
    'ytx' => [
        //主帐号
        'accountSid' => '8a216da85d7dbf78015da06229b70b36',
        //主帐号Token
        'accountToken' => '0ca94e0e4230408baf9ee5c82287de24',
        //应用Id
        'appId' => '8a216da85d7dbf78015da0622a070b3b',
        //请求地址，格式如下，不需要写https://
        'serverIP' => 'app.cloopen.com',
        //'serverIP' => 'sandboxapp.cloopen.com',
        //请求端口
        'serverPort' => '8883',
        //REST版本号
        'softVersion' => '2013-12-26' //28010
    ],
];

<?php
return [
    'adminEmail' => 'vadmin@example.com',
//    'SocketIO'=>'http://www.hbw365.cn:2121',
    'wxQrcode'=>'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=',
//    'exhost' => 'http://mis.hbw365.cn/',
    'wx'=>[
        //  公众号信息
        'mp'=>[
//            /**
//             * 账号基本信息，请从微信公众平台
//             */
            'app_id'  => '',         // AppID
            'secret'  => '',     // AppSecret
            'token'   => '',          // Token
            'encodingAESKey'=>'',// 消息加解密密钥,该选项需要和公众号后台设置保持一直
            'safeMode'=>0,//0-明文 1-兼容 2-安全，该选项需要和公众号后台设置保持一直

            'payment'=>[
                'mch_id'        =>  '',
                'key'           =>  '',
                'notify_url'    =>  '',
                'cert_path'     => '', // XXX: 绝对路径！！！！
                'key_path'      => '',      // XXX: 绝对路径！！！！
            ],

            'oauth' => [
                'scopes'   => 'snsapi_userinfo',
                'callback' => '',
            ],
        ],
        'mini'=>[
            'app_id'  => 'wxdc26e3b90724281d',
            'secret'  => '5a0741bad7d93c1fec14e9254c3f4753',
            'payment' => [
                'mch_id'        => '',
                'key'           => ''
            ],
        ]
    ],
    //容联云通讯,短信接口配置
//    'ytx' => [
//        //主帐号
//        'accountSid' => '8a216da85d7dbf78015da06229b70b36',
//        //主帐号Token
//        'accountToken' => '0ca94e0e4230408baf9ee5c82287de24',
//        //应用Id
//        'appId' => '8a216da85d7dbf78015da0622a070b3b',
//        //请求地址，格式如下，不需要写https://
//        'serverIP' => 'app.cloopen.com',
//        //'serverIP' => 'sandboxapp.cloopen.com',
//        //请求端口
//        'serverPort' => '8883',
//        //REST版本号
//        'softVersion' => '2013-12-26' //28010
//    ]
];

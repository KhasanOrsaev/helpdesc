<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Europe/Moscow',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'J0njvXWypsTk0VyVzTg4roW0CSxzEX8P',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'language' => 'ru-RU',
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => '192.168.0.202',
                //'username' => 'Portal',
                //'password' => 'QWEasd234',
                'port' => '25',
                'encryption' => null,
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                '/<action:[\w-]+>/<id:[\d\w]+>' => 'site/<action>',
                '/<action:[\w-]+>' => 'site/<action>',
                '/<controller:\w+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>'
            ],
        ],
        'ldap' => [
            'class'=>'Edvlerblog\Ldap',
            'options'=> [
                'ad_port'      => 389,
                'domain_controllers'    => array('192.168.0.99'),
                'account_suffix' =>  '@int',
                'base_dn' => 'dc=int,dc=nacpp,dc=ru',
                // for basic functionality this could be a standard, non privileged domain user (required)
                'admin_username' => 'Portal',
                'admin_password' => 'QWEasd234'
            ],
            //Connect on Adldap instance creation (default). If you don't want to set password via main.php you can
            //set autoConnect => false and set the admin_username and admin_password with
            //\Yii::$app->ldap->connect('admin_username', 'admin_password');
            //See function connect() in https://github.com/Adldap2/Adldap2/blob/v5.2/src/Adldap.php

            'autoConnect' => true
        ]

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '192.168.0.143', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '192.168.0.143', '::1'],
    ];
}

return $config;

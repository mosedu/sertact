<?php

/*
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '-JL7PpZ3CQpNWYc4uzWn_EI9t8s7NwlJ',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
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
            'useFileTransport' => true,
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
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
*/
use yii\helpers\ArrayHelper;

$sfCommon = __DIR__ . DIRECTORY_SEPARATOR . 'common.php';
$sfCommonLoc = __DIR__ . DIRECTORY_SEPARATOR . 'common-local.php';
$sfWebLoc = __DIR__ . DIRECTORY_SEPARATOR . 'web-local.php';

$webConfig = [
    'id' => 'messages.tdd',
    'language' => 'ru',
    'components' => [
/*
        'view' => [
            'theme' => [
                'pathMap' => [
//                    '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
                    '@app/views' => '@app/themes/adminlte'
                ],
            ],
        ],
*/
        'request' => [
            'cookieValidationKey' => 'Ep62ru14m6DFsOPqqE1cxq9D8NZOteUR',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
        ],
    ],
];

return ArrayHelper::merge(
    require($sfCommon),
    file_exists($sfCommonLoc) ? require($sfCommonLoc) : [],
    $webConfig,
    file_exists($sfWebLoc) ? require($sfWebLoc) : []
);
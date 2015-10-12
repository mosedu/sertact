<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 01.10.2015
 * Time: 11:36
 */

use yii\helpers\ArrayHelper;

$sfParams = __DIR__ . DIRECTORY_SEPARATOR . 'params-local.php';
$aLocalParams = file_exists($sfParams) ? require($sfParams) : [];

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    $aLocalParams
);

$config = [
    'basePath' => dirname(__DIR__),
    'name' => 'Выдача сертификатов',
    'bootstrap' => ['log'],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => array_keys($aLocalParams['user.groups']),
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'cache' => false,
            'rules' => [
                '<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
                '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                '<_c:[\w\-]+>' => '<_c>/index',
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/views/mail',
            'htmlLayout' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'log' => [
            'class' => 'yii\log\Dispatcher',
        ],
    ],
    'params' => $params,
];

return $config;
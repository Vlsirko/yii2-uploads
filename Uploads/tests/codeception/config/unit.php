<?php
return [
    'id' => 'app-console',
    'class' => 'yii\web\Application',
    'basePath' => \Yii::getAlias('@tests'),
    'runtimePath' => \Yii::getAlias('@tests/_output'),
    'bootstrap' => [],
    'components' => [
        'db' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=test.db',
            'username' => 'root',
            'password' => '',
        ]
    ]
];
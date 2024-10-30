<?php
return [
    'textDomain' => 'luckywp-wiki-linking',
    'bootstrap' => [
        'activation',
        [\luckywp\wikiLinking\plugin\Plugin::className(), 'checkDb'],
        'admin',
        'front',
    ],
    'pluginsLoadedBootstrap' => [
        'settings',
    ],
    'components' => [
        'activation' => \luckywp\wikiLinking\plugin\Activation::className(),
        'settings' => [
            'class' => \luckywp\wikiLinking\plugin\Settings::className(),
            'initGroupsConfigFile' => __DIR__ . '/settings.php',
        ],
        'options' => \luckywp\wikiLinking\core\wp\Options::className(),
        'db' => \luckywp\wikiLinking\plugin\Db::className(),
        'admin' => \luckywp\wikiLinking\admin\Admin::className(),
        'front' => \luckywp\wikiLinking\front\Front::className(),
        'request' => \luckywp\wikiLinking\core\base\Request::className(),
        'view' => \luckywp\wikiLinking\core\base\View::className(),
        'items' => \luckywp\wikiLinking\plugin\repositories\ItemRepository::className(),
        'posts' => \luckywp\wikiLinking\plugin\repositories\PostRepository::className(),
    ],
];

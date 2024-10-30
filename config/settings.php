<?php

use luckywp\wikiLinking\admin\helpers\AdminHtml;
use luckywp\wikiLinking\admin\widgets\accessRoles\AccessRoles;
use luckywp\wikiLinking\admin\widgets\PremiumBadge;
use luckywp\wikiLinking\admin\wp\PremiumSettings;

return [

    // Основная группа
    'general' => [
        'label' => esc_html__('General', 'luckywp-wiki-linking'),
        'sections' => [

            // Основная секция
            'general' => [
                'title' => esc_html__('General', 'luckywp-wiki-linking'),
                'fields' => [
                    'posts_per_item' => [
                        'label' => esc_html__('Number of links to keyword phrase', 'luckywp-wiki-linking'),
                        'widget' => 'textInput',
                        'params' => [
                            'inputOptions' => [
                                'size' => AdminHtml::TEXT_INPUT_SIZE_SMALL,
                            ],
                        ],
                        'sanitizeCallback' => function ($value) {
                            $value = (int)$value;
                            if ($value < 1) {
                                $value = 10;
                            }
                            return $value;
                        },
                        'default' => 10,
                    ],
                ],
            ],

            // Количество ссылок на пост
            'items_per_post' => [
                'title' => esc_html__('Number of links in post', 'luckywp-wiki-linking'),
            ],

            // Связка ключевых фраз и постов
            'binding' => [
                'title' => function () {
                    return '<span class="lwpwlColorMuted">' . esc_html__('Automatic bunch of keyword phrases with posts to place the links', 'luckywp-wiki-linking') . '</span> ' . PremiumBadge::widget();
                },
                'fields' => [
                    'bind_on_published_post_save' => [
                        'label' => '<span class="lwpwlColorMuted">' . esc_html__('When publishing/changing the post', 'luckywp-wiki-linking') . '</span>',
                        'default' => true,
                        'widget' => [PremiumSettings::className(), 'checkbox'],
                        'params' => [
                            'checkboxOptions' => [
                                'label' => esc_html__('search for keyword phrases to place the link in this post', 'luckywp-wiki-linking')
                            ],
                        ],
                    ],
                    'bind_on_item_anchor_save' => [
                        'label' => '<span class="lwpwlColorMuted">' . esc_html__('When creating/changing keyword phrase', 'luckywp-wiki-linking') . '</span>',
                        'default' => true,
                        'widget' => [PremiumSettings::className(), 'checkbox'],
                        'params' => [
                            'checkboxOptions' => [
                                'label' => esc_html__('search for posts to place the link by keyword phrase', 'luckywp-wiki-linking'),
                            ],
                        ],
                    ],
                ],
            ],

            'access' => [
                'title' => function () {
                    return '<span class="lwpwlColorMuted">' . esc_html__('Access settings', 'luckywp-wiki-linking') . '</span> ' . PremiumBadge::widget();
                },
                'fields' => [
                    'access_roles' => [
                        'label' => '<span class="lwpwlColorMuted">' . esc_html__('Full access to the linking management', 'luckywp-wiki-linking') . '</span>',
                        'widget' => function ($field) {
                            echo AccessRoles::widget([
                                'field' => $field,
                                'fake' => true,
                            ]);
                        },
                        'default' => ['administrator'],
                    ]
                ],
            ],
        ],
    ],
];

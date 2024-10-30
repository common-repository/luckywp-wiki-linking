<?php

namespace luckywp\wikiLinking\plugin\entities;

class ItemType
{
    const CUSTOM = 1;
    const POST = 2;

    public static function toIds()
    {
        return [static::CUSTOM, static::POST];
    }

    public static function toList()
    {
        return [
            static::CUSTOM => esc_html__('Custom link', 'luckywp-wiki-linking'),
            static::POST => esc_html__('Post', 'luckywp-wiki-linking'),
        ];
    }
}

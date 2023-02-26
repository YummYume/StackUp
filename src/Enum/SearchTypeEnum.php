<?php

namespace App\Enum;

use App\Enum\Traits\UtilsTrait;

enum SearchTypeEnum: string
{
    use UtilsTrait;

    case Techs = 'techs';
    case Stacks = 'stacks';
    case Profiles = 'profiles';

    public static function getSearchOptions(self $type): array
    {
        return match ($type) {
            self::Techs => [
                'attributesToRetrieve' => ['name', 'slug', 'description', 'picture'],
                'attributesToHighlight' => ['name', 'description'],
                'attributesToCrop' => ['description'],
                'cropMarker' => '...',
            ],
            self::Stacks => [
                'attributesToRetrieve' => ['name', 'slug', 'description', 'profileSlug'],
                'attributesToHighlight' => ['name', 'description'],
                'attributesToCrop' => ['description'],
                'cropMarker' => '...',
            ],
            self::Profiles => [
                'attributesToRetrieve' => ['username', 'slug', 'description', 'picture'],
                'attributesToHighlight' => ['username', 'description'],
                'attributesToCrop' => ['description'],
                'cropMarker' => '...',
            ],
            default => [],
        };
    }
}

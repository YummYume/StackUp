<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('shortList')]
final class ShortListComponent
{
    public array $items = [];

    public ?string $itemFileProperty = null;

    public string $itemNameProperty = 'name';

    public ?string $itemPictureProperty = null;

    public ?string $link = null;

    public ?string $moreText = '';
}

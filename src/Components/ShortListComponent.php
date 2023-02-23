<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('shortList')]
final class ShortListComponent
{
    public ?string $link = null;

    public array $items = [];

    public string $itemNameProperty = 'name';

    public ?string $itemPictureProperty = null;

    public ?string $itemFileProperty = null;
}

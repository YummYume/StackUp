<?php

namespace App\Components;

use App\Entity\Category;
use App\Entity\Stack;
use App\Entity\Tech;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('listItem')]
final class ListItemComponent
{
    public Tech|Stack|Category $item;

    public ?int $index = null;

    public string $name = '';

    public ?string $itemFileProperty = '';

    public ?string $itemNameProperty = '';

    public ?string $itemPictureProperty = '';
}

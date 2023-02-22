<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('shortList')]
final class ShortListComponent
{
    public string $link = '';
    public array $items = [];
}

<?php

namespace App\Components;

use App\Entity\Tech;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('categoryCard')]
final class CategoryCardComponent
{
    /** @var Tech[] */
    public array $techs = [];

    public ?string $name = null;
}

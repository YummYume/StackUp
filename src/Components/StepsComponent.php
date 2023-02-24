<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('steps')]
final class StepsComponent
{
    public int $steps = 1;

    public int $step = 1;
}

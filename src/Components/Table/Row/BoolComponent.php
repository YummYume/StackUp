<?php

namespace App\Components\Table\Row;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('bool', 'components/table/row/bool.html.twig')]
final class BoolComponent
{
    public ?array $config = null;
}

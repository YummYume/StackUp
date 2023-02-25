<?php

namespace App\Components\Table\Row;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('count', 'components/table/row/count.html.twig')]
final class CountComponent
{
    public ?array $config = null;
}

<?php

namespace App\Components\Table\Row;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('date', 'components/table/row/date.html.twig')]
final class DateComponent
{
    public ?array $config = null;
}

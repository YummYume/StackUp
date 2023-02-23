<?php

namespace App\Components;

use App\Entity\Category;
use App\Entity\Stack;
use App\Entity\Tech;
use App\Entity\TechPicture;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent('listItem')]
final class ListItemComponent
{
    public Category|Stack|Tech $item;

    public ?int $index = null;

    #[ExposeInTemplate]
    private string $name;

    #[ExposeInTemplate]
    private ?string $itemFileProperty = null;

    #[ExposeInTemplate]
    private ?TechPicture $picture = null;

    #[PostMount]
    public function postMount(): void
    {
        $this->name = $this->item->getName();

        if ($this->item instanceof Tech) {
            $this->picture = $this->item->getPicture();
            $this->itemFileProperty = 'file';
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItemFileProperty(): ?string
    {
        return $this->itemFileProperty;
    }

    public function getPicture(): ?TechPicture
    {
        return $this->picture;
    }
}

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

    #[ExposeInTemplate]
    private ?string $route;

    #[ExposeInTemplate]
    private ?array $slug = [];

    #[PostMount]
    public function postMount(): void
    {
        $this->name = $this->item->getName();

        if ($this->item instanceof Tech) {
            $this->picture = $this->item->getPicture();
            $this->itemFileProperty = 'file';
        }

        if ($this->item instanceof Stack) {
            $this->route = 'app_stack_show';
            $this->slug = [
                'slug_stack' => $this->item->getSlug(),
                'slug_profile' => $this->item->getProfile()->getSlug(),
            ];
        } elseif ($this->item instanceof Tech) {
            $this->route = 'app_tech_show';
            $this->slug = [
                'slug' => $this->item->getSlug(),
            ];
        } else {
            $this->route = null;
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

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getSlug(): array
    {
        return $this->slug;
    }
}

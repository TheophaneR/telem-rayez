<?php

namespace App\Twig;

use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('dateFr', [$this, 'dateInFrenchFormat'])
        ];
    }

    public function formatPrice(int $priceInCents, int $decimals = 2): string
    {
        $formattedPrice = number_format($priceInCents / 100, $decimals);

        return $formattedPrice . ' €';
    }

    public function dateInFrenchFormat(DateTimeInterface $date): string
    {
        return date_format($date, "d/m/Y");
    }
}
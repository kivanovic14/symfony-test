<?php

namespace App\Twig;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigTest;

// php bin/console debug:container 'App\Twig\AppExtension'
class AppExtensions extends AbstractExtension implements GlobalsInterface
{

    private $locale;

//    public function __construct(string $locale)
//    {
//        $this->locale = $locale;
//    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    public function getGlobals(): array
    {
        return [
            'locale' => $this->locale
        ];
    }

    public function priceFilter($number)
    {
        return '$' . number_format($number, 2, '.', ',');
    }

    public function getTests()
    {
        return [
            new TwigTest(
                'like',
                function ($obj) {
                    return $obj instanceof LikeNotification;
                }
            )
        ];
    }
}
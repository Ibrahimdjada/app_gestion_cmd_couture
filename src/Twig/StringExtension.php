<?php
namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
class StringExtension extends AbstractExtension 
{
    public function getFilters()
    {
        return[
            new 
            TwigFilter('capitaize_first',
            [$this,'capitaizeFirstLetter']),
        ];
    }
    public function capitaizeFirstLetter($value)
    {
        if(!is_string($value)){
            return $value;
        }
        return ucfirst(strtolower($value));
    }
}
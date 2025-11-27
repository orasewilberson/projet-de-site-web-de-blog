<?php
namespace Framework\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TimeExtension extends AbstractExtension
{
    /**
     * @return \TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago($date, string $format = 'd/m/Y H:i')
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        if (!$date instanceof \DateTime) {
            throw new \InvalidArgumentException('Expected DateTime object or string for $date');
        }

        return '<span class="timeago" datetime="' . $date->format(\DateTime::ISO8601) . '">' . $date->format($format) . '</span>';
    }
}

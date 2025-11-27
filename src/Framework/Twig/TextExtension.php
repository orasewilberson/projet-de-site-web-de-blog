<?php
namespace Framework\Twig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;


/**
 * serie d'extensions concernant les textes
 */
class TextExtension extends AbstractExtension
{

    /**
     * @return \TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }
    
    /**
     * Renvoie un extrait du contenu
     * @param string $content
     * @param integer $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string
    {
        if(\is_null($content)) {
            return '';
        }
        if(mb_strlen($content) > $maxLength){
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
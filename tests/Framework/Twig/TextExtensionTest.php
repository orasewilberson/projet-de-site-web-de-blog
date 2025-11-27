<?php
namespace tests\Framework\Twig;

use PHPUnit\Framework\TestCase;
use Framework\Twig\TextExtension;

class TextExtensionTest extends TestCase {
    
    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp(): void
    {
    $this->textExtension = new TextExtension();
    }


    public function testExcerptWithShortText()
    {
        $text = "salut";
        $this->assertEquals($text, $this->textExtension->excerpt($text, 10));
    }

    public function testExcerptWithLongText()
    {
        $text = "salut les gens";
        $this->assertEquals('salut...', $this->textExtension->excerpt($text, 7));
        $this->assertEquals('salut les...', $this->textExtension->excerpt($text, 12));

    }
}
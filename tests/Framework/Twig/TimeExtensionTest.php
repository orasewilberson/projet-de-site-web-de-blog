<?php
namespace tests\Framework\Twig;

use PHPUnit\Framework\TestCase;
use Framework\Twig\TimeExtension;

class TimeExtensionTest extends TestCase {
    
    /**
     * @var TimeExtension
     */
    private $timetExtension;

    public function setUp(): void
    {
        $this->timetExtension = new TimeExtension();
    }

    public function testDateFormat()
    {
        $date = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = '<span class="timeago" datetime="' . $date->format(\DateTime::ISO8601) . '">'. $date->format($format) .'</span>';
        $this->assertEquals($result, $this->timetExtension->ago($date));
    }
}
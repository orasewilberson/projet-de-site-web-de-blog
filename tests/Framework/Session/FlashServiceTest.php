<?php
namespace tests\Framework\Session;

use PHPUnit\Framework\TestCase;
use App\Blog\Session\ArraySession;
use App\Blog\Session\FlashService;

class FlashServiceTest extends TestCase {
   
    /**
     * Undocumented variable
     *
     * @var ArraySession
     */
    private $session;

    /**
     * @var FlashService
     */
    private $flashService;

    public function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->flashService->succes('Bravo');
        $this->assertEquals('Bravo', $this->flashService->get('succes'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Bravo', $this->flashService->get('succes'));
        $this->assertEquals('Bravo', $this->flashService->get('succes'));

    }


}
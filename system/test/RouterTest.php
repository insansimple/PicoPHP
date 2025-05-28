<?php

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected $router;

    protected function setUp(): void
    {
        $this->router = new System\Core\Router();
    }

    public function testAddRoute()
    {
        $this->router->add('GET', '/test', function () {
            return 'OK';
        });

        $this->assertCount(1, $this->router->getRoutes());
    }

    // Tambahkan tes lain sesuai kebutuhan...
}

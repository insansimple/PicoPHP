<?php

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        $this->controller = new System\Core\Controller();
    }

    public function testRenderView()
    {
        $output = $this->controller->render('test_view', ['name' => 'World']);
        $this->assertStringContainsString('Hello, World!', $output);
    }

    public function testRedirect()
    {
        // Simulate a redirect and check the response
        $response = $this->controller->redirect('/home');
        $this->assertEquals(302, $response->status);
        $this->assertEquals('/home', $response->location);
    }
}

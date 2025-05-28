<?php

use PHPUnit\Framework\TestCase;

foreach (glob(__DIR__ . '/../library/helpers/*.php') as $file) {
    require_once $file;
}

class HelperTest extends TestCase
{
    public function testHelperFunction()
    {
        // Assuming you have a helper function named 'helperFunction'
        $result = redirect('/home');
        $this->assertEquals(302, $result->status);
        $this->assertEquals('/home', $result->location);
    }

    public function testAnotherHelperFunction()
    {
        // Assuming you have another helper function named 'anotherHelperFunction'
        $result = config('app_name');
        // $this->assertStringContainsString('string', $result);
        $this->assertIsString($result);
    }
}

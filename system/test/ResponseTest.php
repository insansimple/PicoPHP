<?php

use PHPUnit\Framework\TestCase;
use System\Core\Response;

class ResponseTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the instance before each test
        Response::getInstance();
    }

    public function testGetInstance()
    {
        $response1 = Response::getInstance();
        $response2 = Response::getInstance();
        $this->assertSame($response1, $response2);
    }

    public function testSend()
    {
        $response = Response::getInstance();
        ob_start(); // Start output buffering
        $response->send(Response::STATUS_OK, 'OK');
        $output = ob_get_clean(); // Get the output and clean the buffer

        $this->assertEquals(200, http_response_code());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 200, 'message' => 'OK']),
            $output
        );
    }
}

<?php

use PHPUnit\Framework\TestCase;
use System\Core\Request;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test/uri';
        $_GET = ['param1' => 'value1', 'param2' => 'value2'];
        $_POST = ['post1' => 'data1', 'post2' => 'data2'];
    }

    public function testMethod()
    {
        $request = Request::getInstance();
        $this->assertEquals('GET', $request->method());
    }

    public function testUri()
    {
        $request = Request::getInstance();
        $this->assertEquals('/test/uri', $request->uri());
    }

    public function testQueryParams()
    {
        $request = Request::getInstance();
        $this->assertEquals(['param1' => 'value1', 'param2' => 'value2'], $request->queryParams());
    }

    public function testPostData()
    {
        $request = Request::getInstance();
        $this->assertEquals(['post1' => 'data1', 'post2' => 'data2'], $request->postData());
    }

    public function testIsAjax()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $request = Request::getInstance();
        $this->assertTrue($request->isAjax());

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse($request->isAjax());
    }

    public function testHasFile()
    {
        $request = Request::getInstance();
        $this->assertFalse($request->hasFile('test_file'));

        // Simulate a file upload
        $_FILES['test_file'] = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/phpYzdqkD',
            'error' => 0,
            'size' => 123
        ];
        $this->assertTrue($request->hasFile('test_file'));
    }

    public function testFile()
    {
        $request = Request::getInstance();

        // Simulate a file upload
        $_FILES['test_file'] = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/phpYzdqkD',
            'error' => 0,
            'size' => 123
        ];

        $file = $request->file('test_file')->getFile();
        $this->assertEquals('test.txt', $file['name']);
        $this->assertEquals('text/plain', $file['type']);
        $this->assertEquals('/tmp/phpYzdqkD', $file['tmp_name']);
        $this->assertEquals(0, $file['error']);
        $this->assertEquals(123, $file['size']);
    }

    public function testUpload()
    {
        $request = Request::getInstance();

        // Simulate a file upload
        $_FILES['test_file'] = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/phpYzdqkD',
            'error' => 0,
            'size' => 123
        ];
        $file = $request->file('test_file');
        $targetPath = $file->store('disk/uploads');
        $this->assertStringContainsString('disk/uploads/test.txt', $targetPath);
    }
}

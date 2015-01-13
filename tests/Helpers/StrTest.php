<?php

class Test_Helpers_Str extends \TestCase
{
    public function testContains()
    {
        // содержит строку
        $needle = "consectetur adipiscing elit";
        $result = \Cloudstash\Helper\Str::Contains($needle, $this->eng_lorem_ipsum);
        $this->assertTrue($result);

        // не содержит строку
        $needle = "not found";
        $result = \Cloudstash\Helper\Str::Contains($needle, $this->eng_lorem_ipsum);
        $this->assertFalse($result);

        // содержит многобайтовую строку
        $needle = "сделанный H. Rackham, 1914";
        $result = \Cloudstash\Helper\Str::Contains($needle, $this->rus_lorem_ipsum);
        $this->assertTrue($result);
    }

    public function testStartWith()
    {
        // начинается со строки
        $needle = "Lorem ipsum dolor sit amet";
        $result = \Cloudstash\Helper\Str::StartWith($needle, $this->eng_lorem_ipsum);
        $this->assertTrue($result);

        // не начинается со строки
        $needle = "sed do eiusmod tempor incididunt";
        $result = \Cloudstash\Helper\Str::StartWith($needle, $this->eng_lorem_ipsum);
        $this->assertFalse($result);
    }

    public function testEndWith()
    {
        // заканчивается на строку
        $needle = "incididunt ut labore et dolore magna aliqua.";
        $result = \Cloudstash\Helper\Str::EndWith($needle, $this->eng_lorem_ipsum);
        $this->assertTrue($result);

        // не заканчивается на строку
        $needle = "sed do eiusmod tempor incididunt";
        $result = \Cloudstash\Helper\Str::EndWith($needle, $this->eng_lorem_ipsum);
        $this->assertFalse($result);
    }

    public function testRemoveFirst()
    {
        $source = "Hello, is it unit test!";

        // вырезаем из начала строки
        $needle = "Hello, ";
        $result = \Cloudstash\Helper\Str::RemoveFirst($needle, $source);
        $right_result = "is it unit test!";
        $this->assertEquals($right_result, $result);

        // не выреаем из начала строки, так как данный кусок не из ее начала
        $needle = "is it unit";
        $result = \Cloudstash\Helper\Str::RemoveFirst($needle, $source);
        $this->assertEquals($source, $result);
    }

    public function testRemoveLast()
    {
        $source = '/test/case/url/';

        // удаляем, так как строка действительно найдена в конце текущей
        $right_result = "/test/case/url";
        $result = \Cloudstash\Helper\Str::RemoveLast('/', $source);
        $this->assertEquals($right_result, $result);

        // не удаляем, так как строка не найдена в конце текущей
        $right_result = $source;
        $result = \Cloudstash\Helper\Str::RemoveLast('///', $source);
        $this->assertEquals($right_result, $result);
    }

    public function testRevers()
    {
        // разворчиваем строку
        $source = "Hello, man!";
        $right_result = "!nam ,olleH";
        $result = \Cloudstash\Helper\Str::Reverse($source);
        $this->assertEquals($right_result, $result);

        // разворчиваем многобайтовую строку
        $source = "Привет, man!";
        $right_result = "!nam ,тевирП";
        $result = \Cloudstash\Helper\Str::Reverse($source);
        $this->assertEquals($right_result, $result);
    }

    public function testTrim()
    {
        // обрезаем лишние пробелы вначале и в конце строки
        $source = "  Hello!  ";
        $right_result = "Hello!";
        $result = \Cloudstash\Helper\Str::Trim($source);
        $this->assertEquals($right_result, $result);

        // обрезаем лишние пробелы вначале и в конце многобайтовой строки
        $source = "  Привет!  ";
        $right_result = "Привет!";
        $result = \Cloudstash\Helper\Str::Trim($source);
        $this->assertEquals($right_result, $result);

        $source = '  /test/case/url/   ';

        // обрезаем лишние пробелы и удаляем, так как строка действительно найдена в начале и конце текущей
        $right_result = "test/case/url";
        $result = \Cloudstash\Helper\Str::Trim($source, '/', '/');
        $this->assertEquals($right_result, $result);

        // обрезаем лишние пробелы и  не удаляем, так как строка не найдена в начале и конце текущей
        $right_result = "/test/case/url/";
        $result = \Cloudstash\Helper\Str::Trim($source, '///', '///');
        $this->assertEquals($right_result, $result);
    }
}
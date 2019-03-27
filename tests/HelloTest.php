<?php
/**
 * Created by PhpStorm.
 * User: cg
 * Date: 2019/3/21
 * Time: 10:27 PM
 */

use PHPUnit\Framework\TestCase;

class HelloTest extends TestCase
{
    public function testHello()
    {
        $expect = 'hello';
        $result = 'hello';
        $this->assertEquals($expect, $result);
    }
}

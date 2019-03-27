<?php
/**
 * Created by PhpStorm.
 * User: cg
 * Date: 2019/3/21
 * Time: 10:32 PM
 */

use PHPUnit\Framework\TestCase;

require_once '../SqlParser.php';
use App\SqlParser;



class SqlParserTest extends TestCase
{
    public function testHasSpacing()
    {
        $method = new ReflectionMethod(SqlParser::class, 'hasSpacing');
        $method->setAccessible(true);
        $expect = true;
        $actual = $method->invoke(new SqlParser(), ' ');
        $this->assertEquals($expect, $actual);
    }

    public function testParseColumn()
    {
        $method = new ReflectionMethod(SqlParser::class, 'parseColumn');
        $method->setAccessible(true);
        $expect = [
            'name' => 'handset_reverse',
            'comment' => '手机反序',
        ];
        $columnStr = "`handset_reverse` char(15) NOT NULL DEFAULT '' COMMENT '手机反序',";
        $actual = $method->invoke(new SqlParser(), $columnStr);
        $this->assertEquals($expect, $actual);
    }

    public function testParseFile()
    {
        $parser = new SqlParser();
        $file = 'sql-example.sql';
        $parser->parseSQL($file);
//        $database = $parser->getDatabase();
        $database = $parser->database;
        $tableNames = ['account', 'account_brand_group_relation'];
        $table1 = [
            'name' => 'account',
            'comment' => '商家登录账号表',
            'column' => [
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
            ]
        ];
        $table2 = [
            'name' => 'account',
            'comment' => '商家登录账号表',
            'column' => [
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
                ['name' => '', 'comment' => ''],
            ]
        ];
        $expect = [
            'tableName' => $tableNames,
            'table' => [$table1, $table2]
        ];
        $this->assertEquals($expect, $database);
    }

    public function testRun()
    {
        $parser = new SqlParser();
        $file = 'sql-example.sql';
        $parser->parseSQL($file);
        $parser->createAllDocument('test.md');

    }


}

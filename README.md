# parse-mysql-sql

解析 navicat for mysql 导出的 sql 文件的小工具。

使用例子：

```
    $parser = new SqlParser();
    $file = 'sql-example.sql';
    $parser->parseSQL($file);
    $parser->createAllDocument('test.md');

```
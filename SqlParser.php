<?php
declare(strict_types=1);

namespace App;

class SqlParser
{
    private $database;
    private $prefixs;

    const COMMENT_KEYWORD = 'COMMENT';

    public function parseSQL(string $file): void
    {
        $database = [];
        $handle = fopen($file, 'r');

        if (empty($handle)) {
            $this->database = $database;
            exit;
        }

        $stack = new \SplStack();

        $in = false;
        $out = false;
        $tableName = [];
        $tables = [];
        while (($line = fgets($handle, 4096)) !== false) {

            if (!$in && $this->isStart($line)) {
                $in = true;
            }

            if (!$out && $this->isEnd($line)) {
                $out = true;
            }

            if (!$in) {
                continue;
            } else {
                $stack->push($line);
            }

            if (empty($out)) {
                continue;
            }

            $arr = [];
            while ($stack->count()) {
                $newLine = $stack->shift();
                $arr[] = $newLine;
            }

            $stack = new \SplStack();
            $out = false;
            $in = false;

            $table = $this->parseTable($arr);
            $tables[] = $table;
            $tableName[] = $table['name'] ?? '';
        }

        ksort($tables);
        ksort($tableName);

        $database = [
            'table' => $tables,
            'tableName' => $tableName
        ];

        $this->database = $database;
    }


    private function getPrefixes()
    {
        $tableNames = $this->database['tableName'] ?? [];
        $prefixes = [];
        foreach ($tableNames as $tableName) {
            $arr = explode('_', $tableName);
            $prefix = $arr[0] ?? '';
            if (empty($prefix)) {
                continue;
            }
            $prefixes[] = $prefix;
        }

        $prefixes = array_unique($prefixes);

        return $prefixes;
    }

    private function parseTable(array $sql): array
    {
        $table = [];
        $columns = [];

        $start = array_shift($sql);
        $end = array_pop($sql);

        $pattern = "#`(.*?)`#";
        preg_match($pattern, $start, $matches1);
        $table['name'] = $matches1[1];

        $pattern = "#COMMENT='(.*?)'#";
        preg_match($pattern, $end, $matches2);

        if (isset($matches2[1])) {
            $table['comment'] = trim($matches2[1]);
        } else {
            $table['comment'] = '';
        }

        foreach ($sql as $v) {

            $pattern = "#KEY.*?(.*?)#";
            preg_match($pattern, $v, $matches);
            if (!empty($matches[0])) {
                continue;
            }

            $v = trim($v, ",\n");
            $column = $this->parseColumn($v);

            $columns[] = $column;
        }

        $table['column'] = $columns;

        return $table;
    }

    private function formatPrefixes(array $prefixes): array
    {
        $arr = [];

        foreach ($prefixes as $prefix) {
            $prefixArr = explode('##', $prefix);
            $k = $prefixArr[0] ?? '';
            if (empty($k)) {
                continue;
            }
            $v = trim($prefixArr[1]);
            if (empty($v) || $v == "") {
                $v = $prefixArr[0];
            } else {
                $v = $prefixArr[1];
            }

            $arr[$k] = trim($v);
        }

        return $arr;
    }

    public function createAllDocument($filename, array $filter = [], bool $usePrefix = false): void
    {
        $docs = [];

        if ($usePrefix) {
            $prefixes = $this->getPrefixes();
            foreach ($prefixes as $k => $prefix) {
                if (!in_array($prefix, $filter)) {
                    continue;
                }
                $content = $this->createDocumentByPrefix($prefix);
                file_put_contents($prefix, $content);
            }
        } else {
            $tables = $this->database['table'] ?? [];
            $docs = $this->createDocument($tables);
        }

        file_put_contents($filename, $docs);
    }

    public function createDocumentByPrefix(string $prefix): string
    {
        $tables = $this->getTablesByPrefix($prefix);
        $doc = $this->createDocument($tables);

        return $doc;
    }

    private function getTablesByPrefix(string $prefix): array
    {
        $tables = $this->database['table'] ?? [];
        $newTables = [];
        foreach ($tables as $table) {
            $tableName = $table['name'] ?? '';
            if (strpos($tableName, $prefix) === 0) {
                $newTables[] = $table;
            }
        }

        return $newTables;
    }

    private function createDocument(array $tables): string
    {
        $doc = '';
        foreach ($tables as $table) {
            $doc .= $this->createDocumentSegment($table);
        }

        return $doc;
    }

    private function createDocumentSegment(array $table): string
    {
        $title = "## " . $table['name'] . " (" . $table['comment'] . ")" . PHP_EOL;
        $head = <<<MD
字段|描述
:---|:---
MD;
        $head .= PHP_EOL;
        $body = '';
        $columns = $table['column'] ?: [];
        foreach ($columns as $column) {
            $body .= $column['name'] . '|' . $column['comment'] . PHP_EOL;
        }

        $doc = $title . $head . $body;

        return $doc;
    }

    private function isStart(string $str): bool
    {
        $pattern = "#CREATE TABLE .*? \(#";
        preg_match_all($pattern, $str, $matches);

        return !empty($matches[0]);

    }


    private function isEnd(string $str): bool
    {
        $search = ") ENGINE=";
        if (strpos($str, $search) === false) {
            return false;
        } else {
            return true;
        }
    }

    public function getTableWithoutComment(): array
    {
        $database = $this->database;
        $tables = $database['table'] ?? [];

        $tableWithoutComment = [];
        $tableColumnWithoutComment = [];
        foreach ($tables as $table) {

            $tableName = $table['name'];
            $comment = $table['comment'] ?? '';
            if (empty($comment)) {
                $tableWithoutComment[] = $tableName;
            }

            $columns = $table['column'] ?? [];
            $tmp = [];
            if ($this->checkTableIsWithoutColumnComment($columns)) {
                $tmp['name'] = $tableName;

                $emptyColumnStr = '';
                foreach ($columns as $column) {
                    $columnName = $column['name'];
                    if (in_array($columnName, self::EXCEPT_COLUMN)) {
                        continue;
                    }

                    $columnComment = $column['comment'] ?? '';
                    if (empty($columnComment)) {
                        $emptyColumnStr .= $columnName . ',';
                    }
                }

                $tmp['emptyColumn'] = $emptyColumnStr;

                $tableColumnWithoutComment[] = $tmp;
            }
        }

        $targetTables = [
            'table' => $tableWithoutComment,
            'column' => $tableColumnWithoutComment
        ];

        return $targetTables;
    }

    private function checkTableIsWithoutColumnComment(array $columns): bool
    {
        foreach ($columns as $column) {
            $columnName = $column['name'];
            if (in_array($columnName, self::EXCEPT_COLUMN)) {
                continue;
            }
            $columnComment = $column['comment'] ?? '';
            if (empty($columnComment)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $v
     * @param $column
     * @return mixed
     */
    private function parseColumn(string $v): array
    {
        $newArr = explode(' ', $v);
        $newArr = array_filter($newArr, function ($item) {
            return (!empty($item) || $item != '');
        });

        $column['name'] = trim(array_shift($newArr), '``');

        $pattern = "#COMMENT.*?'(.*?)'#";
        preg_match($pattern, $v, $matches);
        $column['comment'] = $matches[1] ?? '';

        return $column;
    }

    private function checkTableColumnCommentHasSpacing(array $tableColumns): bool
    {
        foreach ($tableColumns as $column) {
            if ($this->hasSpacing($column)) {
                return true;
            }
        }
        return false;
    }

    private function hasSpacing(string $string): bool
    {
        $pos = strpos($string, ' ');

        if ($pos === false) {
            return false;
        }

        return true;
    }

    private function checkCategoryHasSpacing(string $prefix): bool
    {
        $tables = $this->getTablesByPrefix($prefix);
        foreach ($tables as $table) {
            $columns = $table['column'];
            if ($this->checkTableColumnCommentHasSpacing($columns)) {
                return true;
            }
        }

        return false;
    }

    private function getCategoryHasSpacing(): array
    {
        $formatedPrefixs = $this->formatPrefixes($this->prefixs);
        $prefixArr = array_keys($formatedPrefixs);

        $prefixHasBugArr = [];
        foreach ($prefixArr as $prefix) {
            if ($this->checkCategoryHasSpacing($prefix)) {
                $prefixHasBugArr[] = $prefix;
            }
        }

        return $prefixHasBugArr;
    }

    public function fixColumnCommentHasSpacing(): array
    {
        $prefixsHasSpacing = $this->getCategoryHasSpacing();
        $docs = $this->createAllDocument($prefixsHasSpacing);

        return $docs;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}



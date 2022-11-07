<?php

namespace Jackdou\PhpModelGenerator;

use Jackdou\PhpModelGenerator\Components\Base;
use Jackdou\PhpModelGenerator\Components\Func;
use Jackdou\PhpModelGenerator\Components\Header;
use Jackdou\PhpModelGenerator\Components\Property;
use Jackdou\PhpModelGenerator\Components\Tail;
use Jackdou\PhpModelGenerator\Interfaces\Components;
use PDO;

class Application
{
    private $table;
    private $class_name;
    private $namespace;
    private $var_name;
    private $comment_text;
    private $comment_var_type;

    public function __construct(string $cfgFile)
    {
        Config::instance()->parse($cfgFile);
        Database::instance()->connect(Config::instance());
        $this->namespace = Config::instance()->get('namespace');
        $this->db = Config::instance()->get('dbname');
    }

    public function run()
    {
        $include = Config::instance()->get('include');
        $includes = explode(',', $include);
        $exclude = Config::instance()->get('exclude');
        $excludes = explode(',', $exclude);
        $path = Config::instance()->get('path');
        $class_mode = Config::instance()->get('class_name_mode', CLASS_MODE_UCFIRST);
        $file_name_tail = Config::instance()->get('file_name_tail', 'Struct');
        if (!is_dir($path)) {
            $r = mkdir($path, 0755, true);
            if (!$r) {
                throw new \Exception("sorry, I can't create the path:[{$path}].");
            }
        }
        $this->genBase($path, $file_name_tail);

        $tables = Database::instance()->fetchAll('SHOW TABLES', PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $this->table = $table;
            $this->class_name = $this->genClassName($table, $class_mode, $file_name_tail);
            if (in_array($table, $includes) && !in_array($table, $excludes)) {
                $content = '';
                // 生成文件
                $content .= $this->genHeader();
                $content .= $this->genProperty();
                $content .= $this->genFunc();
                $content .= $this->genTail();
                $this->createFile($content, $path);
            }
        }
    }

    protected function genBase(string $path, string $file_name_tail)
    {
        // 生成基类文件
        $mode = Config::instance()->get('class_name_mode', CLASS_MODE_UCFIRST);
        $this->class_name = $this->genClassName('Base', $mode, $file_name_tail);
        $base = new Base;
        $base = $this->inspect($base);
        $this->createFile($base->gender(), $path);
    }

    protected function genHeader()
    {
        $header = new Header;
        $header = $this->inspect($header);
        return $header->gender();
    }

    protected function  genProperty()
    {
        $sql = "SELECT COLUMN_NAME,DATA_TYPE,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='%s' AND TABLE_NAME='%s'";
        $cols = Database::instance()->fetchAll(sprintf($sql, $this->db, $this->table));
        $content = '';
        foreach ($cols as ['COLUMN_NAME' => $n, 'DATA_TYPE' => $t, 'COLUMN_COMMENT' => $c]) {
            $this->comment_var_type = $t;
            $this->comment_text = $c;
            $this->var_name = $n;
            $property = new Property;
            $property = $this->inspect($property);
            $content .= $property->gender();
        }
        return $content;
    }

    protected function genFunc()
    {
        $func = new Func;
        $func = $this->inspect($func);
        return $func->gender();
    }

    protected function genTail()
    {
        $tail = new Tail;
        $tail = $this->inspect($tail);
        return $tail->gender();
    }

    private function inspect(Components $com)
    {
        $t = $com->getParamsType();
        for ($i = 1; $i <= $t; $i++) {
            if (($i & $t) == $i) { // 001 & 011 == 001 
                switch ($i) {
                    case CLASS_NAME:
                        $com->setParams(CLASS_NAME, $this->class_name);
                        break;
                    case NAME_SPACE:
                        $com->setParams(NAME_SPACE, $this->namespace);
                        break;
                    case VAR_NAME:
                        $com->setParams(VAR_NAME, $this->var_name);
                        break;
                    case COMMENT_TEXT:
                        $com->setParams(COMMENT_TEXT, $this->comment_text);
                        break;
                    case COMMENT_VAR_TYPE:
                        $com->setParams(COMMENT_VAR_TYPE, $this->comment_var_type);
                        break;
                }
            }
        }
        return $com;
    }

    private function genClassName(string $src_name, int $mode, string $file_name_tail)
    {
        $n = '';
        switch ($mode) {
            case CLASS_MODE_UCFIRST:
                $words = explode('_', $src_name);
                foreach ($words as $w) {
                    $n .= ucfirst($w);
                }
                $n .= $file_name_tail;
                return $n;
            default:
                return $src_name;
        }
    }

    private function createFile($content, $path)
    {
        $file = $path . sprintf('/%s.php', $this->class_name);
        $jump_exists = Config::instance()->get('jump_exists', true);
        if ($jump_exists && file_exists($file)) {
            return true;
        }
        return file_put_contents($file, $content);
    }
}

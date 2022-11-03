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
        $class_mode = Config::instance()->get('class_name_mode', Constant::CLASS_MODE_UCFIRST);
        $file_name_tail = Config::instance()->get('file_name_tail', 'Struct');
        if (!is_dir($path)) {
            $r = mkdir($path, 0755, true);
            if (!$r) {
                throw new \Exception("sorry, I cant create the path:[{$path}].");
            }
        }
        // 生成基类文件
        $this->class_name = 'BaseStruct';
        $base = new Base;
        $base = $this->inspect($base);
        file_put_contents($path . '/' . 'BaseStruct1.php', $base->gender());
        $tables = Database::instance()->fetchAll('SHOW TABLES', PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $this->table = $table;

            $this->class_name = '';
            if ($class_mode == Constant::CLASS_MODE_UCFIRST) {
                $table_words = explode('_', $table);
                foreach ($table_words as $w) {
                    $this->class_name .= ucfirst($w);
                }
                $this->class_name .= $file_name_tail;
            }
            if (in_array($table, $includes) && !in_array($table, $excludes)) {
                $content = '';
                // 生成文件
                $content .= $this->genHeader();
                $content .= $this->genProperty();
                $content .= $this->genFunc();
                $content .= $this->genTail();
                file_put_contents($path . '/' . $this->class_name . '.php', $content);
            }
        }
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
                    case Constant::CLASS_NAME:
                        $com->setParams(Constant::CLASS_NAME, $this->class_name);
                        break;
                    case Constant::NAME_SPACE:
                        $com->setParams(Constant::NAME_SPACE, $this->namespace);
                        break;
                    case Constant::VAR_NAME:
                        $com->setParams(Constant::VAR_NAME, $this->var_name);
                        break;
                    case Constant::COMMENT_TEXT:
                        $com->setParams(Constant::COMMENT_TEXT, $this->comment_text);
                        break;
                    case Constant::COMMENT_VAR_TYPE:
                        $com->setParams(Constant::COMMENT_VAR_TYPE, $this->comment_var_type);
                        break;
                }
            }
        }
        return $com;
    }
}

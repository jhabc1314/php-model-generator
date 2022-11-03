<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Constant;
use Jackdou\PhpModelGenerator\Interfaces\Components;

class Property implements Components
{
    private $comment_text;
    private $column_type;
    private $comment_var_type;
    private $var_name;

    public function setParams(int $type, $arg)
    {
        switch ($type) {
            case Constant::COMMENT_TEXT:
                $this->comment_text = $arg;
                break;
            case Constant::COMMENT_VAR_TYPE:
                $this->column_type = $arg;
                $this->comment_var_type = $this->translate($arg);
                break;
            case Constant::VAR_NAME:
                $this->var_name = $arg;
                break;
        }
    }

    public function getParamsType(): int
    {
        return Constant::COMMENT_TEXT | Constant::COMMENT_VAR_TYPE | Constant::VAR_NAME;
    }

    public function gender(): string
    {
        return sprintf(<<<EOF

    /**
     * %s 
     * 字段类型:%s
     * @var %s
     */
    public $%s;

EOF, $this->comment_text, $this->column_type, $this->comment_var_type, $this->var_name);
    }

    private function translate($column_type)
    {
        return [
            'tinyint' => 'int',
            'smallint' => 'int',
            'bigint' => 'int',
            'char' => 'string',
            'varchar' => 'string',
            'text' => 'string',
            'decimal' => 'float',
        ][$column_type] ?? $column_type;
    }
}

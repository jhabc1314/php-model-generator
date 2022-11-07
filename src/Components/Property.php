<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Interfaces\Components;
use const Jackdou\PhpModelGenerator\COMMENT_TEXT;
use const Jackdou\PhpModelGenerator\COMMENT_VAR_TYPE;
use const Jackdou\PhpModelGenerator\VAR_NAME;

class Property implements Components
{
    private $comment_text;
    private $column_type;
    private $comment_var_type;
    private $var_name;

    public function setParams(int $type, string $arg)
    {
        switch ($type) {
            case COMMENT_TEXT:
                $this->comment_text = $arg;
                break;
            case COMMENT_VAR_TYPE:
                $this->column_type = $arg;
                $this->comment_var_type = $this->translate($arg);
                break;
            case VAR_NAME:
                $this->var_name = $arg;
                break;
        }
    }

    public function getParamsType(): int
    {
        return COMMENT_TEXT | COMMENT_VAR_TYPE | VAR_NAME;
    }

    public function gender(): string
    {
        $tpl = SRC_PATH . '/template/PropertyTemplate.tpl';
        return sprintf(file_get_contents($tpl),
            $this->comment_text, $this->column_type, $this->comment_var_type, $this->var_name);
    }

    private function translate($column_type): string
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

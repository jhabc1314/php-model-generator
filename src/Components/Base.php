<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Interfaces\Components;
use const Jackdou\PhpModelGenerator\CLASS_NAME;
use const Jackdou\PhpModelGenerator\NAME_SPACE;

class Base implements Components
{
    private $class_name;
    private $namespace;

    public function setParams(int $type, string $arg)
    {
        if ($type == CLASS_NAME) {
            $this->class_name = $arg;
        } elseif ($type == NAME_SPACE) {
            $this->namespace = $arg;
        }
    }

    public function getParamsType(): int
    {
        return CLASS_NAME | NAME_SPACE;
    }

    public function gender(): string
    {
        $tpl = SRC_PATH . '/template/BaseTemplate.tpl';
        return sprintf(file_get_contents($tpl), $this->namespace, $this->class_name);
    }
}

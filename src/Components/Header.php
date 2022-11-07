<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Interfaces\Components;
use const Jackdou\PhpModelGenerator\CLASS_NAME;
use const Jackdou\PhpModelGenerator\NAME_SPACE;

class Header implements Components
{
    private $namespace;
    private $classname;
    public function setParams(int $type, string $arg)
    {
        if ($type == CLASS_NAME) {
            $this->classname = $arg;
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
        $tpl = SRC_PATH . '/template/HeaderTemplate.tpl';
        return sprintf(file_get_contents($tpl), $this->namespace, $this->classname, $this->classname, $this->classname);
    }
}

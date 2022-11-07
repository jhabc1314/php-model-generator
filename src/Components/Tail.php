<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Interfaces\Components;

class Tail implements Components
{
    public function setParams(int $type, string $arg)
    {
    }

    public function getParamsType(): int
    {
        return 0;
    }

    public function gender(): string
    {
        $tpl = SRC_PATH . '/template/TailTemplate.tpl';
        return file_get_contents($tpl);
    }
}

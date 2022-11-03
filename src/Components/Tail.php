<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Interfaces\Components;

class Tail implements Components
{
    public function setParams(int $type, $args)
    {
    }

    public function getParamsType(): int
    {
        return 0;
    }

    public function gender(): string
    {
        return <<<EOF

}

EOF;
    }
}

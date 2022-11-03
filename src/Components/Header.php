<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Constant;
use Jackdou\PhpModelGenerator\Interfaces\Components;

class Header implements Components
{
    private $namespace;
    private $classname;
    public function setParams(int $type, $args)
    {
        if ($type == Constant::CLASS_NAME) {
            $this->classname = $args;
        } elseif ($type == Constant::NAME_SPACE) {
            $this->namespace = $args;
        }
    }

    public function getParamsType(): int
    {
        return Constant::CLASS_NAME | Constant::NAME_SPACE;
    }

    public function gender(): string
    {
        return sprintf(<<<EOF
<?php

namespace %s;

use ArrayAccess;

class %s extends BaseStruct implements ArrayAccess
{

EOF, $this->namespace, $this->classname);
    }
}

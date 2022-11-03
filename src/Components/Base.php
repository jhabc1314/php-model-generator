<?php

namespace Jackdou\PhpModelGenerator\Components;

use Jackdou\PhpModelGenerator\Constant;
use Jackdou\PhpModelGenerator\Interfaces\Components;

class Base implements Components
{
    private $class_name;
    private $namespace;

    public function setParams(int $type, $arg)
    {
        if ($type == Constant::CLASS_NAME) {
            $this->class_name = $arg;
        } elseif ($type == Constant::NAME_SPACE) {
            $this->namespace = $arg;
        }
    }

    public function getParamsType(): int
    {
        return Constant::CLASS_NAME | Constant::NAME_SPACE;
    }

    public function gender(): string
    {
        return sprintf(<<<'EOF'
<?php

namespace %s;

class %s
{
    /**
     * 扫描单条数据库查询记录到此对象中
     *
     * @param array $row
     * 
     * @return static|null
     */
    public static function scan($row)
    {
        if (!$row || !is_array($row)) {
            return null;
        }
        $call = new static();
        $vars = get_class_vars(static::class);
        foreach ($vars as $var => $_) {
            if (isset($row[$var])) {
                $call->$var = $row[$var];
            }
        }
        return $call;
    }

    /**
     * 扫描多条数据记录到结果集中
     *
     * @param array $rows
     * @return static[]|null
     */
    public static function scanList($rows)
    {
        if (!$rows || !is_array($rows)) {
            return null;
        }
        $return = [];
        foreach ($rows as $row) {
            $r = self::scan($row);
            if ($r) {
                $return[] = $r;
            }
        }
        return $return;
    }
}

EOF, $this->namespace, $this->class_name);
    }
}

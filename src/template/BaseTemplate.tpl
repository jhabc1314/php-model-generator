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
    public static function scan(array $row): ?array
    {
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
    public static function scanList(array $rows): ?array
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

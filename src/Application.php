<?php

namespace Jackdou\PhpModelGenerator;

class Application
{
    public function __construct(string $cfgFile)
    {
        Config::instance()->parse($cfgFile);
        Database::instance()->connect(Config::instance());
    }

    public function run()
    {
        $tables = Database::instance()->fetchAll("show tables");
        $include = Config::instance()->get('include');
        $includes = explode(',', $include);
        $exclude = Config::instance()->get('exclude');
        $excludes = explode(',', $exclude);
        $namespace = Config::instance()->get('namespace');
        $path = Config::instance()->get('path');
        foreach ($tables as $table) {
            if (in_array($table, $includes) && !in_array($table, $excludes)) {
                echo $table . PHP_EOL;
            }
        }
    }
}

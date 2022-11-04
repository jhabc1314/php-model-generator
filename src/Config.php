<?php

/**
 * Config
 * @author jackdou
 * @company Home
 * @date 2022-11-03
 */

namespace Jackdou\PhpModelGenerator;

class Config
{
    private $container;

    private static $_instance;

    public static function instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }
        self::$_instance = new self();
        return self::$_instance;
    }

    public function parse(string $cfgFile)
    {
        if (!file_exists($cfgFile)) {
            throw new \Exception("sorry,I can't find this config file: [{$cfgFile}].");
        }
        // TODO 根据配置文件使用不同的适配器
        $fp = fopen($cfgFile, 'r');
        while (!feof($fp)) {
            $c = trim(fgets($fp));
            if (empty($c)) {
                continue;
            }
            $equal = strpos($c, '=');
            $k = substr($c, 0, $equal);
            $v = substr($c, $equal + 1);
            $this->container[trim($k)] = trim($v);
        }
        return $this;
    }

    public function get(string $key, $default = null)
    {
        return $this->container[$key] ?? $default;
    }

    private function __construct()
    {
    }
}

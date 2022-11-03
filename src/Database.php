<?php

namespace Jackdou\PhpModelGenerator;

use PDO;

class Database
{
    /**
     * @var PDO
     */
    private $pdo;
    private static $_instance;

    public static function instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }
        return self::$_instance = new self();
    }

    public function connect(Config $cfg)
    {
        $dsn = $cfg->get('dsn');
        $user = $cfg->get('user');
        $password = $cfg->get('password');
        try {
            $this->pdo = new PDO($dsn, $user, $password);
        } catch(\Exception $e) {
            throw new \Exception("sorry,connect to mysql fail:" . $e->getMessage());
        }
    }

    public function fetch($sql)
    {
        $stmt = $this->pdo->prepare($sql);
        $r = $stmt->execute();
        if (!$r) {
            throw new \Exception("execute fail:" . $stmt->errorCode());
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql)
    {
        $stmt = $this->pdo->prepare($sql);
        $r = $stmt->execute();
        if (!$r) {
            throw new \Exception("execute fail:" . $stmt->errorCode());
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function __construct()
    {
    }
}

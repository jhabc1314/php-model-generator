
    public function offsetExists($offset): bool
    {
        $vars = get_class_vars(self::class);
        if (!array_key_exists($offset, $vars)) {
            return false;
        }
        return true;
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value): void
    {
        $vars = get_class_vars(self::class);
        if (!array_key_exists($offset, $vars)) {
        return;
    }
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
    }

    /**
    * 转换成数组
    *
    * @return array
    */
    public function toArray(): array
    {
        return (array)$this;
    }

    public function __get($var)
    {
        return null;
    }

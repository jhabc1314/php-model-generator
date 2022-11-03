<?php
namespace Jackdou\PhpModelGenerator\Interfaces;

interface Components
{
    /**
     * 注入变量参数
     *
     * @param int $type
     * @param string $arg
     * 
     * @return void
     */
    public function setParams(int $type, $arg);
    
    /**
     * 生成内容
     *
     * @return string
     */
    public function gender():string;

    /**
     * 返回需要什么类型的参数
     *
     * @return integer
     */
    public function getParamsType():int;
}


<?php

namespace Bag2\Cookie;

use OutOfRangeException;

/**
 * Cookie class for HTTP Set-Cookie header
 *
 * @property-read string $name
 * @property-read string $value
 * @property-read array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
 */
final class Cookie
{
    /** @var string */
    private $name;
    /** @var string */
    private $value;
    /** @var array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} */
    private $options;

    /**
     * @param string|int $value
     * @param array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string} $options
     * @phan-suppress PhanAccessReadOnlyMagicProperty
     */
    public function __construct(string $name, $value, array $options = [])
    {
        $this->name = $name;
        $this->value = (string)$value;
        $this->options = $options;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param string $name
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * @param string $name
     * @param string|int $_value
     * @return void
     */
    public function __set($name, $_value)
    {
        throw new OutOfRangeException();
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        throw new OutOfRangeException();
    }

    /**
     * @param array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}} $data
     */
    public function fromArray(array $data): Cookie
    {
        return new Cookie($data['name'], $data['value'], $data['options']);
    }

    /**
     * @return array{name:string,value:string,options:array{expires?:int,path?:string,domain?:string,secure?:bool,httponly?:bool,samesite?:string}}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
        ];
    }
}

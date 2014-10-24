<?php

namespace ArrayReader;

class ArrayReader implements \ArrayAccess, \Iterator {

    private $data = null;
    private $is_undefined = null;

    public function __construct($data, $is_undefined=false) {
        $this->data = $data;
        $this->is_undefined = $is_undefined;
    }

    public function getData() {
        return $this->data;
    }

    public function get() {
        $data = $this->data;
        $is_undefined = false;
        foreach (func_get_args() as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                $data = null;
                $is_undefined = true;
                break;
            }
        }
        return new ArrayReader($data, $is_undefined);
    }

    public function has() {
        $data = $this->data;
        foreach (func_get_args() as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return false;
            }
        }
        return true;
    }

    public function value($default=null) {
        if ($this->is_undefined) {
            return $default;
        } else {
            return $this->data;
        }
    }

    public function string($default='') {
        return strval($this->value($default));
    }

    public function integer($default=0) {
        return intval($this->value($default));
    }

    public function float($default=0.0) {
        return doubleval($this->value($default));
    }

    public function asArray($default=array()) {
        $value = $this->value($default);
        if (is_array($value)) {
            return $value;
        } else {
            return $default;
        }
    }

    public function each($callback) {
        foreach ($this->asArray() as $key => $value) {
            $callback(new ArrayReader($value), $key);
        }
    }

    public function with($callback) {
        if (!$this->is_undefined) {
            $callback($this);
        }
    }

    public function reduce($callback, $carry=0) {
        foreach ($this->asArray() as $key => $item) {
            $carry = $callback($carry, $this->get($key));
        }
        return $carry;
    }

    private static function toArray($value) {
        if (!is_array($value)) {
            $value = array($value);
        }
        return $value;
    }

    /*
     * ArrayAccess methods
     */

    public function offsetExists($key) {
        return $this->has($key);
    }

    public function offsetGet($key) {
        return $this->get($key);
    }

    public function offsetSet($key, $value) {
        // Nothing, don't allow setting
    }

    public function offsetUnset($key) {
        // Nothing, don't allow unsetting
    }

    /*
     * Iterator methods
     */

    public function current() {
        if (is_array($this->data)) {
            return $this->get(key($this->data));
        }
    }

    public function key() {
        if (is_array($this->data)) {
            return key($this->data);
        }
    }

    public function next() {
        if (is_array($this->data)) {
            next($this->data);
        }
    }

    public function rewind() {
        if (is_array($this->data)) {
            rewind($this->data);
        }
    }

    public function valid() {
        if (is_array($this->data)) {
            return true;
        } else {
            return false;
        }
    }

    public function _toString() {
        return $this->string();
    }

}

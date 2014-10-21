<?php

namespace ArrayReader;

class ArrayReader {

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
            if (is_array($data)) {
                if (array_key_exists($key, $data)) {
                    $data = $data[$key];
                } else {
                    $data = array();
                    $is_undefined = true;
                    break;
                }
            } else {
                $is_undefined = true;
                break;
            }
        }
        return new ArrayReader($data, $is_undefined);
    }

    public function has() {
        $data = $this->data;
        foreach (func_get_args() as $key) {
            if (is_array($data)) {
                if (array_key_exists($key, $data)) {
                    $data = $data[$key];
                } else {
                    return false;
                }
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

    private static function toArray($value) {
        if (!is_array($value)) {
            $value = array($value);
        }
        return $value;
    }

}

<?php

namespace webd\vectors;

class Vector
{
    protected $value = array();
    
    public function __construct($value = null) {
        if (is_null($value)) {
            return;
        }
        
        if (is_array($value)) {
            $this->value = $value;
            return;
        }
        
        $this->value = func_get_args();
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function display() {
        echo implode(" ", $this->value) . "\n";
    }
    
    public function mean() {
        $count = count($this->value); 
        $sum = array_sum($this->value); 
        return $sum / $count; 
    }
    
    public function variance() {
        $mean = $this->mean();
        $accumulator = 0;
        for ($i=0; $i < count($this->value); $i++) {
            $accumulator += ($this->value[$i] - $mean)^2 / count($this->value);
        }
        return $accumulator;
    }
    
    public function standardDeviation() {
        return sqrt($this->variance());
    }
    
    public function dotProduct(Vector $other) {
        $accumulator = 0;
        for ($i=0; $i<count($this->value); $i++) {
            $accumulator += ($this->value[$i] * $other->value[$i]);
        }
        return sqrt($accumulator);
    }
    
    public function crossProduct (Vector $other) {
        if ($this->dim() != 3) {
            throw new Exception("Must be 3 dimension");
        }
        
        if ($other->dim() != 3) {
            throw new Exception("Must be 3 dimension");
        }
        
        
        $class = get_called_class();
        $result = new $class;
        /* @var $result Vector */
        $result->value = array(
            $this->value[1] * $other->value[2] - $this->value[2] * $other->value[1],
            $this->value[2] * $other->value[0] - $this->value[0] * $other->value[2],
            $this->value[0] * $other->value[1] - $this->value[1] * $other->value[0]
        );
        
        return $result;
    }
    
    public function add(Vector $other) {
        $class = get_called_class();
        $array = array();
        for ($i=0; $i<count($this->value); $i++) {
            $array[$i] = $this->value[$i] + $other->value[$i];
        }
        return new $class($array);
    }
    
    public function __add(Vector $other) {
        return $this->add($other);
    }
    
    public function __sub($other) {
        $array = array();
        if (is_numeric($other)) {
            for ($i=0; $i<count($this->value); $i++) {
                $array[$i] = $this->value[$i] - $other;
            }
        } else {
            for ($i=0; $i<count($this->value); $i++) {
                $array[$i] = $this->value[$i] - $other->value[$i];
            }
        }
        return new Vector($array);
    }
    
    public function __div($other) {
        $class = get_called_class();
        $array = array();
        for ($i=0; $i<count($this->value); $i++) {
            $array[$i] = $this->value[$i] / $other;
        }
        return new $class($array);
    }
    
    public function length() {
        $accumulator = 0;
        for ($i=0; $i<count($this->value); $i++) {
            $accumulator += ($this->value[$i] * $this->value[$i]);
        }
        return sqrt($accumulator);
    }
    
    public function dim() {
        return count($this->value);
    }
    
    /**
     * Project a set of points on this vector, and return a 1D vector
     * @param Array $array
     */
    public function scalarProject(Vector $other) {
        $result = $this->dotProduct($other) / $this->length();
        if (is_numeric($result)) {
            return $result;
        } else {
            return 0;
        }
    }
}
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
    
    /**
     * 
     * @return [float] Array of value
     */
    public function getValue() {
        return $this->value;
    }
    
    public function display($delimiter = "\t") {
        echo implode($delimiter, $this->value) . "\n";
    }
    
    // ----------------- Standard operations
    
    /**
     * Sum of 2 Vectors OR
     * Sum of a Vector + a numeric
     * 
     * @param Vector or numeric $other
     * @return \webd\vectors\Vector (or child of...)
     */
    public function add($other) {
        $class = get_called_class();
        $array = array();
        
        if (is_numeric($other)) {
            for ($i=0; $i<count($this->value); $i++) {
                $array[$i] = $this->value[$i] + $other;
            }
            
        } else {
            for ($i=0; $i<count($this->value); $i++) {
                $array[$i] = $this->value[$i] + $other->value[$i];
            }
        }
        
        return new $class($array);
    }
    
    public function sub($other) {
        $class = get_called_class();
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
        
        return new $class($array);
    }
    
    public function dotProduct(Vector $other) {
        $accumulator = 0;
        for ($i=0; $i<count($this->value); $i++) {
            $accumulator += ($this->value[$i] * $other->value[$i]);
        }
        return $accumulator;
    }
    
    public function crossProduct (Vector $other) {
        if ($this->length() != 3) {
            throw new Exception("Must be 3 dimension");
        }
        
        if ($other->length() != 3) {
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
    
    public function div($other) {
        $class = get_called_class();
        $array = array();
        
        for ($i=0; $i<count($this->value); $i++) {
            $array[$i] = $this->value[$i] / $other;
        }
        
        return new $class($array);
    }
    
    
    
    // ----------------- If PECL extension OPERATOR is installed...
    public function __add($other) {
        return $this->add($other);
    }
    
    public function __sub($other) {
        return $this->sub($other);
    }
    
    public function __concat(Vector $other) {
        return $this->dotProduct($other);
    }
    
    public function __mul($other) {
        return $this->crossProduct($other);
    }
    
    public function __div($other) {
        return $this->div($other);
    }
    
    // ----------------- Some statistical operations
    /**
     * Compute the sample mean (estimated mean) of the values
     * @return float
     */
    public function mean() {
        $count = count($this->value); 
        $sum = array_sum($this->value); 
        return ((float) $sum) / $count; 
    }
    
    /**
     * Computes the UNBIASED sample variance (estimated variance) of the values
     * TODO: implement a more efficient algorithm:
     * http://en.wikipedia.org/wiki/Algorithms_for_calculating_variance
     * @return float
     */ 
   public function variance() {
        $n = count($this->value);
        $accumulator = 0.0;
        for ($i=0; $i < $n; $i++) {
            $accumulator += $this->value[$i]* $this->value[$i];
        }
        
        return $accumulator / ($n-1) - ($n/($n-1)) * $this->mean() * $this->mean();
    }
    
    /**
     * Compute the sample standard deviation (estimated standard deviation) of the values
     * @return float
     */
    public function standardDeviation() {
        return sqrt($this->variance());
    }
    
    /**
     * 
     * @return Vector with mean = 0 and stdandard deviation = 1
     */
    public function standardNormal() {
        return $this->sub($this->mean())->div($this->standardDeviation());
    }
    
    
    public function sort() {
        $class = get_called_class();
        $value = $this->value; // Makes a copy of $this->value
        sort($value);
        return new $class($value);
    }
    
    /**
     * Compute the Empirical Cumulative Distribution Function for $value
     * Also called the Kaplan-Meier estimate
     * ecdf($value) = (number of values <= $value) / number of values
     * @param float $value
     * @return float
     */
    public function ecdf($value) {
        $n = $this->length();
        $count = 0;
        for ($i=0; $i<$n; $i++) {
            if ($this->value[$i] <= $value) {
                $count++;
            }
        }
        return $count / $n;
    }
    
    /**
     * Tests if the values of the vector follow a normal law, using Anderson - Darling test
     * http://www.itl.nist.gov/div898/handbook/eda/section3/eda35e.htm
     * http://en.wikipedia.org/wiki/Anderson%E2%80%93Darling_test
     * With significance level (alpha) = 1%
     * Procedure is valid for sample size at least 8 values
     * 
     * @return boolean true is values follow a normal law
     */
    public function adtest() {
        $critical = 1.092; // Corresponds to alpha = 0.01
        $nd = new \webd\stats\NormalDistribution();
        $sorted = $this->sort();
        $n = $this->length();
        $A2 = -$n;
        for ($i = 1; $i <= $n; $i++) {
            $A2 += -(2 * $i - 1) / $n * ( log($nd->cumulativeProbability($sorted->value[$i - 1])) + log(1 - $nd->cumulativeProbability($sorted->value[$n - $i])) );
        }
        $A2_star = $A2 * (1 + 4 / $n - 25 / ($n * $n));
        if ($A2_star > $critical) {
            return FALSE;
        } else {
            // Data seems to follow a normal law
            return TRUE;
        }
    }
    
    public function isGaussian() {
        return $this->adtest();
    }
    
    /**
     * Returns normalized vector (vector with same direction but length = 1)
     * @return Vector 
     */
    public function normalize() {
        return $this->div($this->norm());
    }
    
    /**
     * 
     * @return float Eucledian norm of the vector
     */
    public function norm() {
        $accumulator = 0;
        for ($i=0; $i<count($this->value); $i++) {
            $accumulator += ($this->value[$i] * $this->value[$i]);
        }
        return sqrt($accumulator);
    }
    
    public function length() {
        return count($this->value);
    }
    
    /**
     * Compute the scalar projection of vector $a on this vector
     * aka scalar resolute or scalar component of a in the direction of this vector
     * http://en.wikipedia.org/wiki/Scalar_projection
     * @param Vector $a
     */
    public function scalarProject(Vector $a) {
        return $this->dotProduct($a) / $this->norm();
    }
}
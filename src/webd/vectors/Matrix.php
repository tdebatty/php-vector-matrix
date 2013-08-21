<?php

namespace webd\vectors;

class Matrix
{
    public $value = array();
    
    private $_eigenvalue = null;
    private $_eigenvector = null;
    
    public function __construct($value) {
        $this->value = $value;
    }
    
    /** 
     * Compute the principal eigenvalue of the Matrix using power iteration algorithm
     * (also known as Von Mises iteration algorithm)
     * http://en.wikipedia.org/wiki/Power_iteration
     * 
     * @return float
     */
    public function eigenvalue() {
        if (is_null($this->_eigenvalue)) {
            $this->_eigen();
        }
        
        return $this->_eigenvalue;
    }
    
    /** 
     * Compute the eigenvector of the Matrix using power iteration algorithm
     * (also known as Von Mises iteration algorithm)
     * http://en.wikipedia.org/wiki/Power_iteration
     * 
     * @return Vector
     */
    public function eigenvector() {
        if(is_null($this->_eigenvector)) {
            $this->_eigen();
        }
        
        return $this->_eigenvector;
    }
    
    private function _eigen() {
        $iterations = 20;
        $dim = count($this->value[0]);
        for ($i=0; $i<$dim; $i++) {
            $value[] = 1;
        }
        $b = new Vector($value);
        var_dump($b);
        
        for ($i = 0; $i<$iterations; $i++) {
            $b = $this->dotProduct($b); // Dot product Matrix . Vector
            $length = $b->norm();
            $b = $b->div($length);
        }
        
        $this->_eigenvalue = $length;
        $this->_eigenvector = $b;
        
    }
    
    public function cov() {
        
    }
    
    /**
     * Computes the Dot product
     * TODO: implement a more efficient algorithm!
     * http://en.wikipedia.org/wiki/Matrix_multiplication
     * 
     * @param \webd\vectors\Vector $other
     * @return \webd\vectors\Vector
     */
    public function dotProduct($other) {
        if (get_class($other) == 'webd\vectors\Vector') {
            /* @var $other Vector */
            $A = $this->value;
            $b = $other->getValue();
            
            $m = count($A);
            $n = count($A[0]);
            
            for ($i = 0; $i < $m; $i++) {
                $tmp[$i] = 0;
                
                for ($j=0; $j < $n; $j++) {
                    $tmp[$i] += $A[$i][$j] * $b[$j];
                }
            }
            
            return new Vector($tmp);
        }
    }
    
    public function __concat($other) {
        return $this->dotProduct($other);
    }
    
    
}
?>

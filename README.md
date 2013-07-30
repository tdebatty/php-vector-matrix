php-vector-matrix
=================

A PHP library for vectors and matrices algebra


Usage
-----

```php
<?php

use \webd\vectors\Vector;

$v1 = new Vector(1,2,3);
$v2 = new Vector(4,5,10);

$v3 = $v1->add($v2);
$v3->display();

// If you have PECL extension operator installed,
// this returns a Vector with value (5,7,9)
$v4 = $v1 - $v2;
var_dump($v4);

var_dump($v1 - 1);

var_dump($v1 . $v2); // Dot product

var_dump($v1 * $v2); // Cross product

$v2_normalized = $v2->normalize();
var_dump($v2_normalized);
var_dump($v2_normalized->length()); // Should be 1!


$v4 = new Vector(1, 2, 5, 4.2, 8, 5, 10.9, 1, 7, 4);
var_dump($v4->mean());
var_dump($v4->standardDeviation());

$v5 = $v4 - $v4->mean();
$v5 = $v5 / $v4->standardDeviation();

var_dump($v5);
var_dump($v5->mean()); // Should be close to 0
var_dump($v5->standardDeviation()); // Should be close to 1
```
php-vector-matrix
=================

A PHP library for vectors and matrices algebra


Usage
-----

```php
<?php

use \webd\vectors\Vector;

$v1 = new Vector(1,2,3);
$v2 = new Vector(4,5,6);

$v3 = $v1->add($v2);
$v3->display();

// If you have PECL extension operator installed,
// this returns a Vector with value (5,7,9)
$v4 = $v1 + $v2;
var_dump($v4);
```
<?php

namespace App\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase {


public function testProduct():void 
{

    $product = New Product;
    $product->setDescription('description');
    $product->setName('name');
    $product->setPrice(15);

   $this->assertEquals($product->setPrice(15),$product->setPrice(15));
   $this->assertEquals($product->setDescription('description'),$product->setDescription('description'));
   $this->assertEquals($product->setName('name'),$product->setName('name'));
}
}

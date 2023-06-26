<?php

namespace Sales;
use Sales\Product;

interface IProduct {
    public function fromData(): Product;
}
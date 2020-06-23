<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductValue extends Model
{
    /**
     * @var string
     */
    protected $table = 'attribute_product_attribute_value';

    /**
     * @var array
     */
    protected $fillable = [
        'attribute_value_id',
        'attribute_product_id',
        'price',
        'sku',
        'image'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeProduct extends Model
{
    protected $table = 'attribute_product';
    /**
     * @var array
     */
    protected $fillable = [
        'attribute_id',
        'product_id'
    ];

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * @return BelongsTo
     */
    public function attribute_product_attribute_values()
    {
        return $this->belongsTo(ProductValue::class, 'attribute_value_id','attribute_product_id','attribute_product_attribute_value');
    }
}

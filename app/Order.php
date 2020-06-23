<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = "orders";

    protected $fillable = [
        'number',
        'name',
        'email',
        'phone',
        'address',
        'total_price',
        'quantity',
        'currency',
        'attribute_name',
        'attribute_value'
    ];

    /**
     * @param $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    /**
     * @param $value
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

}

<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * Cart Model
 */
class Cart extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_carts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'variants' => [
            'Pixiu\Commerce\Models\ProductVariant',
            'table' => 'pixiu_com_carts_variants',
            'key' => 'cart_id',
            'otherKey' => 'variant_id',
            'pivot' => ['quantity']
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}

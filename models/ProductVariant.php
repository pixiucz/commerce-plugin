<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * ProductCombination Model
 */
class ProductVariant extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_commerce_product_variants';

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
    public $belongsTo = [
        'product' => ['Pixiu\Commerce\Models\Product'],
        'primary_picture' => ['System\Models\File']
    ];
    public $belongsToMany = [
        'attributes' => [
            'Pixiu\Commerce\Models\Attribute',
            'table' => 'pixiu_commerce_variant_attributes',
            'key' => 'variant_id',
            'otherKey' => 'attribute_id',
            'pivot' => ['group_id']
        ],
        'images' => [
            'System\Models\File',
            'table' => 'pixiu_commerce_variant_images',
            'key' => 'variant_id',
            'otherKey' => 'system_file_id'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
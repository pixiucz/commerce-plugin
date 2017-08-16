<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * Attribute Model
 */
class Attribute extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_attributes';

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
        'attributegroup' => [
            'Pixiu\Commerce\Models\AttributeGroup',
            'key' => 'attribute_group_id'
        ]
    ];
    public $belongsToMany = [
        'productvariant' => [
            'Pixiu\Commerce\Models\ProductVariant',
            'table' => 'pixiu_com_variant_attributes',
            'key' => 'attribute_id',
            'otherKey' => 'variant_id',
            'pivot' => ['group_id']
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
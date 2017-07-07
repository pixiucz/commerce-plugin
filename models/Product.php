<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\{Tax, ProductVariant, Attribute, AttributeGroup};

/**
 * Product Model
 */
class Product extends Model
{
    //use \October\Rain\Database\Traits\NestedTree;

    public function getTaxIdOptions()
    {
        return Tax::all()->pluck('name', 'id')->toArray();
    }

    public function getDecompositeOnOptions()
    {
        $this->load(['productvariants.attributes.attributegroup' => function ($q) use (&$attributeGroups) {
            $attributeGroups = $q->select('name', 'id')->get()->toArray();
        }]);

        $options = [];

        if (is_array($attributeGroups)){
            foreach ($attributeGroups as $attributeGroup){
                $options[$attributeGroup['id']] = $attributeGroup['name'];
            }
        }
        return $options;
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_commerce_products';

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
    public $hasMany = [
        'productvariants' => ['Pixiu\Commerce\Models\ProductVariant']
    ];
    public $belongsTo = [
        'brand' => ['Pixiu\Commerce\Models\Brand'],
        'tax' => ['Pixiu\Commerce\Models\Tax'],
        'category' => ['Pixiu\Commerce\Models\Category']
    ];
    public $belongsToMany = [
        'decomposite_on' => [
            'Pixiu\Commerce\Models\AttributeGroup',
            'table' => 'pixiu_commerce_products_groups',
            'key' => 'product_id',
            'otherKey' => 'attribute_group_id'
        ],
        'categories' => [
            'Pixiu\Commerce\Models\Category',
            'table' => 'pixiu_commerce_category_products',
            'key' => 'product_id',
            'otherKey' => 'category_id'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => 'System\Models\File'
    ];

}
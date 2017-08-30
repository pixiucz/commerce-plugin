<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Models\{Tax, ProductVariant, Attribute, AttributeGroup};

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;
    public $rules = [];
    public $customMessages = [];
    public $attributeNames = [];

    public function __construct()
    {
        parent::__construct();
        $this->rules = [
            'name' => 'required',
            'retail_price' => 'required'
        ];
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_products';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['specifications'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'productvariants' => ['Pixiu\Commerce\Models\ProductVariant']
    ];
    public $belongsTo = [
        'brand' => ['Pixiu\Commerce\Models\Brand'],
        'category' => ['Pixiu\Commerce\Models\Category'],
        'tax' => ['Pixiu\Commerce\Models\Tax']
    ];
    public $belongsToMany = [
        'decomposite_on' => [
            'Pixiu\Commerce\Models\AttributeGroup',
            'table' => 'pixiu_com_products_groups',
            'key' => 'product_id',
            'otherKey' => 'attribute_group_id'
        ],
        'categories' => [
            'Pixiu\Commerce\Models\Category',
            'table' => 'pixiu_com_category_products',
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

    public function getTaxRateOptions()
    {
        $taxes = [];
        Tax::get()->each(function($item, $key) use (&$taxes) {
            $taxes[$item->rate] = $item->qualifiedName;
        });
        return $taxes;
    }

    public function filterFields($fields, $context = null)
    {
        if ($context === 'update') {
            if ($this->has_variants) {
                $fields->{'_in_stock@update'}->hidden = true;
                $fields->{'_change_stock@update'}->hidden = true;
                $fields->{'_ean@update'}->hidden = true;
                $fields->{'_slug@update'}->hidden = true;
            } else {
                $fields->{'_form_widget@update'}->hidden = true;
                $fields->{'_in_stock@update'}->value = $this->productvariants->first()->in_stock;
                $fields->{'_ean@update'}->value = $this->productvariants->first()->ean;
                $fields->{'_slug@update'}->hidden = true;
            }
        }
    }

}
<?php namespace Pixiu\Commerce\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use Pixiu\Commerce\Traits\LocalizedValidation;

/**
 * Category Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\NestedTree;
    use Validation;
    use LocalizedValidation;

    public $rules = [];

    public function __construct()
    {
        parent::__construct();

        $this->localizeValidation();
        $this->rules = [
            'name' => 'required',
            'slug' => 'required|unique:pixiu_com_categories'
        ];
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_categories';

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
        'products' => [
            'Pixiu\Commerce\Models\Product',
            'table' => 'pixiu_com_category_products',
            'key' => 'category_id',
            'otherKey' => 'product_id'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value);
    }

}
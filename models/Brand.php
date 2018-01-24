<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Traits\LocalizedValidation;

/**
 * Brand Model
 */
class Brand extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use LocalizedValidation;

    public $rules = [];

    public function __construct()
    {
        parent::__construct();

        $this->localizeValidation();
        $this->rules = [
            'name' => 'required'
        ];
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_brands';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'products' => ['Pixiu\Commerce\Models\Product']
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'logo' => 'System\Models\File'
    ];
    public $attachMany = [];
}

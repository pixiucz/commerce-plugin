<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Classes\Utils;

/**
 * Address Model
 */
class Address extends Model
{
    use \October\Rain\Database\Traits\Validation;
    public $rules = [];
    public $customMessages = [];
    public $attributeNames = [];

    public function __construct()
    {
        parent::__construct();
        $this->rules = [
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required'
        ];
    }

    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ', ' . $this->zip . ' ' . $this->country;
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_commerce_addresses';

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
        'user' => 'RainLab\User\Models\User'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getCountryOptions()
    {
        return Utils::getCountriesEN();
    }
}

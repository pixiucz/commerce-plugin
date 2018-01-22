<?php namespace Pixiu\Commerce\Models;

use Model;
use Pixiu\Commerce\Classes\Utils;
use Pixiu\Commerce\Traits\LocalizedValidation;

/**
 * Address Model
 */
class Address extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use LocalizedValidation;

    public $rules = [];

    public function __construct()
    {
        parent::__construct();

        $this->localizeValidation();
        $this->rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'telephone' => 'required'
        ];
    }

    public function getFullAddressAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ' | ' . $this->address . ', ' . $this->city . ', ' . $this->zip . ' ' . $this->country;
    }

    /**
     * @var string The database table used by the model.
     */
    public $table = 'pixiu_com_addresses';

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['first_name', 'last_name', 'address', 'zip', 'city', 'country', 'dic', 'ic', 'telephone'];

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
